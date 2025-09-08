<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskService
{
    public function create(array $data): Task
    {
        return DB::transaction(function () use ($data) {
            $data['creator_id'] = auth()->guard('sanctum')->user()->id;

            // Create the task
            $task = Task::create(Arr::except($data, ['dependencies']));

            // Attach dependencies if any
            if (isset($data['dependencies'])) {
                $task->dependencies()->attach($data['dependencies']);
                $task->load('dependencies');
            }
            $task->load('assignee');

            return $task;
        });
    }

    public function update(Task $task, array $data): Task
    {
        if($task->status === 'completed') {
            throw new HttpException(422, "Task cannot be updated after completetion.");
        }
        

        return DB::transaction(function () use ($task, $data) {

            if (isset($data['status']) && $data['status'] === 'completed') {
                $incompleteDeps = $task->dependencies()->where('status', '!=', 'completed')->exists();
                if ($incompleteDeps) {
                    throw new HttpException(422, "Task cannot be marked completed until all dependencies are done.");
                }
            }

            $task->update(Arr::except($data, ['dependencies']));

            if (isset($data['dependencies'])) {
                $task->dependencies()->sync($data['dependencies']);
                $task->load('dependencies');
            }

            if (isset($data['assignee_id'])) {
                $task->load('assignee');
            }

            return $task;
        });
    }


    public function addDependencies(Task $task, array $dependencies): Task
    {
        if ($task->status === 'completed') {
            throw new HttpException(422, "Dependencies cannot be added after completetion.");
        }

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

        if (isset($filters['request_user_id'])) {
            $query->where('assignee_id', $filters['request_user_id']);
        }

        return $query->with('dependencies', 'assignee', 'creator')->paginate(5);
    }
}
