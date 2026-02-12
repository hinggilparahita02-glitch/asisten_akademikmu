
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

### Tech Stack:

* **Backend**: Laravel 11
* **Database**: MySQL 8.0
* **API Auth**: Laravel Sanctum (Token Based)
* **Frontend Web**: TailwindCSS + Alpine.js
* **Mobile (Planned)**: Android Studio (Retrofit + Java/Kotlin)

---

## 📋 User Story

| ID | User Story | Priority |
| --- | --- | --- |
| **US-01** | Sebagai user, saya ingin login hanya dengan nama dan kelas agar akses cepat | **High** |
| **US-02** | Sebagai user, saya ingin melihat ringkasan tugas mendesak di dashboard | **High** |
| **US-03** | Sebagai user, saya ingin mencatat ide/materi kuliah dengan fitur Catatan Cepat | **Medium** |
| **US-04** | Sebagai user, saya ingin menggunakan timer saat belajar untuk melacak produktivitas | **Medium** |
| **US-05** | Sebagai user, saya ingin melihat deadline tugas dalam tampilan kalender | **Low** |

---

## 📝 SRS - Feature List

| ID | Feature | Deskripsi | Status |
| --- | --- | --- | --- |
| **FR-01** | API Login | Auth menggunakan Nama & Kelas via Sanctum | ✅ Done |
| **FR-02** | Dashboard API | Mengambil ringkasan data user secara real-time | ✅ Done |
| **FR-03** | Task Calendar | CRUD tugas dengan atribut tanggal deadline | ✅ Done |
| **FR-04** | Study Timer | Menghitung durasi belajar dan menyimpan log | ✅ Done |
| **FR-05** | Note System | CRUD catatan dengan fitur *Pin/Unpin* | ✅ Done |
| **FR-06** | Mobile Integration | REST API siap dikonsumsi Android Studio | 🏗️ In Progress |

---
## Diagram ERD
<img width="484" height="244" alt="image" src="https://github.com/user-attachments/assets/63b4cd3a-1346-4f56-a1f3-7c5cb88f150c" />

## Diagram Use Case
<img width="260" height="308" alt="image" src="https://github.com/user-attachments/assets/fbb44eee-2a26-480d-a7a8-08a71883632a" />

## Diagram Sequence
<img width="604" height="398" alt="image" src="https://github.com/user-attachments/assets/8103fcdf-2698-4ce7-b997-733cc935b88e" />

---

## 🎨 Mock-Up / Screenshots


1. **Halaman Login**: Form input Nama dan Kelas.
   <img width="960" height="297" alt="Screenshot 2026-02-12 094749" src="https://github.com/user-attachments/assets/a36caa02-a3ba-4a28-a2ca-2f01860946be" />

2. **Dashboard**: Widget ringkasan menit belajar dan tugas mendesak.
   <img width="956" height="391" alt="Screenshot 2026-02-12 094803" src="https://github.com/user-attachments/assets/8b962021-6e6d-48c5-8447-c6a3e5d796ee" />

3. **Kalender**: Tampilan grid tanggal dengan penanda tugas.
   <img width="958" height="401" alt="Screenshot 2026-02-12 094818" src="https://github.com/user-attachments/assets/3e19cb8e-8cfc-47a7-ba06-a01dbf1f6b69" />

4. **Study Timer**: Antarmuka jam, menit, detik untuk fokus belajar.
   <img width="479" height="294" alt="Screenshot 2026-02-12 094838" src="https://github.com/user-attachments/assets/c2768645-faba-4e30-9404-7c40d45e0a6b" />

5. **Catatan**: List catatan dengan kartu berwarna.
   <img width="481" height="284" alt="Screenshot 2026-02-12 094850" src="https://github.com/user-attachments/assets/05c23527-bedc-4c0d-8efd-f779b4316e5b" />

---

## 🚀 Instalasi Backend

### 1. Clone & Install

```bash
git clone https://github.com/username/academic-assistant.git
cd academic-assistant
composer install

```

### 2. Konfigurasi Database

Buat database bernama `academic_assistant` di MySQL, lalu sesuaikan `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=academic_assistant
DB_USERNAME=root
DB_PASSWORD=

```

### 3. Migrasi & Jalankan

```bash
php artisan migrate
php artisan serve

```

---

## 📡 REST API Endpoints (v1)

| Method | Endpoint | Deskripsi | Auth |
| --- | --- | --- | --- |
| `POST` | `/api/v1/auth/login` | Login & dapatkan Bearer Token | No |
| `GET` | `/api/v1/dashboard/summary` | Ambil data ringkasan dashboard | **Yes** |
| `GET` | `/api/v1/notes` | List semua catatan user | **Yes** |
| `POST` | `/api/v1/tasks` | Tambah tugas baru ke kalender | **Yes** |

**Contoh Response Login:**

```json
{
  "token": "3|5pvpIY0lhm2ZUtQEndNOC...",
  "user": {
    "id": 3,
    "name": "Hinggil Parahita",
    "class": "II RKS A"
  }
}

```
