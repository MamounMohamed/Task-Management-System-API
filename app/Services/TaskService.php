<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
class TaskService
{
    public function create(array $data): Task
    {

        $task = Task::create(Arr::except($data, ['dependencies']));
        if(isset($data['dependencies'])) {
            $task->dependencies()->attach($data['dependencies']);
        }
        return $task;
    }

    public function update(Task $task, array $data): Task
    {
        if (isset($data['status']) && $data['status'] === 'completed') {
            $incompleteDeps = $task->dependencies()->where('status', '!=', 'completed')->exists();
            if ($incompleteDeps) {
                throw new \Exception("Task cannot be marked completed until all dependencies are done.");
            }
        }

        $task->update(Arr::except($data, ['dependencies']));
        if(isset($data['dependencies'])) {
            $task->dependencies()->sync($data['dependencies']);
        }
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

        if (isset($filters['assignee_id'])) {
            $query->where('assignee_id', $filters['assignee_id']);
        }

        if (isset($filters['due_from']) && isset($filters['due_to'])) {
            $query->whereBetween('due_date', [$filters['due_from'], $filters['due_to']]);
        }

        return $query->with('dependencies', 'assignedUser')->get();
    }
}
