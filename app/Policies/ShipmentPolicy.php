<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Shipment;
use Illuminate\Contracts\Auth\Authenticatable;

class ShipmentPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return true;
    }

    public function view(Authenticatable $user, Shipment $shipment): bool
    {
        if ($user instanceof Admin) {
            return true;
        }

        return $user->id === $shipment->user_id;
    }

    public function create(Authenticatable $user): bool
    {
        return true;
    }

    public function update(Authenticatable $user, Shipment $shipment): bool
    {
        if ($user instanceof Admin) {
            return true;
        }

        return $user->id === $shipment->user_id;
    }

    public function delete(Authenticatable $user, Shipment $shipment): bool
    {
        if ($user instanceof Admin) {
            return true;
        }

        return $user->id === $shipment->user_id;
    }
}
