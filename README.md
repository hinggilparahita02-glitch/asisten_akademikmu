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

## 🛠️ Tech Stack
* **Backend**: Laravel 11.
* **Database**: MySQL 8.0.
* **API Auth**: Laravel Sanctum (Token Based).
* **Mobile**: Android Studio (Kotlin + Retrofit) dengan dukungan **Room Database** untuk sinkronisasi catatan offline.

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

## ⚙️ Instalasi & Konfigurasi (Android)
Untuk menjalankan versi Mobile pada perangkat lokal/emulator:
1. Pastikan server Laravel berjalan menggunakan `php artisan serve`.
2. Gunakan Base URL `http://10.0.2.2:8000/` pada `RetrofitClient` agar terhubung ke localhost laptop.
3. Gunakan **Android API 30** untuk performa optimal pada perangkat spesifikasi terbatas.

```kotlin
// Contoh Konfigurasi Retrofit
private const val BASE_URL = "[http://10.0.2.2:8000/](http://10.0.2.2:8000/)"
