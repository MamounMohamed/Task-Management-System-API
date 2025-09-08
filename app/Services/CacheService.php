<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    protected string $tag;

    public function __construct(string $tag = 'tasks')
    {
        $this->tag = $tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }
  
    public function rememberTasks(array $filters, \Closure $callback)
    {
        $key = $this->getCacheKey($filters);
        return Cache::tags($this->tag)->remember(
            $key,
            now()->addMinutes(10),
            $callback
        );
    }

    public function rememberTask(int $taskId, \Closure $callback): Task
    {
        return Cache::tags($this->tag)->remember(
            'task:' . $taskId,
            now()->addMinutes(10),
            $callback
        );
    }

    public function forgetTask(int $taskId): void
    {
        Cache::tags($this->tag)->forget('task:' . $taskId);
    }

    public function forgetAll(): void
    {
        Cache::tags($this->tag)->flush();
    }

    private function getCacheKey(array $filters): string
    {
        ksort($filters);
        return 'tasks:' . md5(json_encode($filters));
    }
}
