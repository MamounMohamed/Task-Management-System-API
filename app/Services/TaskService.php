<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        if (isset($data['status']) && $data['status'] === 'completed') {
            $incompleteDeps = $task->dependencies()->where('status', '!=', 'completed')->exists();
            if ($incompleteDeps) {
                throw new \Exception("Task cannot be marked completed until all dependencies are done.");
            }
        }

        $task->update($data);
        return $task;
    }

    public function addDependencies(Task $task, array $dependencies): Task
    {
        DB::transaction(function () use ($task, $dependencies) {
            $task->dependencies()->syncWithoutDetaching($dependencies);
        });

        return $task->load('dependencies');
    }

    public function filter(array $filters)
    {
        $query = Task::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['assigned_user_id'])) {
            $query->where('assigned_user_id', $filters['assigned_user_id']);
        }

        if (isset($filters['due_from']) && isset($filters['due_to'])) {
            $query->whereBetween('due_date', [$filters['due_from'], $filters['due_to']]);
        }

        return $query->with('dependencies', 'assignedUser')->get();
    }
}
