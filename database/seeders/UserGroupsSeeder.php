<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_groups')->insert([
            'name' => 'Developer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_groups')->insert([
            'name' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_groups')->insert([
            'name' => 'Customer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
