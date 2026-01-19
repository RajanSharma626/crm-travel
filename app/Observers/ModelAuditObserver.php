<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class ModelAuditObserver
{
    protected function makeLog($model, string $event)
    {
        try {
            $userId = Auth::check() ? Auth::id() : null;
        } catch (\Throwable $e) {
            $userId = null;
        }

        $old = null;
        $new = null;

        if ($event === 'created') {
            $new = $model->getAttributes();
        } elseif ($event === 'updated') {
            $changes = $model->getChanges();
            $oldRaw = $model->getOriginal();
            $old = Arr::only($oldRaw, array_keys($changes));
            $new = Arr::only($model->getAttributes(), array_keys($changes));
        } elseif ($event === 'deleted') {
            $old = $model->getOriginal();
        }

        $request = null;
        try {
            $request = request();
        } catch (\Throwable $e) {
            $request = null;
        }

        AuditLog::create([
            'user_id' => $userId,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'event' => $event,
            'old_values' => $old,
            'new_values' => $new,
            'url' => $request ? $request->fullUrl() : null,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
        ]);
    }

    public function created($model)
    {
        $this->makeLog($model, 'created');
    }

    public function updated($model)
    {
        $this->makeLog($model, 'updated');
    }

    public function deleted($model)
    {
        $this->makeLog($model, 'deleted');
    }
}

