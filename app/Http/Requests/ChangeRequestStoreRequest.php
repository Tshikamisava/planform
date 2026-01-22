<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChangeRequestStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow if user is authenticated and has author or admin role
        $user = Auth::user();
        return $user && ($user->isAuthor() || $user->isAdministrator());
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'author_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'reason' => ['required', 'string'], // Map to reason_for_change
            'request_type' => ['nullable', 'string', Rule::in(['Standard', 'Emergency', 'Routine', 'Corrective'])],
            'priority' => ['nullable', 'string', Rule::in(['Low', 'Medium', 'High', 'Critical'])],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'recipient_id' => ['nullable', 'exists:users,id'],
            'decision_maker_id' => ['nullable', 'exists:users,id'],
            'status' => ['nullable', 'string'],
            // Impact analysis fields
            'cost' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric'],
            'tooling' => ['nullable', 'boolean'],
            'tooling_desc' => ['nullable', 'string', 'required_if:tooling,1'],
            'inventory_scrap' => ['nullable', 'boolean'],
            'parts' => ['nullable', 'array'],
            'parts.*.number' => ['required_with:parts', 'string'],
            'parts.*.current_rev' => ['nullable', 'string'],
            'parts.*.new_rev' => ['nullable', 'string'],
            // Attachments
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => [
                'file',
                'max:10240', // 10MB max per file
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,png,jpg,jpeg,dxf,step,stp',
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
            'recipient_id.required' => 'The recipient is required.',
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
            $this->merge(['author_id' => Auth::id()]);
        }

        // Auto-populate status if not provided
        if (!$this->has('status')) {
            $this->merge(['status' => 'Draft']);
        }

        // Map 'reason' to 'reason_for_change'
        if ($this->has('reason')) {
            $this->merge(['reason_for_change' => $this->input('reason')]);
        }

        // Set default values
        if (!$this->has('request_type')) {
            $this->merge(['request_type' => 'Standard']);
        }
        if (!$this->has('priority')) {
            $this->merge(['priority' => 'Medium']);
        }
        if (!$this->has('due_date')) {
            $this->merge(['due_date' => now()->addDays(14)->format('Y-m-d')]);
        }

        // Convert checkbox values to boolean
        if ($this->has('tooling')) {
            $this->merge(['tooling' => (bool) $this->input('tooling')]);
        }
        if ($this->has('inventory_scrap')) {
            $this->merge(['inventory_scrap' => (bool) $this->input('inventory_scrap')]);
        }

        // Auto-assign recipient if not provided (find first recipient user)
        if (!$this->has('recipient_id') || !$this->recipient_id) {
            $recipient = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'Recipient');
            })->first();
            if ($recipient) {
                $this->merge(['recipient_id' => $recipient->id]);
            }
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
    public function validated($key = null, $default = null): mixed
    {
        // If requesting a specific key, delegate to parent
        if ($key !== null) {
            return parent::validated($key, $default);
        }
        
        $validated = parent::validated();

        // Generate UUID if not provided
        if (!isset($validated['uuid'])) {
            $validated['uuid'] = Str::uuid();
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
