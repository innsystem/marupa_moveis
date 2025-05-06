<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'user_group_id' => 1, // Developer
            'name' => 'LeoDev',
            'email' => 'developer@innsystem.com.br',
            'password' => Hash::make('123456'),
            'password_code' => null,
            'document' => null,
            'phone' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'user_group_id' => 2, // Admin
            'name' => 'Leo Admin',
            'email' => 'contato@innsystem.com.br',
            'password' => Hash::make('123456'),
            'password_code' => null,
            'document' => null,
            'phone' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'user_group_id' => 3, // Customer
            'name' => 'Leo Cliente',
            'email' => 'leozinnn.ecko@gmail.com',
            'password' => Hash::make('123456'),
            'password_code' => null,
            'document' => '415.524.658-96',
            'phone' => '55 16 992747526',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
