<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Task;
use App\Policies\TaskPolicy;
use App\Observers\TaskObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::Policy(Task::class, TaskPolicy::class);
        Task::observe(TaskObserver::class);
    }
}
