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

        User::firstOrCreate(
            ['email' => 'admin@darkpid.com'],
            ['profile' => UserType::Administrator, 'name' => 'Administrator', 'password' => $password]
        );

        User::firstOrCreate(
            ['email' => 'instituition@darkpid.com'],
            ['profile' => UserType::Instituition, 'name' => 'Instituition', 'password' => $password]
        );

        User::firstOrCreate(
            ['email' => 'user@darkpid.com'],
            ['profile' => UserType::User, 'name' => 'User', 'password' => $password]
        );
    }
}
