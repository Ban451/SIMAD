<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kelas;
use App\Models\KategoriDokumen;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Kelas
        $kelas = [
            ['nama_kelas' => 'X RPL A', 'tingkat' => 'X', 'jurusan' => 'RPL'],
            ['nama_kelas' => 'X RPL B', 'tingkat' => 'X', 'jurusan' => 'RPL'],
            ['nama_kelas' => 'XI RPL A', 'tingkat' => 'XI', 'jurusan' => 'RPL'],
            ['nama_kelas' => 'XI RPL B', 'tingkat' => 'XI', 'jurusan' => 'RPL'],
            ['nama_kelas' => 'XII RPL A', 'tingkat' => 'XII', 'jurusan' => 'RPL'],
            ['nama_kelas' => 'XII RPL B', 'tingkat' => 'XII', 'jurusan' => 'RPL'],
        ];

        foreach ($kelas as $k) {
            Kelas::create($k);
        }

        // Buat Kategori Dokumen
        $kategori = [
            ['nama_kategori' => 'Rapor', 'kode' => 'RAPOR'],
            ['nama_kategori' => 'Ijazah', 'kode' => 'IJAZAH'],
            ['nama_kategori' => 'Sertifikat TOEIC', 'kode' => 'TOEIC'],
            ['nama_kategori' => 'Sertifikat UKK', 'kode' => 'UKK'],
            ['nama_kategori' => 'Sertifikat PKL', 'kode' => 'PKL'],
        ];

        foreach ($kategori as $k) {
            KategoriDokumen::create($k);
        }

        // Buat Admin
        User::create([
            'nama' => 'Admin SIMAD',
            'email' => 'admin@simad.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Buat Guru
        User::create([
            'nama' => 'Guru Wali Kelas',
            'email' => 'guru@simad.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'kelas_id' => 1,
        ]);

        // Buat Siswa
        User::create([
            'nama' => 'Amadeo Brian R.P',
            'nis' => '1234567890',
            'password' => Hash::make('1234567890'),
            'role' => 'siswa',
            'kelas_id' => 1,
        ]);

        // Tambahan siswa lain
        User::create([
            'nama' => 'Ahmad Fauzi',
            'nis' => '1234567891',
            'password' => Hash::make('1234567891'),
            'role' => 'siswa',
            'kelas_id' => 1,
        ]);

        User::create([
            'nama' => 'Siti Rahayu',
            'nis' => '1234567892',
            'password' => Hash::make('1234567892'),
            'role' => 'siswa',
            'kelas_id' => 2,
        ]);
    }
}