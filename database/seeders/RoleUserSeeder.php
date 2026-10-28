<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['administrator', 'kepala_sekolah', 'teller', 'siswa'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Student accounts are created by DemoDataSeeder using the student's
        // NIS as the username (YYJNN format), so only staff accounts live here.
        $users = [
            // [nama, username, role]
            ['Administrator', 'admin',   'administrator'],
            ['Kepala Sekolah', 'kepsek', 'kepala_sekolah'],
            ['Teller',        'teller',  'teller'],
        ];

        foreach ($users as [$nama, $username, $role]) {
            $user = User::firstOrCreate(
                ['username' => $username],
                ['nama' => $nama, 'password' => 'password', 'status' => 'aktif']
            );
            $user->assignRole($role);
        }
    }
}
