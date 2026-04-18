<?php

namespace App\Jobs;

use App\Models\FormSubmission;
use App\Services\CrmService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class SyncFormSubmissionToCrm implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(public readonly int $submissionId) {}

    public function handle(CrmService $crm): void
    {
        $submission = FormSubmission::with(['form.fields'])->find($this->submissionId);

        if (! $submission || ! $submission->form) return;

        $form = $submission->form;

        if (! $form->crm_sync || blank($form->crm_key)) return;

        $tableName = Str::slug($form->name, '_');

        // Register form in CRM (idempotent — safe to call every time)
        $fields = $form->fields->map(fn ($f) => [
            'label' => $f->label,
            'type'  => $f->type,
        ])->values()->toArray();

        $crm->registerForm($form->crm_key, $form->name, $tableName, $fields);

        // Submit the entry
        $crm->submitEntry($form->crm_key, $submission->data ?? []);
    }
}
