<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Amina Rahimi',
                'email' => 'admin@factoryerp.test',
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'password' => Hash::make('Admin@12345'),
            ],
            [
                'name' => 'Farid Noori',
                'email' => 'manager@factoryerp.test',
                'role' => User::ROLE_MANAGER,
                'status' => User::STATUS_ACTIVE,
                'password' => Hash::make('Manager@12345'),
            ],
            [
                'name' => 'Laila Safi',
                'email' => 'sales@factoryerp.test',
                'role' => User::ROLE_SALES,
                'status' => User::STATUS_ACTIVE,
                'password' => Hash::make('Sales@12345'),
            ],
            [
                'name' => 'Zia Omari',
                'email' => 'production@factoryerp.test',
                'role' => User::ROLE_PRODUCTION,
                'status' => User::STATUS_ACTIVE,
                'password' => Hash::make('Production@12345'),
            ],
        ];

        foreach ($users as $attributes) {
            User::query()->updateOrCreate(
                ['email' => $attributes['email']],
                $attributes,
            );
        }
    }
}
