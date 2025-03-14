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
            // 🔹 Proyek Pemasangan & Instalasi HVAC
            ['project_name' => 'Instalasi Sistem HVAC Gedung Perkantoran', 'description' => 'Proyek pemasangan sistem HVAC untuk gedung perkantoran baru'],
            ['project_name' => 'Instalasi Chiller untuk Pabrik', 'description' => 'Proyek pemasangan chiller untuk kebutuhan pendinginan di pabrik'],
            ['project_name' => 'Pemasangan AC VRF di Hotel', 'description' => 'Proyek instalasi sistem VRF untuk meningkatkan efisiensi pendinginan di hotel'],
            ['project_name' => 'Penggantian Cooling Tower di Rumah Sakit', 'description' => 'Proyek peremajaan cooling tower untuk sistem pendinginan rumah sakit'],
            ['project_name' => 'Pemasangan AHU dan FCU di Mall', 'description' => 'Proyek pemasangan Air Handling Unit dan Fan Coil Unit untuk pusat perbelanjaan'],
            ['project_name' => 'Instalasi Ventilasi Industri', 'description' => 'Proyek pemasangan sistem ventilasi untuk area produksi industri'],

            // 🔹 Proyek Pemeliharaan & Perawatan HVAC
            ['project_name' => 'Maintenance HVAC Tahunan', 'description' => 'Proyek perawatan berkala sistem HVAC di berbagai gedung'],
            ['project_name' => 'Optimasi Efisiensi Energi HVAC', 'description' => 'Proyek peningkatan efisiensi energi dalam sistem pendinginan dan pemanasan'],
            ['project_name' => 'Pembersihan dan Kalibrasi Ducting', 'description' => 'Proyek pembersihan saluran udara dan kalibrasi untuk menjaga kualitas udara'],
            ['project_name' => 'Peningkatan Kualitas Udara dalam Ruangan (IAQ)', 'description' => 'Proyek pemasangan HEPA filter dan purifikasi udara untuk meningkatkan kualitas udara'],
            ['project_name' => 'Peremajaan Sistem HVAC Gedung Lama', 'description' => 'Proyek penggantian dan peningkatan sistem HVAC di gedung lama'],

            // 🔹 Proyek Inovasi & Pengembangan Teknologi HVAC
            ['project_name' => 'Implementasi Sistem Smart HVAC', 'description' => 'Proyek penerapan IoT dan sensor cerdas dalam sistem HVAC'],
            ['project_name' => 'Pengembangan Sistem Otomasi HVAC', 'description' => 'Proyek otomatisasi pengaturan suhu dan kelembaban berbasis AI'],
            ['project_name' => 'Penggunaan Refrigeran Ramah Lingkungan', 'description' => 'Proyek penggantian refrigeran dengan bahan yang lebih ramah lingkungan'],
            ['project_name' => 'Uji Coba Sistem Pendingin Hemat Energi', 'description' => 'Proyek penelitian dan pengujian sistem pendingin dengan konsumsi energi lebih rendah'],
            ['project_name' => 'Peningkatan Efisiensi Thermal dalam Bangunan', 'description' => 'Proyek optimalisasi isolasi termal untuk mengurangi beban HVAC'],

            // 🔹 Proyek HVAC untuk Sektor Khusus
            ['project_name' => 'Instalasi HVAC untuk Cold Storage', 'description' => 'Proyek pemasangan sistem pendinginan untuk penyimpanan bahan makanan dan farmasi'],
            ['project_name' => 'Sistem HVAC untuk Data Center', 'description' => 'Proyek pemasangan HVAC dengan kontrol kelembaban dan suhu presisi di data center'],
            ['project_name' => 'Pemasangan HVAC di Rumah Sakit', 'description' => 'Proyek instalasi sistem pendinginan dan ventilasi khusus untuk rumah sakit'],
            ['project_name' => 'Ventilasi dan Pengaturan Udara di Pabrik Kimia', 'description' => 'Proyek pemasangan sistem ventilasi yang sesuai dengan standar industri kimia'],
        ]);
    }
}
