<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeRequestStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('create-dcr');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'reason_for_change' => ['required', 'string', 'min:10'],
            'request_type' => ['required', 'string', Rule::in(['Standard', 'Emergency', 'Routine', 'Corrective'])],
            'priority' => ['required', 'string', Rule::in(['Low', 'Medium', 'High', 'Critical'])],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'recipient_id' => ['nullable', 'exists:users,id'],
            'decision_maker_id' => ['nullable', 'exists:users,id'],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => [
                'file',
                'max:10240', // 10MB max per file
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar',
                'distinct'
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The title is required.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.required' => 'The description is required.',
            'description.min' => 'The description must be at least 10 characters.',
            'reason_for_change.required' => 'The reason for change is required.',
            'reason_for_change.min' => 'The reason for change must be at least 10 characters.',
            'request_type.required' => 'The request type is required.',
            'request_type.in' => 'The request type must be one of: Standard, Emergency, Routine, Corrective.',
            'priority.required' => 'The priority is required.',
            'priority.in' => 'The priority must be one of: Low, Medium, High, Critical.',
            'due_date.required' => 'The due date is required.',
            'due_date.after_or_equal' => 'The due date must be today or a future date.',
            'recipient_id.exists' => 'The selected recipient is invalid.',
            'decision_maker_id.exists' => 'The selected decision maker is invalid.',
            'attachments.max' => 'You may upload a maximum of 10 files.',
            'attachments.*.max' => 'Each file may not be greater than 10MB.',
            'attachments.*.mimes' => 'Each file must be one of: pdf, doc, docx, xls, xlsx, ppt, pptx, txt, zip, rar.',
            'attachments.*.distinct' => 'Each file must be unique.',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'title',
            'description' => 'description',
            'reason_for_change' => 'reason for change',
            'request_type' => 'request type',
            'priority' => 'priority',
            'due_date' => 'due date',
            'recipient_id' => 'recipient',
            'decision_maker_id' => 'decision maker',
            'attachments' => 'attachments',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-populate author_id if not provided
        if (!$this->has('author_id')) {
            $this->merge(['author_id' => auth()->id()]);
        }

        // Auto-populate status if not provided
        if (!$this->has('status')) {
            $this->merge(['status' => 'Draft']);
        }

        // Validate recipient and decision maker roles
        if ($this->has('recipient_id') && $this->recipient_id) {
            $recipient = \App\Models\User::find($this->recipient_id);
            if (!$recipient || !$recipient->isRecipient()) {
                $this->merge(['recipient_id' => null]);
            }
        }

        if ($this->has('decision_maker_id') && $this->decision_maker_id) {
            $decisionMaker = \App\Models\User::find($this->decision_maker_id);
            if (!$decisionMaker || !$decisionMaker->isDecisionMaker()) {
                $this->merge(['decision_maker_id' => null]);
            }
        }
    }

    /**
     * Get the validated data with additional processing.
     */
    public function validated(): array
    {
        $validated = parent::validated();

        // Generate UUID if not provided
        if (!isset($validated['uuid'])) {
            $validated['uuid'] = \Str::uuid();
        }

        // Generate DCR ID if not provided
        if (!isset($validated['dcr_id'])) {
            $validated['dcr_id'] = \App\Models\ChangeRequest::generateDcrId();
        }

        // Process attachments
        if (isset($validated['attachments']) && is_array($validated['attachments'])) {
            $attachmentData = [];
            foreach ($validated['attachments'] as $key => $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $attachmentData[$key] = [
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'uploaded_at' => now()->toISOString(),
                    ];
                }
            }
            $validated['attachments'] = $attachmentData;
        }

        return $validated;
    }
}
