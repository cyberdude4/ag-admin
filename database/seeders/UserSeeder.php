<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserSeeder extends Seeder
{

    public function run(){
        DB::table('users')->insert(
            [
                'firstname' => 'Admin',
                'lastname' => 'User',
                'mobile' => '1111111111',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin@password'),
                'first_ip' => '127.0.0.1',
                'last_ip' => '127.0.0.1',
                'first_login' => Carbon::now(),
                'last_login' => Carbon::now(),
            ],
            [
                'firstname' => 'Vishal',
                'lastname' => 'Bondre',
                'mobile' => '9158822456',
                'email' => 'vishalworkstation@gmail.com',
                'password' => Hash::make('vishalvishal'),
                'first_ip' => '127.0.0.1',
                'last_ip' => '127.0.0.1',
                'first_login' => Carbon::now(),
                'last_login' => Carbon::now(),
            ]
        );
    }

}
