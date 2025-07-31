<?php
// app/Http/Requests/UploadEmployeeFilesRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadEmployeeFilesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; //
    }

    public function rules(): array
    {
        return [
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'contract' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'document' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ];
    }
}
