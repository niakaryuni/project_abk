<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Abk;
use App\Models\Nilai;

class AbkSeeder extends Seeder
{
    public function run(): void
    {
        $siswa = [
            ['nama' => 'Ahmad Fauzi',    'umur' => 9,  'kelas' => '3A', 'keterangan' => 'Autisme ringan'],
            ['nama' => 'Siti Rahma',     'umur' => 8,  'kelas' => '2B', 'keterangan' => 'Tunarungu'],
            ['nama' => 'Budi Santoso',   'umur' => 11, 'kelas' => '5A', 'keterangan' => 'Tunagrahita ringan'],
            ['nama' => 'Dewi Lestari',   'umur' => 10, 'kelas' => '4B', 'keterangan' => 'ADHD'],
            ['nama' => 'Rizky Pratama',  'umur' => 7,  'kelas' => '1A', 'keterangan' => 'Lambat belajar'],
        ];

        $aspeks = ['motorik', 'keterampilan', 'sosial'];
        $mapel  = ['Matematika', 'Bahasa Indonesia', 'Seni Budaya', 'Olahraga', 'IPA'];

        foreach ($siswa as $s) {
            $abk = Abk::create($s);

            // Tambah beberapa data nilai
            for ($i = 0; $i < 6; $i++) {
                Nilai::create([
                    'abk_id'         => $abk->id,
                    'mata_pelajaran' => $mapel[array_rand($mapel)],
                    'aspek'          => $aspeks[$i % 3],
                    'nilai'          => rand(60, 95),
                    'catatan'        => 'Perkembangan baik, perlu pendampingan lebih lanjut.',
                    'tanggal'        => now()->subDays(rand(1, 60))->format('Y-m-d'),
                ]);
            }
        }
    }
}