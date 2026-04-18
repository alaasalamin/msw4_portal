<?php

namespace App\Observers;

use App\Jobs\SyncFormSubmissionToCrm;
use App\Models\FormSubmission;

class FormSubmissionObserver
{
    public function created(FormSubmission $formSubmission): void
    {
        SyncFormSubmissionToCrm::dispatch($formSubmission->id);
    }

    /**
     * Handle the FormSubmission "updated" event.
     */
    public function updated(FormSubmission $formSubmission): void
    {
        //
    }

    /**
     * Handle the FormSubmission "deleted" event.
     */
    public function deleted(FormSubmission $formSubmission): void
    {
        //
    }

    /**
     * Handle the FormSubmission "restored" event.
     */
    public function restored(FormSubmission $formSubmission): void
    {
        //
    }

    /**
     * Handle the FormSubmission "force deleted" event.
     */
    public function forceDeleted(FormSubmission $formSubmission): void
    {
        //
    }
}
