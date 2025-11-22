<p align="center">
    <a href="#" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="App Logo">
    </a>
</p>

<p align="center">
    <a href="#"><img src="https://img.shields.io/badge/build-passing-brightgreen" alt="Build Status"></a>
    <a href="#"><img src="https://img.shields.io/badge/version-1.0.0-blueviolet" alt="Version"></a>
    <a href="#"><img src="https://img.shields.io/badge/license-MIT-success" alt="License"></a>
</p>

---

## Aplikasi Kasir – Point of Sale (POS)

Aplikasi Kasir sederhana namun profesional berbasis **Laravel** untuk kebutuhan bisnis pribadi seperti toko retail, warung, minimarket, atau usaha kecil lainnya.  
Didesain dengan antarmuka cepat, fitur lengkap, dan stabil untuk operasional harian.

---

## ✨ Fitur Utama

- **Penjualan Cepat (Kasir)**
  - Live search produk
  - Barcode scanner
  - Hitung otomatis (subtotal, diskon, pajak, total)
  - Multi pembayaran  
  - Hold order

- **Manajemen Produk**
  - Tambah / edit produk
  - Kategori & satuan
  - Update stok otomatis

- **Struk & Laporan**
  - Cetak struk 58/80mm
  - Laporan harian / mingguan / custom
  - Export Excel & PDF

- **Pengaturan**
  - Profil toko
  - Printer thermal
  - Ukuran font & tampilan

---

## 🛠 Teknologi

- Laravel 10+  
- TailwindCSS  
- Alpine.js  
- MySQL / MariaDB  

---

## 🚀 Instalasi

bash
git clone https://github.com/username/kasir-app.git

cd kasir-app

composer install

npm install

npm run build

cp .env.example .env

php artisan key:generate

---
## Konfigurasi database pada .env:
bash
DB_DATABASE=kasir

DB_USERNAME=root

DB_PASSWORD=


## Migrasi & seeder:

php artisan migrate --seed


## Jalankan aplikasi:

php artisan serve

## 🔑 Akun Default
Role	    Owner

Email	    admin@example.com    

Password    password

	   

📄 Lisensi

Aplikasi ini menggunakan lisensi MIT.

❤️ Terima Kasih

Aplikasi ini dibuat untuk mempermudah operasional bisnis pribadi saya.
Semoga terus berkembang dan bermanfaat.
