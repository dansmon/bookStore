<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'emso' => '0911991500459',
            'password' => bcrypt('geslo123'),
            'is_admin' => 1,
            'is_user' => 0
        ]);
    }
}
