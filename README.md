# SIP (Sistem Informasi Penjadwalan)

SIP adalah sistem yang dirancang untuk membantu MIN 2 Pontianak dalam membuat dan mengelola penjadwalan mata pelajaran secara efisien. Dengan sistem ini, admin dan wakil kepala kurikulum dapat dengan mudah mengatur jadwal pelajaran, mengoptimalkan waktu, dan meningkatkan pengalaman belajar bagi siswa.

## Fitur Utama

-   **Pengelolaan Mata Pelajaran**: Tambah, edit, dan hapus mata pelajaran.
-   **Penjadwalan Otomatis**: Sistem otomatis untuk membuat jadwal berdasarkan mata pelajaran dan ketersediaan ruang kelas.
-   **Manajemen Pengguna**: Pengaturan akses untuk admin, wakil kepala kurikulum dan guru.
-   **Laporan**: Fitur untuk mencetak dan mendownload laporan jadwal pelajaran.

## Algoritma Welch-Powell

Sistem ini menggunakan algoritma Welch-Powell untuk menghasilkan jadwal mata pelajaran. Algoritma ini membantu dalam menghindari bentrokan jadwal antara kelas dan guru dengan cara mengoptimalkan penjadwalan berdasarkan ketersediaan.

### Manfaat Penggunaan Algoritma Welch-Powell

-   **Efisiensi**: Mengurangi waktu yang dibutuhkan untuk membuat jadwal.
-   **Keadilan**: Menjamin bahwa semua kelas dan guru mendapatkan waktu yang adil dalam jadwal.
-   **Optimalisasi**: Memaksimalkan penggunaan ruang kelas dan menghindari tumpang tindih antar jadwal.

### Implementasi

Algoritma Welch-Powell diterapkan dengan memodelkan mata pelajaran dan ketersediaan ruang kelas sebagai graf. Setiap mata pelajaran diwakili sebagai simpul, dan bentrokan yang mungkin terjadi ditandai sebagai sisi. Dengan demikian, sistem dapat menghasilkan jadwal yang optimal berdasarkan input yang diberikan.

## Teknologi yang Digunakan

-   **Bahasa Pemrograman**: PHP
-   **Framework**: Laravel
-   **Database**: MySQL
-   **Frontend**: HTML, CSS, JavaScript
-   **Version Control**: Git

## Instalasi

Untuk menginstal dan menjalankan sistem ini di komputer lokal Anda, ikuti langkah-langkah berikut:

1. **Clone Repository**:
    ```bash
    git clone https://github.com/arieflazuardi/SIP.git
    ```
2. **Masuk ke Direktori Proyek**:
    ```bash
    cd repo-name
    ```
3. **Instalasi Dependensi**:
    ```bash
    composer install
    ```
4. **Buat File .env**:

    ```bash
    cp .env.example .env
    ```

5. **Konfigurasi Database**:
   Edit file .env dan sesuaikan pengaturan database Anda.
6. **Jalankan Migrasi**:
    ```bash
    php artisan migrate
    ```
7. **Jalankan Server**:
    ```bash
    php artisan serve
    ```

Akses aplikasi di: http://localhost:8000

## Penggunaan

Setelah aplikasi berjalan, Anda dapat mengaksesnya melalui browser. Gunakan akun admin untuk masuk dan mulai mengelola penjadwalan mata pelajaran.

## Kontribusi

Kami menyambut baik kontribusi dari siapa saja. Jika Anda ingin berkontribusi, silakan buat pull request atau laporkan isu.

## Lisensi

Proyek ini dilisensikan di bawah MIT License.

## Kontak

Untuk pertanyaan dan dukungan lebih lanjut, silakan hubungi:

Nama: Arief Lazuardi
Email: m.arieflazuardi@gmail.com
GitHub: https://github.com/AriefLazuardi
Terima kasih telah menggunakan SIP (Sistem Informasi Penjadwalan) MIN 2 Pontianak!
