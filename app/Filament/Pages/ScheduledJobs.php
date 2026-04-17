<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class ScheduledJobs extends Page
{
    protected string $view = 'filament.pages.scheduled-jobs';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-clock'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Configuration'; }
    public static function getNavigationSort(): ?int                    { return 7; }
    public static function getNavigationLabel(): string                 { return 'Job-Übersicht'; }
    public function getTitle(): string                                   { return 'Job-Übersicht'; }

    public function getJobs(): array
    {
        $rows = DB::table('jobs')
            ->orderBy('available_at')
            ->get();

        return $rows->map(function ($row) {
            $payload    = json_decode($row->payload, true);
            $class      = $payload['displayName'] ?? ($payload['job'] ?? 'Unbekannt');
            $data       = $payload['data'] ?? [];
            $command    = isset($data['command']) ? @unserialize($data['command']) : null;
            $now        = now()->timestamp;
            $available  = (int) $row->available_at;
            $isDelayed  = $available > $now;

            return [
                'id'          => $row->id,
                'queue'       => $row->queue,
                'class'       => class_basename($class),
                'full_class'  => $class,
                'attempts'    => (int) $row->attempts,
                'reserved'    => (bool) $row->reserved_at,
                'available_at'=> $available,
                'created_at'  => (int) $row->created_at,
                'is_delayed'  => $isDelayed,
                'delay_secs'  => max(0, $available - $now),
                'meta'        => $this->extractMeta($command, $class),
            ];
        })->toArray();
    }

    public function getFailedJobs(): array
    {
        return DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->limit(50)
            ->get()
            ->map(function ($row) {
                $payload = json_decode($row->payload, true);
                $class   = $payload['displayName'] ?? ($payload['job'] ?? 'Unbekannt');
                $data    = $payload['data'] ?? [];
                $command = isset($data['command']) ? @unserialize($data['command']) : null;

                return [
                    'id'            => $row->id,
                    'uuid'          => $row->uuid,
                    'queue'         => $row->queue,
                    'class'         => class_basename($class),
                    'failed_at'     => $row->failed_at,
                    'exception'     => collect(explode("\n", $row->exception))->first(),
                    'exception_full' => $row->exception,
                    'meta'          => $this->extractMeta($command, $class),
                ];
            })->toArray();
    }

    public ?string $expandedJob = null;

    public function getRecentLogs(): array
    {
        return DB::table('automation_logs')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn ($row) => [
                'id'           => $row->id,
                'rule_name'    => $row->rule_name,
                'trigger_type' => $row->trigger_type,
                'action_type'  => $row->action_type,
                'status'       => $row->status,
                'device_id'    => $row->device_id,
                'payload'      => json_decode($row->payload ?? '{}', true),
                'error'        => $row->error,
                'created_at'   => $row->created_at,
            ])
            ->toArray();
    }

    public function toggleJobLog(string $uuid): void
    {
        $this->expandedJob = $this->expandedJob === $uuid ? null : $uuid;
    }

    public function retryFailed(string $uuid): void
    {
        \Artisan::call('queue:retry', ['id' => [$uuid]]);
    }

    public function deleteFailed(string $uuid): void
    {
        DB::table('failed_jobs')->where('uuid', $uuid)->delete();
    }

    public function deleteJob(int $id): void
    {
        DB::table('jobs')->where('id', $id)->delete();
    }

    // ── Extract human-readable context from the serialized job ───────────────
    private function extractMeta(mixed $command, string $class): array
    {
        if (! is_object($command)) return [];

        $meta = [];

        // SendAutomationEmailJob
        if (property_exists($command, 'device') && $command->device) {
            $meta['Ticket'] = $command->device->ticket_number ?? '—';
            $meta['Gerät']  = trim(($command->device->brand ?? '') . ' ' . ($command->device->model ?? ''));
        }
        if (property_exists($command, 'config') && is_array($command->config)) {
            if (isset($command->config['subject'])) $meta['Betreff'] = $command->config['subject'];
            if (isset($command->config['delay_value'])) {
                $unit = match($command->config['delay_unit'] ?? 'hours') {
                    'minutes' => 'Min.', 'days' => 'Tage', default => 'Std.',
                };
                $meta['Verzögerung'] = $command->config['delay_value'] . ' ' . $unit;
            }
        }
        if (property_exists($command, 'ruleName') && $command->ruleName) {
            $meta['Regel'] = $command->ruleName;
        }

        return $meta;
    }
}
