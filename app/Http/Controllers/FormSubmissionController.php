<?php

namespace App\Http\Controllers;

use App\Models\CustomForm;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class FormSubmissionController extends Controller
{
    public function submit(Request $request, int $formId)
    {
        $form = CustomForm::with('fields')->findOrFail($formId);

        // Build validation rules from the form fields
        $rules = [];
        foreach ($form->fields as $field) {
            $key = 'field_' . $field->id;
            $rule = $field->is_required ? 'required' : 'nullable';

            $rule .= match ($field->type) {
                'email'  => '|email|max:255',
                'number' => '|numeric',
                'tel'    => '|max:30',
                default  => '|max:2000',
            };

            $rules[$key] = $rule;
        }

        $validated = $request->validate($rules);

        // Map field IDs back to labels for readable storage
        $data = [];
        foreach ($form->fields as $field) {
            $key = 'field_' . $field->id;
            if (array_key_exists($key, $validated) && $validated[$key] !== null) {
                $data[$field->label] = $validated[$key];
            }
        }

        FormSubmission::create([
            'form_id'    => $form->id,
            'page_slug'  => $request->input('_page_slug'),
            'data'       => $data,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $form->success_message ?: 'Thank you! Your message has been received.',
                'redirect' => $form->redirect_url ?: null,
            ]);
        }

        return back()->with('form_success', $form->success_message ?: 'Thank you! Your message has been received.');
    }
}
