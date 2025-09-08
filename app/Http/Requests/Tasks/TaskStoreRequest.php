<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Task::class);
    }
    
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date|after:today',
            'assignee_id' => 'required|exists:users,id',
            'dependencies' => 'nullable|array',
            'dependencies.*' => 'exists:tasks,id|different:task_id',
        ];
    }
}
