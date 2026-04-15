<?php

namespace App\Http\Controllers;

use App\Models\DeviceNote;
use App\Models\DevicePhotoToken;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DevicePhotoUploadController extends Controller
{
    /** Show the mobile upload page for a token. */
    public function show(string $token): View|\Illuminate\Http\RedirectResponse
    {
        $record = DevicePhotoToken::with('device')
            ->where('token', $token)
            ->whereNull('used_at')
            ->first();

        if (! $record) {
            return view('upload.photo-expired');
        }

        return view('upload.photo', [
            'token'  => $token,
            'device' => $record->device,
        ]);
    }

    /** Handle the photo upload. */
    public function store(Request $request, string $token): View|\Illuminate\Http\RedirectResponse
    {
        $record = DevicePhotoToken::with('device')
            ->where('token', $token)
            ->whereNull('used_at')
            ->first();

        if (! $record) {
            return view('upload.photo-expired');
        }

        $request->validate([
            'photo' => ['required', 'image', 'max:15360'], // 15 MB
        ]);

        $path = $request->file('photo')->store('device-photos', 'public');

        // Save as a DeviceNote with type=image, is_public=false (internal)
        DeviceNote::create([
            'device_id'   => $record->device_id,
            'author_name' => 'Foto-Upload (Telefon)',
            'author_role' => 'employee',
            'content'     => $path,
            'type'        => 'image',
            'is_public'   => false,
        ]);

        // Consume token → auto-creates the next one
        $record->consume();

        return view('upload.photo-success', ['device' => $record->device]);
    }
}
