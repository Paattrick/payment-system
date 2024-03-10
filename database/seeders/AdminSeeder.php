<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'last_name' => 'admin',
            'middle_name' => 'admin',
            'contact_number' => 'admin',
            'gender' => 'admin',
            'province' => 'admin',
            'municipality' => 'admin',
            'barangay' => 'admin',
            'password' => Hash::make('admin@admin'),
        ]);

        $user->assignRole('admin');
    }
}
