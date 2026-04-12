<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $technician = User::firstOrCreate(
            ['email' => 'tech@moonrepair.de'],
            [
                'name'     => 'Max Techniker',
                'password' => Hash::make('password'),
                'role'     => 'technician',
            ]
        );

        // Wipe existing test devices for this technician to avoid duplicates
        Device::where('technician_id', $technician->id)->delete();

        $devices = [
            // ── URGENT ───────────────────────────────────────────────────────
            [
                'brand' => 'Apple', 'model' => 'iPhone 14 Pro',
                'serial_number' => 'F2LXQ1ABCD', 'color' => 'Space Black',
                'customer_name' => 'Thomas Müller', 'customer_phone' => '+49 176 1234567',
                'issue_description' => 'Cracked screen, touch unresponsive in bottom half',
                'status' => 'in_repair', 'priority' => 'urgent',
                'estimated_cost' => 189.00, 'received_at' => now()->subDays(8),
            ],
            [
                'brand' => 'Apple', 'model' => 'MacBook Pro 16"',
                'serial_number' => 'C02ZG2BCDEF', 'color' => 'Space Gray',
                'customer_name' => 'Stefan Richter', 'customer_phone' => '+49 170 9988776',
                'issue_description' => 'Does not boot — black screen after macOS update',
                'status' => 'diagnosing', 'priority' => 'urgent',
                'estimated_cost' => null, 'received_at' => now()->subDays(9),
                'internal_notes' => 'Customer needs laptop for work urgently!',
            ],
            [
                'brand' => 'Samsung', 'model' => 'Galaxy S24 Ultra',
                'serial_number' => 'R9PNA1234X', 'color' => 'Titanium Gray',
                'customer_name' => 'Julia Braun', 'customer_phone' => '+49 160 5554433',
                'issue_description' => 'Phone dropped — back glass shattered, camera lens cracked',
                'status' => 'received', 'priority' => 'urgent',
                'estimated_cost' => null, 'received_at' => now()->subDays(0),
            ],

            // ── HIGH ─────────────────────────────────────────────────────────
            [
                'brand' => 'Samsung', 'model' => 'Galaxy S23 Ultra',
                'serial_number' => 'R5CTA0123B', 'color' => 'Phantom Black',
                'customer_name' => 'Anna Schmidt', 'customer_phone' => '+49 151 9876543',
                'issue_description' => 'Battery drains in 2 hours, device gets very hot',
                'status' => 'diagnosing', 'priority' => 'high',
                'estimated_cost' => null, 'received_at' => now()->subDays(4),
            ],
            [
                'brand' => 'Apple', 'model' => 'MacBook Pro 14"',
                'serial_number' => 'C02XG1ABCDE', 'color' => 'Silver',
                'customer_name' => 'Klaus Weber', 'customer_phone' => '+49 170 5556789',
                'issue_description' => 'Keyboard not working — several keys stuck after liquid spill',
                'status' => 'waiting_parts', 'priority' => 'high',
                'estimated_cost' => 320.00, 'received_at' => now()->subDays(5),
                'internal_notes' => 'Ordered replacement keyboard, ETA 2 days',
            ],
            [
                'brand' => 'Google', 'model' => 'Pixel 8 Pro',
                'serial_number' => 'GX9KP7MNOP', 'color' => 'Obsidian',
                'customer_name' => 'Lena Fischer', 'customer_phone' => '+49 176 3332211',
                'issue_description' => 'Microphone not working — caller cannot hear anything',
                'status' => 'in_repair', 'priority' => 'high',
                'estimated_cost' => 110.00, 'received_at' => now()->subDays(3),
            ],
            [
                'brand' => 'Apple', 'model' => 'iPad Pro 12.9"',
                'serial_number' => 'DLXKP5QRST', 'color' => 'Silver',
                'customer_name' => 'Markus Schulz', 'customer_phone' => '+49 152 6667788',
                'issue_description' => 'Screen flickering constantly, touch response intermittent',
                'status' => 'waiting_approval', 'priority' => 'high',
                'estimated_cost' => 240.00, 'received_at' => now()->subDays(4),
                'internal_notes' => 'Sent quote via email. Customer seen it but no reply yet.',
            ],

            // ── NORMAL ───────────────────────────────────────────────────────
            [
                'brand' => 'Samsung', 'model' => 'Galaxy Tab S8',
                'serial_number' => 'R52N300456C', 'color' => 'Graphite',
                'customer_name' => 'Laura Becker', 'customer_phone' => '+49 163 1112233',
                'issue_description' => 'Charging port broken, device only charges wirelessly',
                'status' => 'received', 'priority' => 'normal',
                'estimated_cost' => null, 'received_at' => now()->subDays(1),
            ],
            [
                'brand' => 'Apple', 'model' => 'iPhone 13',
                'serial_number' => 'DNPXQ9EFGH', 'color' => 'Blue',
                'customer_name' => 'Felix Wagner', 'customer_phone' => '+49 178 4445566',
                'issue_description' => 'Camera not focusing, front camera completely black',
                'status' => 'waiting_approval', 'priority' => 'normal',
                'estimated_cost' => 149.00, 'received_at' => now()->subDays(3),
                'internal_notes' => 'Sent quote to customer, waiting for approval',
            ],
            [
                'brand' => 'Sony', 'model' => 'Xperia 1 V',
                'serial_number' => 'SX1V9UVWXY', 'color' => 'Black',
                'customer_name' => 'Nina Vogel', 'customer_phone' => '+49 159 2223344',
                'issue_description' => 'SIM card not detected after dropping the phone',
                'status' => 'diagnosing', 'priority' => 'normal',
                'estimated_cost' => null, 'received_at' => now()->subDays(2),
            ],
            [
                'brand' => 'OnePlus', 'model' => '12 Pro',
                'serial_number' => 'OP12P3ZA1B', 'color' => 'Flowy Emerald',
                'customer_name' => 'Patrick Neumann', 'customer_phone' => '+49 157 8889900',
                'issue_description' => 'Speakers crackling at medium volume, no output at max',
                'status' => 'in_repair', 'priority' => 'normal',
                'estimated_cost' => 75.00, 'received_at' => now()->subDays(2),
            ],
            [
                'brand' => 'Apple', 'model' => 'iPhone 15',
                'serial_number' => 'F8MNQ2CDEF', 'color' => 'Pink',
                'customer_name' => 'Sarah König', 'customer_phone' => '+49 174 4443322',
                'issue_description' => 'Battery swollen, back cover slightly lifted',
                'status' => 'waiting_parts', 'priority' => 'normal',
                'estimated_cost' => 99.00, 'received_at' => now()->subDays(3),
                'internal_notes' => 'Battery ordered from supplier. Arrives tomorrow.',
            ],

            // ── LOW ──────────────────────────────────────────────────────────
            [
                'brand' => 'Huawei', 'model' => 'P40 Pro',
                'serial_number' => 'V39HW7IJKL', 'color' => 'Silver Frost',
                'customer_name' => 'Maria Hoffmann', 'customer_phone' => '+49 152 7778899',
                'issue_description' => 'Phone fell in water, not turning on',
                'status' => 'ready', 'priority' => 'low',
                'estimated_cost' => 95.00, 'final_cost' => 95.00,
                'received_at' => now()->subDays(6),
                'internal_notes' => 'Dried out and replaced battery. Ready for pickup.',
            ],
            [
                'brand' => 'Motorola', 'model' => 'Edge 40 Pro',
                'serial_number' => 'MOT40P8GHIJ', 'color' => 'Interstellar Black',
                'customer_name' => 'Hans Zimmermann', 'customer_phone' => '+49 163 5556677',
                'issue_description' => 'Screen has dead pixels in top-left corner',
                'status' => 'ready', 'priority' => 'low',
                'estimated_cost' => 130.00, 'final_cost' => 120.00,
                'received_at' => now()->subDays(7),
                'internal_notes' => 'Screen replaced. Awaiting customer pickup.',
            ],
            [
                'brand' => 'Xiaomi', 'model' => '14 Ultra',
                'serial_number' => 'MI14U6KLMN', 'color' => 'White',
                'customer_name' => 'Petra Lange', 'customer_phone' => '+49 176 1110099',
                'issue_description' => 'Back glass cracked, cosmetic damage only',
                'status' => 'received', 'priority' => 'low',
                'estimated_cost' => null, 'received_at' => now()->subHours(3),
            ],
        ];

        foreach ($devices as $data) {
            Device::create(array_merge($data, ['technician_id' => $technician->id]));
        }
    }
}
