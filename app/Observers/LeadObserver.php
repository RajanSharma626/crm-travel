<?php

namespace App\Observers;

use App\Models\Lead;
use App\Models\LeadHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LeadObserver
{
    /**
     * Handle the Lead "creating" event.
     */
    public function creating(Lead $lead)
    {
        $max = DB::table('leads')->max('tsq_number');
        $next = $max ? $max + 1 : 1600;
        $lead->tsq_number = $next;
        $lead->tsq = 'TSQ' . $next;
    }

    /**
     * Handle the Lead "created" event.
     */
    public function created(Lead $lead)
    {
        if (Auth::check()) {
            LeadHistory::create([
                'lead_id' => $lead->id,
                'from_status' => null,
                'to_status' => $lead->status ?? 'new',
                'changed_by' => Auth::id(),
                'note' => 'Lead created',
            ]);
        }
    }

    /**
     * Handle the Lead "updated" event.
     */
    public function updated(Lead $lead)
    {
        // Track status changes
        if ($lead->isDirty('status') && Auth::check()) {
            LeadHistory::create([
                'lead_id' => $lead->id,
                'from_status' => $lead->getOriginal('status'),
                'to_status' => $lead->status,
                'changed_by' => Auth::id(),
                'note' => 'Status changed',
            ]);
        }
    }
}
