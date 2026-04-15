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
        // Add phase_id column
        Schema::table('workflow_steps', function (Blueprint $table) {
            $table->foreignId('phase_id')->nullable()->after('id')->constrained('workflow_phases')->nullOnDelete();
        });

        // Map existing phase numbers to new phase_id
        $phases = \DB::table('workflow_phases')->orderBy('sort_order')->pluck('id');
        $phase1Id = $phases[0] ?? null;
        $phase2Id = $phases[1] ?? null;

        if ($phase1Id) \DB::table('workflow_steps')->where('phase', 1)->update(['phase_id' => $phase1Id]);
        if ($phase2Id) \DB::table('workflow_steps')->where('phase', 2)->update(['phase_id' => $phase2Id]);

        // Drop old columns
        Schema::table('workflow_steps', function (Blueprint $table) {
            $table->dropColumn(['phase', 'phase_label']);
        });
    }

    public function down(): void
    {
        Schema::table('workflow_steps', function (Blueprint $table) {
            $table->tinyInteger('phase')->default(1)->after('id');
            $table->string('phase_label')->nullable()->after('phase');
            $table->dropForeign(['phase_id']);
            $table->dropColumn('phase_id');
        });
    }
};
