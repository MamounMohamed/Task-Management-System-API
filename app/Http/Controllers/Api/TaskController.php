<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\TaskStoreRequest;
use App\Http\Requests\Tasks\TaskUpdateRequest;
use App\Http\Requests\Tasks\TaskDependencyRequest;
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
        $this->authorize('viewAny', Task::class);
        $filters = $request->only(['status', 'assignee_id', 'due_from', 'due_to']);
        $tasks = $this->service->filter($filters);

        // users only see their tasks
        if ($request->user()->role === 'user') {
            $tasks = $tasks->where('assignee_id', $request->user()->id);
        }

        return $this->successResponse(data: TaskResource::collection($tasks));
    }

    public function store(TaskStoreRequest $request)

    {
        $this->authorize('create', Task::class);
        $task = $this->service->create($request->validated());
        return $this->successResponse(data: TaskResource::make($task), message: 'Task created successfully', statusCode: 201);
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return $this->successResponse(
            data: TaskResource::make(
                $task->load('dependencies', 'assignee', 'creator')
            )
        );
    }
    public function update(TaskUpdateRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $task = $this->service->update($task, $request->validated());
        return $this->successResponse(data: TaskResource::make($task), message: 'Task updated successfully');
    }

    public function addDependencies(TaskDependencyRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $task = $this->service->addDependencies($task, $request->dependencies);
        return $this->successResponse(data: TaskResource::make($task));
    }
}
