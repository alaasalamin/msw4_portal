<?php

namespace App\Console\Commands;

use App\Models\CustomerGroup;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ImportB2cCustomers extends Command
{
    protected $signature = 'import:b2c-customers {path : Path to b2c_customers.sql dump}';

    protected $description = 'Import legacy b2c_customers dump into the customers table';

    public function handle(): int
    {
        $path = $this->argument('path');
        if (! is_file($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $b2cGroupId = CustomerGroup::where('name', 'B2C')->value('id');
        if (! $b2cGroupId) {
            $this->error('B2C group not found. Run the customer_groups migration first.');
            return self::FAILURE;
        }

        // 1. Load the dump into a temporary table by stripping unrelated statements
        // and executing the CREATE + INSERTs through Laravel's PDO connection.
        Schema::dropIfExists('b2c_customers_import');

        $sql = file_get_contents($path);

        // Rename the original table so we don't clash with anything pre-existing.
        $sql = str_replace('`b2c_customers`', '`b2c_customers_import`', $sql);

        // Strip statements that pdo/mysqli wrappers tend to choke on.
        $sql = preg_replace('/^--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*!.*?\*\/;?/s', '', $sql);
        $sql = preg_replace('/^(START TRANSACTION|COMMIT|SET .*?);?$/m', '', $sql);

        $this->info('Loading dump into b2c_customers_import …');
        DB::unprepared($sql);

        $sourceCount = DB::table('b2c_customers_import')->count();
        $this->info("Source rows: {$sourceCount}");

        // 2. Transform → customers, skipping duplicate emails (in-DB and in-batch).
        // Pre-compute one bcrypt hash; these are CRM records, not login users.
        $placeholderPassword = Hash::make(Str::random(40));

        $inserted = 0;
        $skipped  = 0;
        $seenInBatch = [];

        DB::table('b2c_customers_import')->orderBy('id')->chunk(500, function ($rows) use (&$inserted, &$skipped, &$seenInBatch, $b2cGroupId, $placeholderPassword) {
            $now = now();
            $batch = [];
            foreach ($rows as $row) {
                $email = filled($row->email) ? strtolower(trim($row->email)) : "import-{$row->id}@bizo.local";

                if (isset($seenInBatch[$email])) {
                    $skipped++;
                    continue;
                }
                $seenInBatch[$email] = true;

                $batch[] = [
                    'name'                    => trim($row->name) ?: '—',
                    'company_name'            => filled($row->company) ? trim($row->company) : null,
                    'customer_group_id'       => $b2cGroupId,
                    'email'                   => $email,
                    'phone'                   => $this->clean($row->phone),
                    'address_street'          => $this->clean($row->street),
                    'address_building_number' => null,
                    'address_city'            => $this->clean($row->city),
                    'address_zip_code'        => $this->clean($row->zip),
                    'password'                => $placeholderPassword,
                    'created_at'              => $this->parseTs($row->created_at) ?? $now,
                    'updated_at'              => $this->parseTs($row->updated_at) ?? $now,
                ];
            }

            if ($batch) {
                // insertOrIgnore: silently skip rows that collide with existing emails
                // (e.g. re-runs after a partial import).
                $written = DB::table('customers')->insertOrIgnore($batch);
                $inserted += $written;
                $skipped  += count($batch) - $written;
            }
        });

        // 3. Clean up.
        Schema::dropIfExists('b2c_customers_import');

        $this->info("Inserted: {$inserted}");
        $this->info("Skipped (duplicate email): {$skipped}");

        return self::SUCCESS;
    }

    /**
     * Treat the dump's `-` / empty-string placeholders as null.
     */
    private function clean(?string $value): ?string
    {
        if ($value === null) return null;
        $value = trim($value);
        if ($value === '' || $value === '-') return null;
        return $value;
    }

    private function parseTs($value): ?Carbon
    {
        if (! $value) return null;
        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
