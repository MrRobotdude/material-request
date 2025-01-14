<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        DB::table('projects')->insert([
            ['project_name' => 'Pengembangan Server', 'description' => 'Proyek pengembangan server utama'],
            ['project_name' => 'Pembangunan Gedung', 'description' => 'Proyek pembangunan gedung baru'],
            ['project_name' => 'Digital Transformation', 'description' => 'Proyek transformasi digital'],
            ['project_name' => 'Implementasi ERP', 'description' => 'Proyek implementasi sistem ERP perusahaan'],
            ['project_name' => 'Modernisasi Infrastruktur', 'description' => 'Proyek modernisasi infrastruktur IT'],
            ['project_name' => 'Pengembangan Aplikasi Mobile', 'description' => 'Proyek pengembangan aplikasi untuk pengguna mobile'],
            ['project_name' => 'Analisis Data Perusahaan', 'description' => 'Proyek pengolahan dan analisis data perusahaan'],
            ['project_name' => 'Migrasi Data', 'description' => 'Proyek migrasi data dari sistem lama'],
            ['project_name' => 'Peningkatan Keamanan', 'description' => 'Proyek peningkatan keamanan sistem perusahaan'],
            ['project_name' => 'Automasi Proses Bisnis', 'description' => 'Proyek automasi berbagai proses bisnis'],
            ['project_name' => 'Peningkatan Sistem Manajemen', 'description' => 'Proyek perbaikan sistem manajemen'],
            ['project_name' => 'Pengembangan Jaringan', 'description' => 'Proyek pengembangan jaringan perusahaan'],
            ['project_name' => 'Desain Ulang Website', 'description' => 'Proyek redesign website perusahaan'],
            ['project_name' => 'Pengembangan Chatbot', 'description' => 'Proyek pengembangan chatbot untuk layanan pelanggan'],
            ['project_name' => 'Pelatihan Karyawan', 'description' => 'Proyek pelatihan karyawan terkait sistem baru'],
            ['project_name' => 'Penelitian Pasar', 'description' => 'Proyek penelitian pasar untuk pengembangan produk'],
            ['project_name' => 'Optimalisasi Produksi', 'description' => 'Proyek optimalisasi proses produksi'],
            ['project_name' => 'Pengembangan CRM', 'description' => 'Proyek pengembangan sistem manajemen hubungan pelanggan'],
            ['project_name' => 'Pembangunan Data Center', 'description' => 'Proyek pembangunan data center baru'],
            ['project_name' => 'Pengembangan Sistem Cloud', 'description' => 'Proyek pengembangan sistem berbasis cloud'],
        ]);
    }
}
