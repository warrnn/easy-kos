<?php

namespace Database\Seeders;

use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pengguna')->insert([
            [
                'id' => 1,
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'id_role' => 1
            ],
            [
                'id' => 2,
                'username' => 'Adit',
                'password' => Hash::make('1234'),
                'id_role' => 2
            ],
            [
                'id' => 3,
                'username' => 'Budi',
                'password' => Hash::make('1234'),
                'id_role' => 3
            ]
        ]);
    }
}
