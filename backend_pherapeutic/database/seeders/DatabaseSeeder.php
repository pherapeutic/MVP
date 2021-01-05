<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
		    'first_name' => 'Admin',
		    'email' => 'admin1@yopmail.com',
            'password' => Hash::make('string1'),
            'role' => '2'
		]);
    }
}
