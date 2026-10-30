# Personal Hub Management

Sistem manajemen terpadu (*All-in-One Personal Workspace*) berbasis web yang dirancang untuk menyeimbangkan ritme hidup (*work-life balance*), mengelola portofolio proyek teknis, memantau kesehatan finansial, hingga administrasi berkas operasional resmi dengan sistem isolasi peran ganda (*Multi-Role Switching*) dalam satu akun.

---

## 🛠️ Tumpukan Teknologi (Tech Stack)

* **Backend Framework**: [Laravel 11 / 13](https://laravel.com/) (PHP 8.3+)
* **Admin Panel & UI Engine**: [Filament v3](https://filamentphp.com/) (Livewire 3, Alpine.js, Tailwind CSS)
* **Database**: MySQL / MariaDB (Mendukung PostgreSQL)
* **Dokumen & PDF Generator**: [`barryvdh/laravel-dompdf`](https://github.com/barryvdh/laravel-dompdf) (Format A4 Portrait & A5 Landscape)
* **Manajemen State Peran**: Native Laravel Session-State

---

## 🌟 Fitur Utama

### 1. Sistem Multi-Role Dinamis (*One-Account Role Switching*)
Pengguna dapat beralih peran kerja secara instan melalui dropdown navigasi atas tanpa keluar log (*logout*). Navigasi sidebar dan widget dashboard terisolasi total sesuai mode yang aktif:
* **🌾 Mode Warga / Individu**:
  * Fokus pada ritme hidup, agenda kegiatan warga desa, kegiatan keluarga, dan waktu santai.
  * Sidebar hanya menampilkan kalender kegiatan dan konsep rencana.
  * Dashboard murni menyajikan kalender dan barometer jam kerja/sosial.
* **💻 Mode Pengembang (Developer / Sysadmin)**:
  * Fokus pada pengelolaan kredensial server, API, dan basis data di modul *Brankas Akun (Vault)*.
* **👔 Mode Manajer & Bisnis**:
  * Fokus pada operasional klien, manajemen proyek, surat-menyurat resmi, dan administrasi keuangan.
  * Dashboard otomatis terkunci pada visualisasi kas, rem anggaran, dan tagihan aktif.
* **👑 Mode Semua Fitur (Master / Super Admin)**:
  * Membuka seluruh menu operasional dan menampilkan tab ganda interaktif di dashboard.

---

### 2. Dashboard Adaptif & Tab Interaktif
* **Tab Ritme Hidup & Kalender**:
  * **Interactive Monthly Calendar**: Kalender 7 kolom bergaris tegas dengan pembeda akhir pekan, indikator hari ini, dan navigasi bulan reaktif.
  * **Tooltip Event Detail**: Menampilkan rincian agenda, durasi jam, kategori, dan lokasi saat kursor diarahkan ke agenda.
  * **Barometer Waktu**: Pemantau kapasitas kerja pekanan (maksimal 40 jam), kontribusi sosial warga desa, serta waktu keluarga.
* **Tab Finansial & Arus Kas**:
  * **Barometer Anggaran**: Pelacak realisasi pengeluaran bulanan terhadap target batas aman (*budget ceiling*).
  * **Tren Arus Kas (Line Chart)**: Grafik komparasi pemasukan vs pengeluaran selama 6 bulan terakhir.
  * **Pemantau Tagihan & Invoice**: Tabel tagihan berstatus aktif dilengkapi badge sisa waktu jatuh tempo (*countdown*) dan tombol cepat pelunasan.

---

### 3. Manajemen Aktivitas & Pipeline Konsep
* **Pipa Konsep ke Jadwal (*Concept Plans*)**:
  * Pencatatan ide atau draf agenda sebelum dijadwalkan secara resmi.
  * Aksi **"Jadikan Jadwal"** dalam 1 klik untuk langsung mengonversi ide menjadi agenda kalender berbobot durasi jam.
* **Notulensi Rapat (*Meeting Minutes*)**:
  * Dokumentasi hasil pertemuan, daftar hadir, dan tindak lanjut (*action items*).
  * **Dropdown Bertingkat Dinamis**: Memilih proyek akan otomatis memfilter opsi penugasan (*tasks*) yang relevan.

---

### 4. Administrasi Dokumen & Cetak PDF Otomatis
* **Surat Penugasan Tim (*Assignment Letter*)**:
  * Penomoran surat tugas otomatis (`ST/YYYY/MM/XXX`).
  * Relasi ke proyek, daftar personel yang ditugaskan, dan rincian instruksi kerja.
  * Ekspor PDF resmi format A4 Portrait.
* **Surat Kontrak Kerja Sederhana (*Work Contract*)**:
  * Penomoran SPK/Kontrak otomatis (`KTR/YYYY/MM/XXX`).
  * Relasi ke master klien dan proyek, ruang lingkup kerja, klausul pembayaran, dan nominal kontrak fleksibel (dapat menyusul).
  * Ekspor PDF perjanjian kerja sama format A4 Portrait.
* **Kwitansi Pengeluaran (*Expense Receipt*)**:
  * Penomoran kwitansi otomatis (`KW/YYYY/MM/XXX`).
  * Terhubung dengan modul *Expense* dengan pengisian nominal dan keterangan otomatis (*autofill*).
  * Ekspor cetak PDF tanda terima format A5 Landscape dengan pengamanan nama berkas (*safe-filename*).
* **Penerbitan Kwitansi Cepat (*One-Click Receipt*)**:
  * Tombol **"Terbitkan Kwitansi"** langsung dari tabel pengeluaran, yang bertransisi menjadi **"Cetak Kwitansi"** jika berkas sudah diterbitkan.

---

### 5. Modul Finansial & Data Terpadu
* **Keuangan & Kas**: Rekening dompet/bank, pencatatan pemasukan, pengeluaran berpos anggaran, serta pencatatan utang-piutang.
* **Relasi & Klien**: Master basis data klien dan portofolio proyek penugasan.
* **Master Wilayah**: Basis data hierarkis Provinsi dan Kota/Kabupaten.
* **Brankas Akun (Vault)**: Penyimpanan aman kredensial akun, kredensial hosting/server, dan catatan sensitif yang terenkripsi di basis data.

---

## 🚀 Panduan Instalasi Lokal

1. **Clone Repositori & Masuk ke Direktori Proyek**:
   ```bash
   git clone <repository-url>
   cd personal-hub
   ```

2. **Pasang Dependensi PHP**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**:
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
   *Sesuaikan pengaturan koneksi database `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di berkas `.env`.*

4. **Jalankan Migrasi Database**:
   ```bash
   php artisan migrate
   ```

5. **Buat Akun Administrator**:
   ```bash
   php artisan make:filament-user
   ```

6. **Bersihkan Cache & Jalankan Server**:
   ```bash
   php artisan optimize:clear
   php artisan serve
   ```
   Akses panel melalui browser di: `http://localhost:8000/admin`

---

## 📁 Struktur Berkas Penting

```text
app/
├── Filament/
│   ├── Pages/
│   │   └── Dashboard.php                 # Logika isolasi tab & widget dashboard per role
│   ├── Resources/
│   │   ├── AssignmentLetterResource.php  # Resource Surat Penugasan & Aksi Cetak
│   │   ├── ConceptPlanResource.php       # Resource Ide Rencana & Aksi Konversi Jadwal
│   │   ├── ExpenseReceiptResource.php    # Resource Kwitansi & Autofill Expense
│   │   ├── ExpenseResource.php           # Resource Pengeluaran & Tombol Cetak Cepat
│   │   ├── MeetingMinuteResource.php     # Resource Notulensi & Dependent Dropdown Task
│   │   └── WorkContractResource.php      # Resource Surat Kontrak & Aksi Cetak
│   └── Widgets/                          # Widget Kalender, Finansial, & Arus Kas
├── Http/Controllers/
│   └── DocumentPrintController.php       # Generator stream PDF via DomPDF
└── Models/                               # Model Eloquent & Relasi Data
resources/
├── views/
│   ├── filament/
│   │   ├── components/
│   │   │   └── role-switcher.blade.php   # Komponen Dropdown Switcher Peran Navbar
│   │   ├── pages/
│   │   │   └── dashboard.blade.php       # Blade View Wrapper Tab Dashboard
│   │   └── widgets/
│   │       └── month-calendar-widget.blade.php # Tampilan Kalender Native
│   └── prints/                           # Template HTML/Blade Dokumen Cetak (PDF)
```