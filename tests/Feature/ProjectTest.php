<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        Permission::create(['name' => 'view any projects', 'guard_name' => 'web']);
        Permission::create(['name' => 'view projects', 'guard_name' => 'web']);
        Permission::create(['name' => 'create projects', 'guard_name' => 'web']);
        Permission::create(['name' => 'update projects', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete projects', 'guard_name' => 'web']);
        
        // Create super admin role
        $role = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $role->givePermissionTo(['view any projects', 'view projects', 'create projects', 'update projects', 'delete projects']);
    }

    /**
     * Test get all projects.
     *
     * @return void
     */
    public function test_get_all_projects()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        // Create client first
        $client = Client::factory()->create();
        
        // Create projects
        Project::factory()->count(3)->create(['client_id' => $client->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /**
     * Test create a project.
     *
     * @return void
     */
    public function test_create_project()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        // Create client first
        $client = Client::factory()->create();

        $projectData = [
            'client_id' => $client->id,
            'name' => 'Test Project',
            'analyst' => 'Test Analyst',
            'grade' => 'A',
            'roles' => [
                ['name' => 'Developer'],
                ['name' => 'Designer']
            ]
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/projects', $projectData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('projects', [
            'client_id' => $client->id,
            'name' => 'Test Project',
            'analyst' => 'Test Analyst',
            'grade' => 'A'
        ]);
    }

    /**
     * Test get single project.
     *
     * @return void
     */
    public function test_get_single_project()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/projects/{$project->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $project->id,
                'name' => $project->name
            ]);
    }

    /**
     * Test update a project.
     *
     * @return void
     */
    public function test_update_project()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        $updatedData = [
            'name' => 'Updated Project Name',
            'grade' => 'B'
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/projects/{$project->id}", $updatedData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('projects', $updatedData);
    }

    /**
     * Test delete a project.
     *
     * @return void
     */
    public function test_delete_project()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}