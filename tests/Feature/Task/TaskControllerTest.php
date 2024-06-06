<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this > actingAs($this->user, 'sanctum');
});

it('can create task', function () {
    $headers = [
        'Accept' => 'application/json',
    ];

    $response = $this->postJson('/api/tasks', [
        'title' => 'New Task',
        'description' => 'Description of the task',
        'is_important' => true,
        'is_completed' => false,
        'due_date' => now()->addDays(7)->toDateTimeString(),
        'user_id' => $this->user->id,
    ], $headers);

    $response->assertStatus(201)
        ->assertJson([
            'title' => 'New Task',
            'description' => 'Description of the task',
            'is_important' => true,
            'is_completed' => false,
        ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'New Task',
        'description' => 'Description of the task',
        'is_important' => true,
        'is_completed' => false,
        'user_id' => $this->user->id,
    ]);
});

it('can get tasks', function () {
    Task::factory()->create(['title' => 'First Task', 'user_id' => $this->user->id]);
    Task::factory()->create(['title' => 'Second Task', 'user_id' => $this->user->id]);

    $response = $this->getJson('/api/tasks');

    $response->assertStatus(200)
        ->assertJsonCount(2)
        ->assertJsonFragment(['title' => 'First Task'])
        ->assertJsonFragment(['title' => 'Second Task']);
    $this->assertTrue(true);
});

it('can get a specific task', function () {
    $task = Task::factory()->create(['title' => 'Specific Task', 'user_id' => $this->user->id]);

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson([
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'is_important' => $task->is_important,
            'is_completed' => $task->is_completed,
            'due_date' => $task->due_date,
        ]);
});

it('can update a task', function () {
    $task = Task::factory()->create(['title' => 'Initial Task', 'user_id' => $this->user->id]);

    $new_task_data = collect([
        'title' => 'Updated Task',
        'description' => 'Updated description',
        'is_important' => false,
        'is_completed' => true,
    ]);

    $response = $this->putJson("/api/tasks/{$task->id}", $new_task_data->all());

    $response->assertStatus(200)
        ->assertJson($new_task_data->all());

    $this->assertDatabaseHas('tasks', $new_task_data->merge(['user_id' => $this->user->id])->all());
});

it('can delete task', function () {
    $task = Task::factory()->create(['title' => 'Task to Delete', 'user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});
