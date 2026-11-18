<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun dosen default.
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Mahasiswa default.
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name' => 'Ahmad Pratama',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210001',
            ]
        );

        // 3. Mahasiswa tambahan untuk menguji rekap beberapa mahasiswa.
        $mahasiswa2 = User::updateOrCreate(
            ['email' => 'siti@celoe.test'],
            [
                'name' => 'Siti Rahmawati',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210002',
            ]
        );

        // 4. Mata kuliah utama.
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mempelajari pengembangan aplikasi web menggunakan Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 5. Mata kuliah tambahan dengan semester dan SKS berbeda.
        $course2 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF302'],
            [
                'nama_mk' => 'Basis Data Lanjut',
                'deskripsi' => 'Mempelajari perancangan basis data, relasi, query, dan optimasi database.',
                'sks' => 3,
                'semester' => 'Genap 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        $course3 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF303'],
            [
                'nama_mk' => 'Rekayasa Perangkat Lunak',
                'deskripsi' => 'Mempelajari proses analisis, perancangan, pengembangan, dan pengujian perangkat lunak.',
                'sks' => 2,
                'semester' => 'Ganjil 2027/2028',
                'dosen_id' => $dosen->id,
            ]
        );

        // 6. Pendaftaran mahasiswa berstatus approved.
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
            $mahasiswa2->id => ['status' => 'approved'],
        ]);

        $course2->students()->syncWithoutDetaching([
            $mahasiswa2->id => ['status' => 'approved'],
        ]);

        // 7. Materi contoh untuk mata kuliah pertama.
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan View Blade.',
            ]
        );

        // 8. Dua tugas contoh pada dua mata kuliah berbeda.
        $assignment1 = Assignment::updateOrCreate(
            [
                'course_id' => $course1->id,
                'judul' => 'Implementasi Routing dan Middleware',
            ],
            [
                'deskripsi' => 'Buat contoh route Laravel yang menggunakan middleware auth dan role untuk membatasi akses pengguna.',
                'deadline' => now()->addDays(14),
            ]
        );

        $assignment2 = Assignment::updateOrCreate(
            [
                'course_id' => $course2->id,
                'judul' => 'Perancangan Basis Data',
            ],
            [
                'deskripsi' => 'Buat rancangan basis data sederhana dan jelaskan relasi antar tabelnya.',
                'deadline' => now()->addDays(14),
            ]
        );

        // 9. Contoh pengumpulan tugas dengan nilai beragam.
        Submission::updateOrCreate(
            [
                'assignment_id' => $assignment1->id,
                'user_id' => $mahasiswa1->id,
            ],
            [
                'jawaban' => 'Saya menggunakan middleware auth untuk memeriksa login dan role untuk membatasi akses berdasarkan peran pengguna.',
                'submitted_at' => now()->subDays(2),
                'nilai' => 85,
                'catatan_dosen' => 'Penjelasan sudah baik. Tambahkan contoh pengujian akses untuk setiap role.',
            ]
        );

        Submission::updateOrCreate(
            [
                'assignment_id' => $assignment1->id,
                'user_id' => $mahasiswa2->id,
            ],
            [
                'jawaban' => 'Middleware digunakan untuk memeriksa permintaan sebelum diteruskan ke controller aplikasi.',
                'submitted_at' => now()->subDay(),
                'nilai' => 68,
                'catatan_dosen' => 'Jawaban perlu dilengkapi dengan contoh penerapan role middleware.',
            ]
        );

        Submission::updateOrCreate(
            [
                'assignment_id' => $assignment2->id,
                'user_id' => $mahasiswa2->id,
            ],
            [
                'jawaban' => 'Rancangan basis data terdiri dari tabel mahasiswa, mata kuliah, dan tabel pendaftaran sebagai penghubung.',
                'submitted_at' => now(),
                'nilai' => null,
                'catatan_dosen' => null,
            ]
        );
    }
}