<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $manager;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = User::factory()->create(['role' => 'manager']);
        $this->user = User::factory()->create(['role' => 'user']);
    }

    /** @test */
    public function manager_can_create_a_task()
    {
        $dependency = Task::factory()->create();
        $payload = [
            'title' => 'New Task',
            'description' => 'Task details',
            'due_date' => now()->addDays(5)->toDateString(),
            'assignee_id' => $this->user->id,
            'dependencies' => [$dependency->id]
        ];

        $response = $this->actingAs($this->manager, 'sanctum')
            ->postJson('/api/tasks', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'New Task');

        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
        $this->assertDatabaseHas('task_dependencies', [
            'task_id' => $response->json('data.id'),
            'dependency_id' => $dependency->id
        ]);
    }

    /** @test */
    public function user_cannot_create_task()
    {
        $payload = ['title' => 'Forbidden'];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/tasks', $payload);

        $response->assertForbidden();
    }

    /** @test */
    public function validation_fails_with_invalid_data()
    {
        $response = $this->actingAs($this->manager, 'sanctum')
            ->postJson('/api/tasks', [
                'title' => '', // required
                'due_date' => 'yesterday',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'due_date']);
    }

    /** @test */
    public function user_can_only_see_their_tasks()
    {
        $taskForUser = Task::factory()->create(['assignee_id' => $this->user->id]);
        $taskForOther = Task::factory()->create(['assignee_id' => User::factory()->create()->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/tasks');

        $response->assertOk();

        $taskIds = collect($response->json('data'))->pluck('id');

        $this->assertTrue($taskIds->contains($taskForUser->id));
        $this->assertFalse($taskIds->contains($taskForOther->id));
    }

    /** @test */
    public function manager_can_see_all_tasks()
    {
        Task::factory(3)->create();

        $response = $this->actingAs($this->manager, 'sanctum')
            ->getJson('/api/tasks');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function can_filter_tasks_by_status_and_date_range()
    {
        $completedTask = Task::factory()->create(['status' => 'completed']);
        $pendingTask = Task::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->manager, 'sanctum')
            ->getJson('/api/tasks?status=completed');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');

        // Extract the IDs from the response
        $taskIds = collect($response->json('data'))->pluck('id');

        $this->assertTrue($taskIds->contains($completedTask->id));
        $this->assertFalse($taskIds->contains($pendingTask->id));
    }

    /** @test */
    public function user_can_update_only_their_task_status()
    {
        $task = Task::factory()->create(['assignee_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/tasks/{$task->id}", ['status' => 'completed']);

        $response->assertOk()
            ->assertJsonPath('data.status', 'completed');
    }

    /** @test */
    public function user_cannot_update_task_details()
    {
        $task = Task::factory()->create(['assignee_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/tasks/{$task->id}", ['title' => 'Hacked']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']); // only status is allowed
    }

    /** @test */
    public function manager_can_update_all_task_fields()
    {
        $task = Task::factory()->create();

        $response = $this->actingAs($this->manager, 'sanctum')
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Updated',
                'status' => 'pending',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Updated');
    }

    /** @test */
    public function cannot_mark_completed_if_dependencies_are_not_done()
    {
        $dep = Task::factory()->create(['status' => 'pending']);
        $task = Task::factory()->create(['status' => 'pending']);
        $task->dependencies()->attach($dep);

        $response = $this->actingAs($this->manager, 'sanctum')
            ->putJson("/api/tasks/{$task->id}", ['status' => 'completed']);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Task cannot be marked completed until all dependencies are done.']);
    }

    /** @test */
    public function manager_can_add_dependencies()
    {
        $task = Task::factory()->create();
        $dep1 = Task::factory()->create();
        $dep2 = Task::factory()->create();

        $response = $this->actingAs($this->manager, 'sanctum')
            ->postJson("/api/tasks/{$task->id}/dependencies", [
                'dependencies' => [$dep1->id, $dep2->id],
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('task_dependencies', [
            'task_id' => $task->id,
            'dependency_id' => $dep1->id
        ]);
        $this->assertDatabaseHas('task_dependencies', [
            'task_id' => $task->id,
            'dependency_id' => $dep2->id
        ]);
    }
}
