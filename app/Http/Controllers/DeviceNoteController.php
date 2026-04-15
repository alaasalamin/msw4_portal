<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeviceNoteController extends Controller
{
    /**
     * Store a new note on a device.
     *
     * Accessible by:
     *   - admin  (auth:admin)     → can set is_public freely
     *   - employee (auth:web, type=employee) → can set is_public freely
     *   - customer / partner (auth:web)      → is_public forced to true
     */
    public function store(Request $request, Device $device): RedirectResponse
    {
        $request->validate([
            'content'   => ['required', 'string', 'max:5000'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        $note = DeviceNote::forCurrentUser([
            'device_id' => $device->id,
            'content'   => $request->input('content'),
            'type'      => 'text',
        ]);

        // Customers and partners can only post public notes
        $authorRole = $note->author_role;
        if (in_array($authorRole, ['customer', 'partner'])) {
            $note->is_public = true;
        } else {
            $note->is_public = (bool) $request->input('is_public', false);
        }

        $note->save();

        return back()->with('success', 'Notiz gespeichert.');
    }

    /**
     * Update the visibility or content of a note.
     * Only the author or an admin may edit.
     */
    public function update(Request $request, DeviceNote $note): RedirectResponse
    {
        $this->authorizeNoteAccess($note);

        $request->validate([
            'content'   => ['required', 'string', 'max:5000'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        $data = ['content' => $request->input('content')];

        // Only admins and employees may change visibility
        if (in_array($note->author_role, ['admin', 'employee'])) {
            $data['is_public'] = (bool) $request->input('is_public', $note->is_public);
        }

        $note->update($data);

        return back()->with('success', 'Notiz aktualisiert.');
    }

    /**
     * Soft-delete a note.
     * Only the author or an admin may delete.
     */
    public function destroy(DeviceNote $note): RedirectResponse
    {
        $this->authorizeNoteAccess($note);

        $note->delete();

        return back()->with('success', 'Notiz gelöscht.');
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function authorizeNoteAccess(DeviceNote $note): void
    {
        // Admins can do anything
        if (auth('admin')->check()) {
            return;
        }

        $user = auth()->user();

        // Must be the author
        if (
            $note->authorable_type === \App\Models\User::class &&
            $note->authorable_id   === $user?->id
        ) {
            return;
        }

        abort(403, 'Keine Berechtigung.');
    }
}
