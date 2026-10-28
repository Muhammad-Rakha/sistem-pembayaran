<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run order matters:
     *  1. RoleUserSeeder  — creates the 4 Spatie roles + staff demo accounts
     *  2. DemoDataSeeder  — creates jurusan, kelas, tarif, siswa, VA, tagihan
     *                       (also creates siswa login users + assigns 'siswa' role)
     */
    public function run(): void
    {
        $this->call([
            RoleUserSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
