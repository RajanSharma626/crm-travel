<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Payment;
use App\Models\CostComponent;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportController extends Controller
{
    public function index()
    {
        $stats = [
            'total_leads' => Lead::count(),
            'new_leads' => Lead::where('status', 'new')->count(),
            'booked_leads' => Lead::where('status', 'booked')->count(),
            'closed_leads' => Lead::where('status', 'closed')->count(),
            'total_revenue' => Payment::where('status', 'paid')->sum('amount'),
            'total_cost' => CostComponent::sum('amount'),
            'pending_deliveries' => Delivery::where('delivery_status', '!=', 'Delivered')->count(),
            'overdue_payments' => Payment::where('status', 'overdue')->count(),
        ];

        // Recent leads
        $recentLeads = Lead::with(['service', 'destination', 'assignedUser'])
            ->latest()
            ->limit(10)
            ->get();

        // Follow-up reminders — support either follow_up_at (new) or follow_up_date (legacy)

        $followUpColumn = null;
        if (Schema::hasColumn('lead_remarks', 'follow_up_at')) {
            $followUpColumn = 'follow_up_at';
        } elseif (Schema::hasColumn('lead_remarks', 'follow_up_date')) {
            $followUpColumn = 'follow_up_date';
        }

        if ($followUpColumn) {
            $followUps = \App\Models\LeadRemark::where($followUpColumn, '>=', today())
                ->where($followUpColumn, '<=', today()->addDays(7))
                ->with(['lead', 'user'])
                ->orderBy($followUpColumn)
                ->get();
        } else {
            // No follow-up columns available in DB
            $followUps = collect();
        }

        return view('reports.index', compact('stats', 'recentLeads', 'followUps'));
    }

    public function leads(Request $request)
    {
        $query = Lead::with(['service', 'destination', 'assignedUser']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $leads = $query->latest()->paginate(50);
        return view('reports.leads', compact('leads'));
    }

    public function revenue(Request $request)
    {
        $query = Payment::with('lead');

        if ($request->date_from) {
            $query->whereDate('paid_on', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('paid_on', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(50);
        
        $totals = [
            'total' => $payments->sum('amount'),
            'paid' => $payments->where('status', 'paid')->sum('amount'),
            'pending' => $payments->where('status', 'pending')->sum('amount'),
        ];

        return view('reports.revenue', compact('payments', 'totals'));
    }

    public function profit(Request $request)
    {
        $leads = Lead::with(['payments', 'costComponents', 'operation'])
            ->where('status', 'booked')
            ->get();

        $profitData = $leads->map(function ($lead) {
            $revenue = $lead->payments->where('status', 'paid')->sum('amount');
            $cost = $lead->operation?->nett_cost ?? $lead->costComponents->sum('amount');
            return [
                'lead' => $lead,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $revenue - $cost,
            ];
        });

        $totals = [
            'revenue' => $profitData->sum('revenue'),
            'cost' => $profitData->sum('cost'),
            'profit' => $profitData->sum('profit'),
        ];

        return view('reports.profit', compact('profitData', 'totals'));
    }

    public function exportLeads(Request $request)
    {
        $query = Lead::with(['service', 'destination', 'assignedUser']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $leads = $query->get();

        return Excel::download(new class($leads) implements FromCollection, WithHeadings {
            private $leads;

            public function __construct($leads)
            {
                $this->leads = $leads;
            }

            public function collection()
            {
                return $this->leads->map(function ($lead) {
                    return [
                        $lead->tsq,
                        $lead->customer_name,
                        $lead->primary_phone ?? $lead->phone,
                        $lead->email,
                        $lead->service?->name,
                        $lead->destination?->name,
                        $lead->status,
                        $lead->assignedUser?->name,
                        $lead->selling_price,
                        $lead->booked_value,
                        $lead->created_at->format('Y-m-d'),
                    ];
                });
            }

            public function headings(): array
            {
                return ['TSQ', 'Customer Name', 'Phone', 'Email', 'Service', 'Destination', 'Status', 'Assigned To', 'Selling Price', 'Booked Value', 'Created At'];
            }
        }, 'leads_' . date('Y-m-d') . '.xlsx');
    }
}
