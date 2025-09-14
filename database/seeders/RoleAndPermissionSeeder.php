<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Membuat permissions dengan format Filament Shield (underscore)
        $permissions = [
            // Users permissions
            'view_any_user', 'view_user', 'create_user', 'update_user', 'delete_user', 'restore_user', 'force_delete_user',
            // Roles permissions
            'view_any_role', 'view_role', 'create_role', 'update_role', 'delete_role', 'delete_any_role', 'restore_role', 'restore_any_role', 'force_delete_role', 'force_delete_any_role', 'replicate_role', 'reorder_role',
            // Permissions permissions
            'view_any_permission', 'view_permission', 'create_permission', 'update_permission', 'delete_permission', 'delete_any_permission', 'restore_permission', 'restore_any_permission', 'force_delete_permission', 'force_delete_any_permission', 'replicate_permission', 'reorder_permission',
            // Clients permissions
            'view_any_client', 'view_client', 'create_client', 'update_client', 'delete_client', 'restore_client', 'force_delete_client',
            // Projects permissions
            'view_any_project', 'view_project', 'create_project', 'update_project', 'delete_project', 'restore_project', 'force_delete_project',
            // Proposals permissions
            'view_any_proposal', 'view_proposal', 'create_proposal', 'update_proposal', 'delete_proposal', 'restore_proposal', 'force_delete_proposal',
            // Proposal Contents permissions
            'view_any_proposal_content', 'view_proposal_content', 'create_proposal_content', 'update_proposal_content', 'delete_proposal_content', 'restore_proposal_content', 'force_delete_proposal_content',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // create roles and assign created permissions

        $role = Role::firstOrCreate(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());

        // Membuat role user dengan permission terbatas
        $role = Role::firstOrCreate(['name' => 'user'])
            ->givePermissionTo([
                'view_any_user', 'view_user',
                'view_any_client', 'view_client', 'create_client', 'update_client',
                'view_any_project', 'view_project', 'create_project', 'update_project',
                'view_any_proposal', 'view_proposal', 'create_proposal', 'update_proposal',
                'view_any_proposal_content', 'view_proposal_content', 'create_proposal_content', 'update_proposal_content'
            ]);

        // Membuat role Analis dengan permission CRUD untuk Client, Project, dan Proposal
        $role = Role::firstOrCreate(['name' => 'Analis'])
            ->givePermissionTo([
                'view_any_client', 'view_client', 'create_client', 'update_client', 'delete_client',
                'view_any_project', 'view_project', 'create_project', 'update_project', 'delete_project',
                'view_any_proposal', 'view_proposal', 'create_proposal', 'update_proposal', 'delete_proposal',
                'view_any_proposal_content', 'view_proposal_content', 'create_proposal_content', 'update_proposal_content', 'delete_proposal_content'
            ]);
    }
}
