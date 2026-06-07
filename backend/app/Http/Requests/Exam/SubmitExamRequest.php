<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class SubmitExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => ['required', 'uuid', 'exists:exam_sessions,id'],
            'answers' => ['required', 'array', 'min:1'],
            'answers.*' => ['required', 'integer', 'min:0', 'max:3'],
            'duration_sec' => ['required', 'integer', 'min:1', 'max:10800'],
            'started_at' => ['required', 'date'],
        ];
    }
}
