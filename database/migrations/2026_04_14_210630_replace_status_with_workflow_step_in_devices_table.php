<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->foreignId('workflow_step_id')
                ->nullable()
                ->after('coordinator_id')
                ->constrained('workflow_steps')
                ->nullOnDelete();

            $table->dropColumn('status');
        });

        // Assign a random step to existing devices
        $stepIds = \DB::table('workflow_steps')->pluck('id')->toArray();
        if (! empty($stepIds)) {
            \DB::table('devices')->get()->each(function ($device) use ($stepIds) {
                \DB::table('devices')
                    ->where('id', $device->id)
                    ->update(['workflow_step_id' => $stepIds[array_rand($stepIds)]]);
            });
        }
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('status')->default('received')->after('coordinator_id');
            $table->dropForeign(['workflow_step_id']);
            $table->dropColumn('workflow_step_id');
        });
    }
};
