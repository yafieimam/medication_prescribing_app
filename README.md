# 🏥 Aplikasi Pencatatan Pemeriksaan & Resep Obat

Sistem berbasis web untuk pencatatan pemeriksaan pasien oleh dokter dan pengelolaan resep serta pembayaran oleh apoteker.

---

## 🚀 Fitur Utama

### 👨‍⚕️ Dokter
- 🔎 Input pemeriksaan pasien (tanda vital, catatan, upload berkas)
- 💊 Tambah resep obat secara dinamis dengan pencarian obat via API
- 📎 Upload banyak berkas per pemeriksaan
- ✏️ Edit pemeriksaan (selama belum dilayani)
- 🗂️ Lihat detail pemeriksaan
- 🧾 Validasi data dan notifikasi (toast)

### 💊 Apoteker
- 📄 Lihat daftar pemeriksaan yang belum dilayani
- ✅ Finalisasi pembayaran resep
- 🧾 Cetak resi pembayaran dalam format PDF
- 📌 Lihat detail resep dan total harga per item

---

## ⚙️ Tech Stack

- Laravel 10+
- Blade Components + TailwindCSS
- Select2 untuk autocomplete obat
- DOM scripting dengan JavaScript
- DomPDF untuk cetak resi PDF
- Relasi Many-to-Many dengan data tambahan (pivot-like)

---

## 🧰 Instalasi & Menjalankan Proyek

### 1. Clone repositori
git clone https://github.com/yafieimam/medication_prescribing_app.git
cd medication_prescribing_app

### 2. Install dependensi
composer install
npm install && npm run build

### 3. Konfigurasi .env dan Konfigurasi Storage
cp .env.example .env
php artisan key:generate
php artisan storage:link

### 4. Tambahkan Konfigurasi pada .env
Atur API:
OBAT_API_URL=http://recruitment.rsdeltasurya.com/api/v1
OBAT_EMAIL=email address anda
OBAT_PHONE=nomor handphone anda

Atur DB:
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

### 5. Migrasi & seeding database
php artisan migrate --seed

### 6. Jalankan server
php artisan serve

## 🔑 Login & Role
Role	    Email	                Password
Dokter	    dokter@example.com	    password
Apoteker	apoteker@example.com	password

## 🛠️ Fitur Khusus

### 🔍 Autocomplete Obat (API)
- Terintegrasi dengan API eksternal
- Otomatis menghitung harga berdasarkan tanggal pemeriksaan

### 📦 Upload Berkas
- Dapat upload lebih dari 1 file per pemeriksaan
- Bisa hapus berkas yang terlanjur diupload saat edit

### 📋 Dynamic Resep Form
- Bisa tambah/hapus baris resep
- Select2 untuk pencarian nama obat

### 📄 PDF Resi Pembayaran
- Cetak struk resep per pasien dalam format rapi
- Menampilkan detail dosis, jumlah, harga satuan, dan total



