<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\CacheService;

class TaskObserver
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    public function created(Task $task): void
    {
        $this->cache->forgetAll();
    }

    public function updated(Task $task): void
    {
        $this->cache->forgetAll();
        $this->cache->forgetTask($task->id); // invalidate single task cache
    }

    public function deleted(Task $task): void
    {
        $this->cache->forgetAll();
        $this->cache->forgetTask($task->id);
    }
}
