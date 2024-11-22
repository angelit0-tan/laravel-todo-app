<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user is the owner of the task
        if ($this->method() != self::METHOD_POST) {
            if ($this->task->user_id !== Auth::user()->id) abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized Request');
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // If method is delete, get, don't validate any rules
        if (collect([self::METHOD_GET, self::METHOD_DELETE])->contains($this->method())) {
            return [];
        }

        return [
            'title'         => 'required|max:100',
            'description'   => 'required|max:500',
            'due_date'      => 'nullable|date_format:Y-m-d|after_or_equal:today',
        ];
    }
}
