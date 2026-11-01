<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@skhn01.sch.id'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );

        // Guru
        User::firstOrCreate(
            ['email' => 'guru@skhn01.sch.id'],
            [
                'name'     => 'Guru',
                'password' => Hash::make('password'),
            ]
        );

        $this->call(AbkSeeder::class);
    }
}