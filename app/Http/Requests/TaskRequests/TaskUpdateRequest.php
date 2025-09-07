<?php

namespace App\Http\Requests\TaskRequests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Task;

class TaskUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');
        return $this->user()->can('update', $task);
    }

    public function rules(): array
    {
        $user = $this->user();

        if ($user->role === 'manager') {
            return [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string',
                'due_date' => 'sometimes|nullable|date|after:today',
                'assignee_id' => 'sometimes|nullable|exists:users,id',
                'status' => 'sometimes|in:pending,completed,cancelled',
                'dependencies' => 'sometimes|nullable|array',
                'dependencies.*' => 'sometimes|exists:tasks,id|different:task_id',
            ];
        }

        return [
            'status' => 'required|in:pending,completed,cancelled',
        ];
    }
}
