<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProposalTest extends TestCase
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
        Permission::create(['name' => 'view any proposals', 'guard_name' => 'web']);
        Permission::create(['name' => 'view proposals', 'guard_name' => 'web']);
        Permission::create(['name' => 'create proposals', 'guard_name' => 'web']);
        Permission::create(['name' => 'update proposals', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete proposals', 'guard_name' => 'web']);
        
        // Create super admin role
        $role = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $role->givePermissionTo(['view any proposals', 'view proposals', 'create proposals', 'update proposals', 'delete proposals']);
    }

    /**
     * Test get all proposals for a project.
     *
     * @return void
     */
    public function test_get_all_proposals_for_project()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        // Create client and project first
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);
        
        // Create proposals
        Proposal::factory()->count(3)->create(['project_id' => $project->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/projects/{$project->id}/proposals");

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /**
     * Test create a proposal.
     *
     * @return void
     */
    public function test_create_proposal()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        // Create client and project first
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        $proposalData = [
            'project_id' => $project->id,
            'version' => 1
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/proposals', $proposalData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('proposals', [
            'project_id' => $project->id,
            'version' => 1
        ]);
    }

    /**
     * Test get single proposal.
     *
     * @return void
     */
    public function test_get_single_proposal()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);
        $proposal = Proposal::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/proposals/{$proposal->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $proposal->id,
                'version' => $proposal->version
            ]);
    }

    /**
     * Test update a proposal.
     *
     * @return void
     */
    public function test_update_proposal()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);
        $proposal = Proposal::factory()->create(['project_id' => $project->id]);

        $updatedData = [
            'version' => 2
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/proposals/{$proposal->id}", $updatedData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('proposals', $updatedData);
    }

    /**
     * Test delete a proposal.
     *
     * @return void
     */
    public function test_delete_proposal()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);
        $proposal = Proposal::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/proposals/{$proposal->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('proposals', ['id' => $proposal->id]);
    }

    /**
     * Test create proposal for specific project.
     *
     * @return void
     */
    public function test_create_proposal_for_project()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        $proposalData = [
            'version' => 1
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/projects/{$project->id}/proposals", $proposalData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('proposals', [
            'project_id' => $project->id,
            'version' => 1
        ]);
    }
}