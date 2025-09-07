<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 tasks
        $tasks = Task::factory(10)->create();

        foreach ($tasks as $task) {
            // Possible dependencies = all tasks created before this one
            $possibleDependencies = $tasks->where('id', '<', $task->id)->pluck('id')->toArray();
            // Ensure no cyclic dependencies by only choosing "earlier" tasks
            if (!empty($possibleDependencies)) {
                $dependencies = collect($possibleDependencies)
                    ->random(rand(0, min(3, count($possibleDependencies)))) 
                    ->all();
                $task->dependencies()->attach($dependencies);
            }
        }
    }
}
