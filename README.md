# 📚 Dokumentasi Project: Asisten Akademik Harian

**Asisten Akademik Harian** adalah platform manajemen produktivitas mahasiswa yang dirancang untuk membantu pengelolaan waktu belajar, tugas, dan catatan dalam satu ekosistem terintegrasi.

## 📖 Deskripsi
Aplikasi ini membantu mahasiswa mengorganisir jadwal akademik mereka melalui fitur kalender tugas, pencatatan cepat, dan pengukur waktu belajar (*Study Timer*). 

### Fitur Utama:
* **Authentication**: Login sederhana menggunakan Nama dan Kelas.
* **Dashboard Summary**: Ringkasan aktivitas harian (menit belajar, jumlah tugas, dan catatan terbaru).
* **Manajemen Tugas (Kalender)**: Visualisasi deadline tugas dalam bentuk kalender interaktif.
* **Study Timer**: Pengukur waktu belajar untuk membantu fokus mahasiswa.
* **Catatan Cepat**: Fitur CRUD catatan dengan dukungan penyematan (*pin*) catatan penting.
#### Fitur Lanjutan:
* **Academic Management (Courses)**: Sebagai referensi utama data akademik yang menghubungkan setiap sesi belajar dan tugas dengan mata kuliah tertentu untuk menghasilkan statistik performa yang akurat.

---

## 📊 Detail Arsitektur Database & Relasi

Meskipun beberapa antarmuka dirancang sederhana untuk kecepatan akses (UX), tabel **`courses`** tetap dipertahankan sebagai pusat relasi data karena peran krusialnya:

1. **Relasi Study Timer**: Setiap log waktu belajar pada `study_sessions` terhubung ke `courses`. Hal ini memungkinkan sistem menghitung total menit belajar per-mata kuliah untuk ditampilkan di Dashboard Summary.
2. **Pengelompokan Tugas**: Memungkinkan fitur Kalender untuk memfilter tugas berdasarkan mata kuliah tertentu di pengembangan versi mendatang.
3. **Integritas Data**: Menghindari redundansi data dengan memastikan nama mata kuliah terstandarisasi, sehingga laporan akademik user tetap konsisten.

> **Catatan Implementasi Mobile**: Untuk menjaga kecepatan alur kerja pengguna (UX), sesi belajar dipetakan secara otomatis ke entitas 'General Study' di dalam tabel ini agar user tidak perlu memilih mata kuliah secara manual setiap kali memulai timer.

---

## 🧩 Tech Stack

### 1. Backend & Frontend (Web)
* **Framework**: Laravel 11.
* **Bahasa Pemrograman**: PHP >= 8.2.
* **Database**: MySQL / SQLite.
* **Engine Template**: Blade Template.
* **Styling**: TailwindCSS, HTML, dan CSS.
* **Interaktivitas**: JavaScript.

### 2. Mobile
* **IDE**: Android Studio.
* **Bahasa Pemrograman**: Kotlin.
* **Komponen Utama**: Android WebView.
* **Environment**: Emulator Pixel (API 33).

### 3. Tools
* **Version Control**: Git & GitHub.
* **Local Server**: XAMPP.

---

## 🏗️ Arsitektur Sistem
Sistem ini mengadopsi model *Client-Server* terpusat:
* **Server-Side**: Seluruh logika bisnis, manajemen database, dan tampilan UI dikelola oleh Laravel menggunakan Blade Template.
* **Client-Side (Mobile)**: Aplikasi Android bertindak sebagai *wrapper* atau kontainer menggunakan **WebView** untuk memuat URL server lokal/cloud.

---

## 📋 User Story
| ID | User Story | Priority |
| --- | --- | --- |
| **US-01** | Sebagai user, saya ingin login hanya dengan nama dan kelas agar akses cepat | **High** |
| **US-02** | Sebagai user, saya ingin melihat ringkasan tugas mendesak di dashboard | **High** |
| **US-03** | Sebagai user, saya ingin mencatat ide/materi kuliah secara offline | **Medium** |
| **US-04** | Sebagai user, saya ingin menggunakan timer saat belajar untuk melacak produktivitas | **Medium** |
| **US-05** | Sebagai user, saya ingin melihat deadline tugas dalam tampilan kalender | **Low** |

---
## 📐 SDLC (Software Development Life Cycle)
Metodologi yang digunakan: **Waterfall dengan iterasi**
1. **Planning**: Penentuan user story dan kebutuhan fungsional.
2. **Analysis**: Perancangan SRS dan analisis endpoint API Laravel.
3. **Design**: Perancangan ERD, Use Case, dan Mockup UI Android.
4. **Development**: Coding backend Laravel dan implementasi Mobile (Kotlin).
5. **Testing**: Pengujian fungsionalitas dan konektivitas API.
6. **Maintenance**: Refactoring kode dan optimasi performa berkelanjutan.
---

## 📝 SRS - Feature List
| ID | Feature | Deskripsi | Status |
| --- | --- | --- | --- |
| **FR-01** | API Login | Auth menggunakan Nama & Kelas via Sanctum | ✅ Done |
| **FR-02** | Dashboard API | Mengambil ringkasan data user secara real-time | ✅ Done |
| **FR-03** | Task Calendar | CRUD tugas dengan atribut tanggal deadline | ✅ Done |
| **FR-04** | Study Timer | Menghitung durasi belajar dan menyimpan log | ✅ Done |
| **FR-05** | Note System | CRUD catatan dengan fitur *Pin/Unpin* | ✅ Done |
| **FR-06** | Mobile Integration | Navigasi Fragment & integrasi Room Database | 🏗️ In Progress |

---

## 🏗️ Arsitektur Sistem (Client-Server)
Sistem dikembangkan dengan memisahkan tanggung jawab antara *Frontend* Mobile dan *Backend* API:

* **Presentation Layer (Mobile App)**: Dikembangkan menggunakan Kotlin di Android Studio dengan arsitektur Fragment untuk navigasi yang efisien.
* **Data Access Layer**: 
    * **Remote**: Menggunakan library **Retrofit** untuk konsumsi RESTful API dari server Laravel.
    * **Local Persistence**: Menggunakan **Android Room Database** untuk penyimpanan data `notes` secara offline demi performa yang lebih cepat.
* **Backend Layer**: Laravel 11 sebagai penyedia layanan API (Auth, CRUD, Summary) dengan database MySQL 8.0.

---

## 📊 Rancangan UML & Database

### 1. Entity Relationship Diagram (ERD)
Berdasarkan skema database, sistem mengelola entitas sebagai berikut:
* **Users**: Menyimpan identitas mahasiswa (Nama dan Kelas).
* **Tasks**: Manajemen deadline tugas dengan status pengerjaan.
* **Courses**: Tabel referensi mata kuliah, SKS, dan nilai yang menghubungkan tugas dan sesi belajar.
* **Study Sessions**: Mencatat durasi fokus belajar mahasiswa yang terhubung ke mata kuliah spesifik.
* **Notes**: Sistem catatan cepat dengan dukungan fitur *pinning* untuk prioritas.
<img width="392" height="226" alt="image" src="https://github.com/user-attachments/assets/1e11f1d8-630e-4464-9ac4-4388418947cb" />


### 2. Use Case Diagram
* **Actor**: Mahasiswa (User).
* **Fitur Utama**:
    * **Authentication**: Login berbasis Nama & Kelas via Laravel Sanctum.
    * **Dashboard**: Melihat ringkasan menit belajar dan jumlah tugas mendesak.
    * **Manage Tasks**: Operasi CRUD pada jadwal tugas dan kalender.
    * **Manage Notes**: Pengelolaan catatan harian secara offline/online.
    * **Study Timer**: Melakukan sesi fokus belajar (Pomodoro).
    <img width="1511" height="1208" alt="image" src="https://github.com/user-attachments/assets/3e612d36-874b-42e8-b1dc-a8622d707e2b" />


### 3. Sequence Diagram (Alur Login & Sync)
1.  User menginput kredensial di aplikasi Mobile.
2.  Aplikasi mengirim request ke Endpoint `/api/v1/auth/login`.
3.  Server Laravel memvalidasi dan mengembalikan **Bearer Token**.
4.  Aplikasi menyimpan token dan melakukan sinkronisasi data Dashboard secara otomatis.
<img width="2811" height="1690" alt="image" src="https://github.com/user-attachments/assets/9cfad74f-4bd1-4c7e-bcdd-377c417f6621" />

---

## 🛠️ Tech Stack
* **Backend**: Laravel 11 & MySQL 8.0.
* **Mobile**: Kotlin, Retrofit, Room Database, Material Design 3.
* **Tools**: Android Studio, Git & GitHub, XAMPP.

---

## 📦 Instalasi

### 1. Backend (Laravel)
Pastikan PHP >= 8.2 dan Composer sudah terinstal.

1. **Clone & Install**
   ```bash
   git clone <repository-url>
   cd backend-folder
   composer install
Setup Environment

Bash
cp .env.example .env
php artisan key:generate
Konfigurasi Database
Edit file .env dan sesuaikan dengan database MySQL Anda:

Cuplikan kode
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username
DB_PASSWORD=password
Migrasi & Run

Bash
php artisan migrate
php artisan serve
Akses di: http://localhost:8000.

2. Mobile (Android)
Gunakan Android Studio dengan Android API 30 untuk performa optimal pada spesifikasi laptop i3.

Buka folder mobile di Android Studio.

Edit file RetrofitClient.kt, ubah BASE_URL menjadi http://10.0.2.2:8000/ agar terhubung ke localhost laptop.

Klik Sync Project with Gradle Files.

Jalankan pada emulator atau perangkat fisik.

---

## 👤 Author
**Hinggil Parahita**
