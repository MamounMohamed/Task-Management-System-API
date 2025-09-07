<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;

class TaskPolicy
{
    /**
     * Determine whether the user can view a specific task.
     * - Managers can view any task
     * - Users can only view their assigned tasks
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->role === 'manager') {
            return true;
        }

        return $task->assignee_id === $user->id;
    }

    /**
     * Determine whether the user can view any tasks.
     * - Managers can see all
     * - Users can only see their own tasks
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['manager', 'user']);
    }

    /**
     * Determine whether the user can create tasks.
     * - Only managers
     */
    public function create(User $user): bool
    {
        return $user->role === 'manager';
    }

    /**
     * Determine whether the user can update the task.
     * - Managers can update any details
     * - Users can only update status of their own tasks
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->role === 'manager') {
            return true;
        }

        if ($user->role === 'user' && $task->assignee_id === $user->id) {
            return true;
        }

        return false;
    }


    /**
     * Determine whether the user can delete a task.
     * - Only managers
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->role === 'manager';
    }
}
