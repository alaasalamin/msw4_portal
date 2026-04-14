<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Spatie\Activitylog\Models\Activity;

class ActivityLogWidget extends Widget
{
    protected string $view = 'filament.widgets.activity-log-widget';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function getActivities()
    {
        return Activity::with('causer')
            ->latest()
            ->limit(50)
            ->get()
            ->map(function (Activity $activity) {
                $causer     = $activity->causer;
                $causerName = $causer?->name ?? 'System';
                $event      = $activity->event ?? 'updated';
                $subject    = $activity->subject_type
                    ? class_basename($activity->subject_type)
                    : null;
                $properties = $activity->properties->except(['attributes', 'old'])->toArray();
                $changes    = $activity->properties->get('attributes', []);
                $old        = $activity->properties->get('old', []);

                // Build human-readable description
                if ($activity->log_name === 'site_settings') {
                    $desc = "Updated <strong>Site Settings</strong>";
                } elseif ($activity->log_name === 'email_settings') {
                    $desc = "Updated <strong>Email Settings</strong>";
                } elseif ($activity->log_name === 'addons') {
                    $desc = "Updated <strong>Add-on Settings</strong>";
                } elseif ($subject && $activity->subject) {
                    $label = $this->subjectLabel($activity);
                    $desc  = ucfirst($event) . " <strong>{$subject}</strong>" . ($label ? ": {$label}" : '');
                } else {
                    $desc = $activity->description;
                }

                return [
                    'id'          => $activity->id,
                    'causer'      => $causerName,
                    'description' => $desc,
                    'event'       => $event,
                    'changes'     => !empty($changes) ? $changes : $properties,
                    'old'         => $old,
                    'time'        => $activity->created_at,
                    'log_name'    => $activity->log_name,
                ];
            });
    }

    private function subjectLabel(Activity $activity): string
    {
        $subject = $activity->subject;
        if (!$subject) return '';

        return match (true) {
            isset($subject->tracking_number) => $subject->tracking_number,
            isset($subject->ticket_number)   => $subject->ticket_number,
            isset($subject->name)            => $subject->name,
            isset($subject->email)           => $subject->email,
            default                          => "#{$subject->id}",
        };
    }

    public static function getEventColor(string $event): string
    {
        return match ($event) {
            'created' => 'success',
            'deleted' => 'danger',
            'updated' => 'warning',
            default   => 'info',
        };
    }
}
