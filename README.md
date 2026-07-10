# Arsip Surat (Penerimaan Surat)

Sistem Informasi Penerimaan dan Pengarsipan Surat, dibangun menggunakan framework **Laravel** dan dilengkapi dengan microservice berbasis **Python** untuk pemrosesan/ekstraksi data surat.

## Prasyarat
Pastikan sistem Anda telah terinstal perangkat lunak berikut sebelum menjalankan aplikasi:
- [PHP](https://www.php.net/downloads) (Minimal versi 8.2)
- [Composer](https://getcomposer.org/)
- [Node.js & npm](https://nodejs.org/)
- [MySQL](https://www.mysql.com/) / XAMPP (untuk database)
- [Python](https://www.python.org/downloads/) (Minimal versi 3.9+)

---

## 🚀 Cara Instalasi & Menjalankan Aplikasi

Ikuti langkah-langkah di bawah ini untuk melakukan *clone* dan menjalankan aplikasi di komputer lokal Anda:

### 1. Clone Repositori
Buka terminal (Command Prompt / Git Bash / PowerShell) dan jalankan:
```bash
git clone https://github.com/Frinzz03/arsip-surat.git
cd arsip-surat
```

### 2. Setup Aplikasi Web (Laravel)
Instal dependensi PHP menggunakan Composer:
```bash
composer install
```

Instal dependensi frontend (Tailwind/Vite) dan build aset:
```bash
npm install
npm run build
```

Salin file pengaturan *environment* dan sesuaikan koneksi database Anda:
```bash
cp .env.example .env
```
*(Buka file `.env` di text editor Anda, lalu pastikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sudah sesuai dengan database MySQL lokal Anda. Buat database kosong terlebih dahulu di phpMyAdmin/MySQL).*

Generate *application key* untuk keamanan Laravel:
```bash
php artisan key:generate
```

Jalankan migrasi database beserta data *seeder* awal (jika ada):
```bash
php artisan migrate --seed
```

Buat *symbolic link* untuk direktori penyimpanan file (storage):
```bash
php artisan storage:link
```

Jalankan server pengembangan Laravel:
```bash
php artisan serve
```
Aplikasi web sekarang dapat diakses di `http://localhost:8000`.

---

### 3. Setup Microservice (Python)
Aplikasi ini memiliki microservice Python (misalnya untuk ekstraksi PDF/teks). Buka **terminal baru** (biarkan server Laravel tetap berjalan), lalu jalankan:

Masuk ke folder microservice:
```bash
cd microservice
```

Buat *Virtual Environment* (Venv) untuk mengisolasi dependensi Python:
```bash
python -m venv venv
```

Aktifkan *Virtual Environment*:
- **Windows (Command Prompt):** `venv\Scripts\activate.bat`
- **Windows (PowerShell):** `.\venv\Scripts\Activate.ps1`
- **Mac/Linux:** `source venv/bin/activate`

Instal dependensi Python:
```bash
pip install -r requirements.txt
```

Jalankan aplikasi microservice (sesuaikan dengan cara jalannya, misal Flask/FastAPI):
```bash
python app.py
# atau menyesuaikan perintah jalannya: flask run / uvicorn app:app dsb.
```

---

## Kontribusi
Jika Anda ingin berkontribusi:
1. *Fork* repositori ini
2. Buat *branch* fitur Anda (`git checkout -b fitur/NamaFitur`)
3. Lakukan *commit* perubahan Anda (`git commit -m 'Menambahkan Fitur A'`)
4. *Push* ke *branch* tersebut (`git push origin fitur/NamaFitur`)
5. Buka *Pull Request* baru
