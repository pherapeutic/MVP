<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Hash;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert([
		    'first_name' => 'Parmod',
            'last_name' => 'kumar',
		    'email' => 'admin@itechnolab.com',
		    'password' => Hash::make('12345678')
		]);

        //php artisan db:seed --class=UsersTableSeeder
    }
}
