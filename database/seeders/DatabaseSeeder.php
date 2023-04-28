<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // default akun admin
        $admin = User::create([
            'email' => 'admin@gmail.com',
            'name' => 'admin',
            'password' => Hash::make(123),
            'email_verified_at' => now(),
            'status' => User::DITERIMA,
        ]);
        // default akun user (blm verified email)
        $user = User::create([
            'email' => 'user@gmail.com',
            'name' => 'user',
            'password' => Hash::make(123),
            'status' => User::PENGAJUAN,
            'otp' => 111111
        ]);
        

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        $admin->assignRole('admin');
        $user->assignRole('user');
    }
}
