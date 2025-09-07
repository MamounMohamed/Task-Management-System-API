<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class TaskDependencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'manager';
    }

    public function rules(): array
    {
        return [
            'dependencies' => 'required|array',
            'dependencies.*' => 'exists:tasks,id|different:task_id',
        ];
    }
}
