<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@mitienda.pe',
            'password' => Hash::make('password123'),
            'phone' => '999999999',
            'role' => 'admin',
            'is_active' => true
        ]);

        User::create([
            'name' => 'Empleado Demo',
            'email' => 'empleado@mitienda.pe',
            'password' => Hash::make('password123'),
            'phone' => '988888888',
            'role' => 'employee',
            'is_active' => true
        ]);
    }
}
