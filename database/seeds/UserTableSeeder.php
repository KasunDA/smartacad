<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_types')->insert([
            ['user_type' => 'Super Admin'], ['user_type' => 'Admin']
        ]);

        DB::table('users')->insert([
            'email' => 'admin@gmail.com',
            'first_name' => 'Emmanuel',
            'last_name'=> 'Okafor',
            'phone_no' => '08061539278',
            'verified' => 1,
            'status' => 1,
            'user_type_id' => 1,
            'password' => Hash::make('password')
        ]);
    }
}
