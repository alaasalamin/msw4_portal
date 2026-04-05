<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Services\DhlService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShipmentController extends Controller
{
    public function __construct(private DhlService $dhl) {}

    public function index()
    {
        $shipments = auth()->user()
            ->shipments()
            ->latest()
            ->get();

        return Inertia::render('Shipments/Index', [
            'shipments' => $shipments,
        ]);
    }

    public function create()
    {
        return Inertia::render('Shipments/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'                   => 'required|in:domestic,international',
            'sender_name'            => 'required|string|max:50',
            'sender_company'         => 'nullable|string|max:50',
            'sender_street'          => 'required|string|max:50',
            'sender_house_number'    => 'required|string|max:10',
            'sender_postal_code'     => 'required|string|max:10',
            'sender_city'            => 'required|string|max:50',
            'sender_country'         => 'required|string|size:3',
            'sender_email'           => 'nullable|email',
            'sender_phone'           => 'nullable|string|max:20',
            'recipient_name'         => 'required|string|max:50',
            'recipient_company'      => 'nullable|string|max:50',
            'recipient_street'       => 'required|string|max:50',
            'recipient_house_number' => 'required|string|max:10',
            'recipient_postal_code'  => 'required|string|max:10',
            'recipient_city'         => 'required|string|max:50',
            'recipient_country'      => 'required|string|size:3',
            'recipient_email'        => 'nullable|email',
            'recipient_phone'        => 'nullable|string|max:20',
            'weight_kg'              => 'required|numeric|min:0.1|max:31.5',
            'reference'              => 'nullable|string|max:35',
        ]);

        $dhlResult = $this->dhl->createShipment($validated);

        $shipment = auth()->user()->shipments()->create([
            ...$validated,
            'tracking_number' => $dhlResult['tracking_number'],
            'label_url'       => $dhlResult['label_url'],
            'dhl_response'    => $dhlResult['dhl_response'],
            'status'          => 'label_created',
        ]);

        return redirect()->route('shipments.show', $shipment)
            ->with('success', 'Shipment created successfully.');
    }

    public function show(Shipment $shipment)
    {
        $this->authorize('view', $shipment);

        return Inertia::render('Shipments/Show', [
            'shipment' => $shipment,
        ]);
    }

    public function track(Shipment $shipment)
    {
        $this->authorize('view', $shipment);

        $tracking = $this->dhl->trackShipment($shipment->tracking_number);

        if (! empty($tracking[0]['status']['description'])) {
            $shipment->update(['status' => $tracking[0]['status']['description']]);
        }

        return Inertia::render('Shipments/Show', [
            'shipment' => $shipment->fresh(),
            'tracking' => $tracking,
        ]);
    }
}
