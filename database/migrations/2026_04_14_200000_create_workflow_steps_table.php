<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('phase')->default(1);
            $table->string('phase_label')->nullable();
            $table->string('label');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ── Seed default steps ────────────────────────────────────────────
        $phase1 = 'Phase 1: Initial Diagnosis & Preparation';
        $phase2 = 'Phase 2: Completion & Logistics';

        $steps = [
            [1, $phase1, 'Konvertiert / Aufgenommen / Erhalten / In Diagnose',  1],
            [1, $phase1, 'Mainboard Diagnose',                                   2],
            [1, $phase1, 'Warten auf Ersatzteil',                                3],
            [1, $phase1, 'Fehler gefunden',                                      4],
            [1, $phase1, 'Kv geschrieben',                                       5],
            [1, $phase1, 'Freigabe',                                             6],
            [1, $phase1, 'Zusammenbauen',                                        7],
            [2, $phase2, 'Repariert',                                            8],
            [2, $phase2, 'Getestet',                                             9],
            [2, $phase2, 'Kunde holt ab',                                       10],
            [2, $phase2, 'Rechnung + Versand',                                  11],
            [2, $phase2, 'Verpackt',                                            12],
            [2, $phase2, 'Zahlungseingang',                                     13],
            [2, $phase2, 'Abgeschlossen',                                       14],
        ];

        $now = now();
        foreach ($steps as [$phase, $phaseLabel, $label, $order]) {
            DB::table('workflow_steps')->insert([
                'phase'       => $phase,
                'phase_label' => $phaseLabel,
                'label'       => $label,
                'sort_order'  => $order,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
