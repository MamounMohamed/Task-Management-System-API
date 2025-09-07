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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class TaskController extends Controller
{
    use AuthorizesRequests;

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
        try {
            $task = $this->service->create($request->validated());
            return $this->successResponse(data: TaskResource::make($task), message: 'Task created successfully', statusCode: 201);
        } catch (HttpException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return $this->successResponse(data: TaskResource::make($task->load('dependencies', 'assignee', 'creator')));
    }

    public function update(TaskUpdateRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        try {
            $task = $this->service->update($task, $request->validated());
            return $this->successResponse(data: TaskResource::make($task), message: 'Task updated successfully');
        } catch (HttpException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }
    }

    public function addDependencies(TaskDependencyRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        try {
            $task = $this->service->addDependencies($task, $request->dependencies);
            return $this->successResponse(data: TaskResource::make($task));
        } catch (HttpException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }
    }
}
