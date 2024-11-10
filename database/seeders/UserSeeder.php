<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminPermission = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'kasir']);
        Role::firstOrCreate(['name' => 'toko']);

        $users = [
            [
                'name' => 'fauzan',
                'email' => 'fauzan@gmail.com',
                'password' => Hash::make('fauzan1234'),
                'status' => 1,
            ],
            [
                'name' => 'rafi',
                'email' => 'rafi@gmail.com',
                'password' => Hash::make('rafi1234'),
                'status' => 1,
            ],
            [
                'name' => 'salma',
                'email' => 'salma@gmail.com',
                'password' => Hash::make('salma1234'),
                'status' => 1,
            ],
            [
                'name' => 'lia',
                'email' => 'lia@gmail.com',
                'password' => Hash::make('lia12345'),
                'status' => 1,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
            $user = User::create(attributes: $user);
            $user->givePermissionTo($adminPermission);
        }
    }
}
