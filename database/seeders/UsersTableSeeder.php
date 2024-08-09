<?php

namespace Database\Seeders;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleuser = Roles::where('name', 'admin')->first();
        User::create(
            [
                'name' => 'irfan',
                'email' => 'muhammadirfan.9f@gmail.com',
                'password' => Hash::make('12345678'),
                'role_id' => $roleuser->id,
            ]
        );
        $roleuser = Roles::where('name', 'user')->first();
        User::create(
            [
                'name' => 'user',
                'email' => 'user@mail.com',
                'password' => Hash::make('12345678'),
                'role_id' => $roleuser->id,
            ]
        );
    }
}
