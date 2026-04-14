<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTypePermission extends Model
{
    protected $fillable = ['user_type', 'permission', 'enabled'];

    protected function casts(): array
    {
        return ['enabled' => 'boolean'];
    }

    // ── All defined permissions per user type ─────────────────────────────────

    /** @return array<string, array<string, string>> */
    public static function definitions(): array
    {
        return [
            'customer' => [
                'submit_repair'     => 'Submit Repair Requests',
                'view_repairs'      => 'View Repair History',
                'track_shipments'   => 'Track Shipment Status',
                'view_invoices'     => 'View Invoices',
                'download_invoices' => 'Download Invoices',
            ],
            'employee' => [
                'technician_board'      => 'Access Technician Board',
                'update_repair_status'  => 'Update Repair Status',
                'add_repair_notes'      => 'Add Notes to Repairs',
                'view_customer_info'    => 'View Customer Information',
                'view_all_repairs'      => 'View All Repair Tickets',
            ],
            'partner' => [
                'submit_shipments'      => 'Submit Shipments',
                'view_own_shipments'    => 'View Own Shipments',
                'track_shipments'       => 'Track Shipment Status',
                'view_rates'            => 'View Shipping Rates',
            ],
        ];
    }

    // ── Runtime permission check (request-level cache) ─────────────────────────

    /** @var array<string, array<string, bool>>|null */
    private static ?array $cache = null;

    public static function check(string $userType, string $permission): bool
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (static::all() as $row) {
                self::$cache[$row->user_type][$row->permission] = (bool) $row->enabled;
            }
        }

        // Default: allow if no row exists yet (not yet configured)
        return self::$cache[$userType][$permission] ?? true;
    }

    /** Clear the in-memory cache after saving. */
    public static function clearCache(): void
    {
        self::$cache = null;
    }

    // ── Seed all permissions for a type (all enabled by default) ──────────────

    public static function seedDefaults(): void
    {
        foreach (static::definitions() as $type => $permissions) {
            foreach (array_keys($permissions) as $permission) {
                static::firstOrCreate(
                    ['user_type' => $type, 'permission' => $permission],
                    ['enabled' => true]
                );
            }
        }
    }
}
