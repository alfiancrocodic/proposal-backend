<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClientTest extends TestCase
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
        Permission::create(['name' => 'view any clients', 'guard_name' => 'web']);
        Permission::create(['name' => 'view clients', 'guard_name' => 'web']);
        Permission::create(['name' => 'create clients', 'guard_name' => 'web']);
        Permission::create(['name' => 'update clients', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete clients', 'guard_name' => 'web']);
        
        // Create super admin role
        $role = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $role->givePermissionTo(['view any clients', 'view clients', 'create clients', 'update clients', 'delete clients']);
    }

    /**
     * Test get all clients.
     *
     * @return void
     */
    public function test_get_all_clients()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        Client::factory()->count(5)->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/clients');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    /**
     * Test create a client.
     *
     * @return void
     */
    public function test_create_client()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $clientData = [
            'company' => 'Test Company',
            'location' => 'Test Location',
            'badanUsaha' => 'PT',
            'picName' => 'Test PIC',
            'position' => 'Test Position',
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/clients', $clientData);

        $response->assertStatus(201)
            ->assertJsonFragment($clientData);

        $this->assertDatabaseHas('clients', $clientData);
    }

    /**
     * Test update a client.
     *
     * @return void
     */
    public function test_update_client()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $client = Client::factory()->create();

        $updatedData = [
            'company' => 'Updated Company Name',
            'location' => 'Updated Location',
        ];

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/clients/{$client->id}", $updatedData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('clients', $updatedData);
    }

    /**
     * Test delete a client.
     *
     * @return void
     */
    public function test_delete_client()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $client = Client::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/clients/{$client->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }
}
