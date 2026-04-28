<?php

namespace Database\Seeders;

use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Staff — password login
        User::firstOrCreate(['email' => 'admin@insurance.ae'], [
            'name'        => 'Platform Administrator',
            'password'    => Hash::make('Admin@1234'),
            'role'        => 'admin',
            'is_active'   => true,
            'is_verified' => true,
        ]);

        User::firstOrCreate(['email' => 'agent@insurance.ae'], [
            'name'        => 'Insurance Agent',
            'password'    => Hash::make('Agent@1234'),
            'role'        => 'agent',
            'is_active'   => true,
            'is_verified' => true,
        ]);

        User::firstOrCreate(['email' => 'auditor@insurance.ae'], [
            'name'        => 'Compliance Auditor',
            'password'    => Hash::make('Auditor@1234'),
            'role'        => 'auditor',
            'is_active'   => true,
            'is_verified' => true,
        ]);

        // Customer — magic link only (no password)
        User::firstOrCreate(['email' => 'sangharshsulke@gmail.com'], [
            'name'        => 'Sample Customer',
            'password'    => null,
            'role'        => 'customer',
            'is_active'   => true,
            'is_verified' => true,
        ]);

        // Chat rooms
        $rooms = [
            ['name' => 'General',      'type' => 'general',      'description' => 'General discussion for all staff'],
            ['name' => 'Claims',       'type' => 'claims',       'description' => 'Claims processing and updates'],
            ['name' => 'Underwriting', 'type' => 'underwriting', 'description' => 'Underwriting team channel'],
            ['name' => 'Support',      'type' => 'support',      'description' => 'Customer support escalations'],
        ];

        foreach ($rooms as $room) {
            ChatRoom::firstOrCreate(['name' => $room['name']], $room);
        }
    }
}