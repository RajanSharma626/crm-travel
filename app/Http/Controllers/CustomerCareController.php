<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Destination;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerCareController extends LeadController
{
    /**
     * Override index to show only unassigned leads
     */
    public function index(Request $request)
    {
        $statuses = [
            'new' => 'New',
            'contacted' => 'Contacted',
            'follow_up' => 'Follow Up',
            'priority' => 'Priority',
            'booked' => 'Booked',
            'closed' => 'Closed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
        ];

        $filters = [
            'status' => $request->input('status'),
            'search' => $request->input('search'),
        ];

        // Show only unassigned leads (where assigned_user_id is null)
        $leadsQuery = Lead::with(['service', 'destination', 'assignedUser', 'remarks' => function ($q) {
            $q->orderBy('created_at', 'desc')->limit(1);
        }])
            ->whereNull('assigned_user_id')
            ->orderBy('created_at', 'desc');

        if (!empty($filters['status'])) {
            $leadsQuery->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $searchTerm = trim($filters['search']);
            $likeTerm = '%' . $searchTerm . '%';

            $leadsQuery->where(function ($query) use ($likeTerm) {
                $query->where('customer_name', 'like', $likeTerm)
                    ->orWhere('first_name', 'like', $likeTerm)
                    ->orWhere('last_name', 'like', $likeTerm)
                    ->orWhere('phone', 'like', $likeTerm)
                    ->orWhere('primary_phone', 'like', $likeTerm)
                    ->orWhere('secondary_phone', 'like', $likeTerm)
                    ->orWhere('other_phone', 'like', $likeTerm)
                    ->orWhere('tsq', 'like', $likeTerm)
                    ->orWhere('tsq_number', 'like', $likeTerm);
            });
        }

        $leads = $leadsQuery->paginate(25);
        $leads->appends($request->query());

        // Add latest remark to each lead
        $leads->getCollection()->transform(function ($lead) {
            $lead->latest_remark = $lead->remarks->first();
            return $lead;
        });

        $services = Service::orderBy('name')->get();
        $destinations = Destination::orderBy('name')->get();
        $employees = User::whereNotNull('user_id')->orderBy('name')->get();

        return view('leads.index', [
            'leads' => $leads,
            'statuses' => $statuses,
            'filters' => $filters,
            'services' => $services,
            'destinations' => $destinations,
            'employees' => $employees,
        ]);
    }

    /**
     * Override store to redirect back to customer care index
     */
    public function store(Request $request)
    {
        $response = parent::store($request);
        
        // If it's a redirect, change the target to customer care index
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return redirect()->route('customer-care.leads.index')->with('success', 'Lead created successfully in Customer Care!');
        }
        
        return $response;
    }

    /**
     * Override update to redirect back to customer care index
     */
    public function update(Request $request, Lead $lead)
    {
        $response = parent::update($request, $lead);
        
        // If it's a redirect, change the target to customer care index
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return redirect()->route('customer-care.leads.index')->with('success', 'Lead updated successfully in Customer Care!');
        }
        
        return $response;
    }

    /**
     * Override destroy to redirect back to customer care index
     */
    public function destroy(Lead $lead)
    {
        parent::destroy($lead);
        return redirect()->route('customer-care.leads.index')->with('success', 'Lead deleted successfully!');
    }
}
