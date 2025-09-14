<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AditUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create adit@crocodic.com user
        $user = User::firstOrCreate(
            ['email' => 'adit@crocodic.com'],
            [
                'name' => 'Adit Crocodic',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        // Assign user role (which has create_proposal permission)
        $user->assignRole('user');
        
        echo "User adit@crocodic.com created successfully with 'user' role\n";
    }
}