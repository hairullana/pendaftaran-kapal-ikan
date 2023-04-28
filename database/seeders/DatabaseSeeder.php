<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
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

        // role nya
        $role_admin = Role::create(['name' => 'admin']);
        $role_user = Role::create(['name' => 'user']);

        // assign
        $admin->assignRole('admin');
        $user->assignRole('user');

        // permission
        $list_new_user = Permission::create(['name' => 'list new user']); // cek list user yang baru daftar
        $acc_user = Permission::create(['name' => 'acc user']); // acc atau tolak user

        // give permission to role
        // $role_admin->givePermissionTo($list_new_user);
        // $role_admin->givePermissionTo($acc_user);
    }
}
