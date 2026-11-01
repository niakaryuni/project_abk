# SIMBA ABK

Sistem Informasi Monitoring Perkembangan Belajar Anak Berkebutuhan Khusus (ABK), dibangun sebagai tugas akhir untuk SKHN 01 Kabupaten Tangerang.

## Fitur
- Autentikasi dengan 4 peran: Admin, Guru, Kepala Sekolah, Orang Tua
- Pengelolaan data siswa dan penilaian (perkembangan dan akademik)
- Dashboard statistik dan grafik tren perkembangan
- Laporan otomatis dan ekspor PDF

## Teknologi
Laravel, PHP, MySQL, Chart.js, laravel-dompdf

## Cara Menjalankan
1. `git clone https://github.com/niakaryuni/project_abk.git`
2. `cd project_abk`
3. `composer install`
4. Salin `.env.example` menjadi `.env`, lalu atur koneksi database
5. `php artisan key:generate`
6. `php artisan migrate --seed`
7. `php artisan serve`

## Catatan
Data pada repositori ini hanya data contoh. Data asli siswa tidak disertakan.
