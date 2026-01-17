<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Создаем базовые роли
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Администратор']);
        $userRole = Role::firstOrCreate(['slug' => 'user'], ['name' => 'Пользователь']);

        // Создаем или находим админа
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Привязываем роль админа, если еще не привязана
        if (!$admin->roles()->where('slug', 'admin')->exists()) {
            $admin->roles()->attach($adminRole);
        }
    }
}
