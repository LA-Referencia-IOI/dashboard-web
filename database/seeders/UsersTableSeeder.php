<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = bcrypt('password');

        User::create([
            'profile' => UserType::Administrator,
            'name' => 'Administrador',
            'email' => 'admin@seed.com',
            'password' => $password
        ]);

        User::create([
            'profile' => UserType::Manager,
            'name' => 'Gerente',
            'email' => 'manager@seed.com',
            'password' => $password
        ]);

        User::create([
            'profile' => UserType::User,
            'name' => 'Usuário',
            'email' => 'user@seed.com',
            'password' => $password
        ]);
    }
}
