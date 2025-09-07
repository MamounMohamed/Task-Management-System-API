<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequests\TaskStoreRequest;
use App\Http\Requests\TaskRequests\TaskUpdateRequest;
use App\Http\Requests\TaskRequests\TaskDependencyRequest; 
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected TaskService $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'assignee_id', 'due_date']);
        $tasks = $this->service->filter($filters);

        // users only see their tasks
        if ($request->user()->role === 'user') {
            $tasks = $tasks->where('assigned_user_id', $request->user()->id);
        }

        return TaskResource::collection($tasks);
    }

    public function store(TaskStoreRequest $request)
    {
        $task = $this->service->create($request->validated());
        return new TaskResource($task);
    }

    public function show(Task $task)
    {
        return new TaskResource($task->load('dependencies', 'assignedUser'));
    }

    public function update(TaskUpdateRequest $request, Task $task)
    {
        $task = $this->service->update($task, $request->validated());
        return new TaskResource($task);
    }

    public function addDependencies(TaskDependencyRequest $request, Task $task)
    {
        $task = $this->service->addDependencies($task, $request->dependencies);
        return new TaskResource($task);
    }
}
