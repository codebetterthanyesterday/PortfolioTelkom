<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        for ($i=1; $i <= 6 ; $i++) { 
            User::create([
                'email' => 'admin'.$i.'@telkom.ac.id',
                'username' => 'admin'.$i,
                'phone_number' => null,
                'full_name' => null,
                'password' => Hash::make('Admin123'),
                'role' => 'admin',
                'avatar' => null,
                'short_about' => 'System Administrator',
                'about' => 'Administrator of Telkom Project Gallery',
                'email_verified_at' => now(),
            ]);
        }



        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@telkom.ac.id');
        $this->command->info('Password: Admin123');
    }
}
