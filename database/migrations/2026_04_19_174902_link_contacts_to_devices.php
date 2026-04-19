<?php

use App\Models\Contact;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->foreignId('contact_id')->nullable()->after('coordinator_id')->constrained('contacts')->nullOnDelete();
        });

        // Migrate existing device customer data into contacts (deduplicate by email)
        DB::table('devices')
            ->select('customer_name', 'customer_email', 'customer_phone')
            ->whereNotNull('customer_name')
            ->distinct()
            ->get()
            ->each(function ($row) {
                $contact = Contact::firstOrCreate(
                    ['email' => $row->customer_email ?: null],
                    [
                        'name'  => $row->customer_name,
                        'phone' => $row->customer_phone,
                    ]
                );

                DB::table('devices')
                    ->where('customer_name', $row->customer_name)
                    ->when($row->customer_email, fn ($q) => $q->where('customer_email', $row->customer_email))
                    ->update(['contact_id' => $contact->id]);
            });

        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_email', 'customer_phone']);
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
            $table->dropColumn('contact_id');
        });
    }
};
