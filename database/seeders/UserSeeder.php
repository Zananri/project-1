<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Pemohon',
                'email' => 'pemohon@perusahaan.com',
                'password' => Hash::make('password'),
                'role' => 'pemohon',
                'jabatan' => 'Staff',
                'divisi' => 'Keuangan',
            ],
            [
                'name' => 'Pejabat Satu',
                'email' => 'pejabat1@perusahaan.com',
                'password' => Hash::make('password'),
                'role' => 'pejabat_1',
                'jabatan' => 'Manager',
                'divisi' => 'Keuangan',
            ],
            [
                'name' => 'Pejabat Dua',
                'email' => 'pejabat2@perusahaan.com',
                'password' => Hash::make('password'),
                'role' => 'pejabat_2',
                'jabatan' => 'Senior Manager',
                'divisi' => 'Keuangan',
            ],
            [
                'name' => 'Pejabat Tiga',
                'email' => 'pejabat3@perusahaan.com',
                'password' => Hash::make('password'),
                'role' => 'pejabat_3',
                'jabatan' => 'General Manager',
                'divisi' => 'Keuangan',
            ],
            [
                'name' => 'Pejabat Empat',
                'email' => 'pejabat4@perusahaan.com',
                'password' => Hash::make('password'),
                'role' => 'pejabat_4',
                'jabatan' => 'Business Executive',
                'divisi' => 'Eksekutif',
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
