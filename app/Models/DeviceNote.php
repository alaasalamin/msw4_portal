<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'device_id',
        'author_name',
        'author_role',
        'authorable_type',
        'authorable_id',
        'content',
        'type',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * The actual author — resolves to Admin or User (employee/customer/partner).
     */
    public function authorable(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** Only notes visible to customers and partners (public feed). */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /** All internal + public notes (for employees/admins). */
    public function scopeInternal($query)
    {
        return $query;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Build a DeviceNote instance pre-filled from the currently authenticated actor.
     * Supports auth:admin (Admin model) and auth:web (User model — any type).
     */
    public static function forCurrentUser(array $attributes = []): static
    {
        $note = new static($attributes);

        if ($admin = auth('admin')->user()) {
            $note->author_name     = $admin->name;
            $note->author_role     = 'admin';
            $note->authorable_type = Admin::class;
            $note->authorable_id   = $admin->id;
        } elseif ($user = auth()->user()) {
            $note->author_name     = $user->name;
            $note->author_role     = $user->type; // employee | customer | partner
            $note->authorable_type = User::class;
            $note->authorable_id   = $user->id;
        }

        return $note;
    }
}
