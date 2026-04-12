<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeviceController extends Controller
{
    public function technicianBoard()
    {
        $user = auth()->user();

        $devices = Device::with('coordinator')
            ->where('technician_id', $user->id)
            ->whereNotIn('status', ['completed', 'returned'])
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->orderBy('received_at')
            ->get()
            ->map(fn($d) => [
                'id'             => $d->id,
                'ticket_number'  => $d->ticket_number,
                'brand'          => $d->brand,
                'model'          => $d->model,
                'serial_number'  => $d->serial_number,
                'color'          => $d->color,
                'customer_name'  => $d->customer_name,
                'customer_phone' => $d->customer_phone,
                'issue_description' => $d->issue_description,
                'internal_notes' => $d->internal_notes,
                'status'         => $d->status,
                'priority'       => $d->priority,
                'estimated_cost' => $d->estimated_cost,
                'received_at'    => $d->received_at->toISOString(),
                'days_in_shop'   => $d->days_in_shop,
                'aging_level'    => $d->aging_level,
                'estimated_completion' => $d->estimated_completion?->toISOString(),
            ]);

        return Inertia::render('Technician/Board', [
            'devices' => $devices,
        ]);
    }

    public function updateStatus(Request $request, Device $device)
    {
        $request->validate([
            'status' => 'required|in:received,diagnosing,waiting_approval,in_repair,waiting_parts,ready,completed,returned',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'completed') {
            $data['completed_at'] = now();
        }

        $device->update($data);

        return back();
    }

    public function updateNotes(Request $request, Device $device)
    {
        $request->validate(['internal_notes' => 'nullable|string|max:2000']);
        $device->update(['internal_notes' => $request->internal_notes]);
        return back();
    }
}
