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
            'name' => 'Administrator',
            'email' => 'admin@darkpid.com',
            'password' => $password
        ]);

        User::create([
            'profile' => UserType::Instituition,
            'name' => 'Instituition',
            'email' => 'instituition@darkpid.com',
            'password' => $password
        ]);

        User::create([
            'profile' => UserType::User,
            'name' => 'User',
            'email' => 'user@darkpid.com',
            'password' => $password
        ]);
    }
}
