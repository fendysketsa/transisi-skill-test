# Companies and Employees Management

Aplikasi Laravel untuk mengelola data companies dan employees dengan autentikasi administrator. Aplikasi ini dibuat menggunakan Laravel, `laravel/ui`, Resource Controller, Form Request Validation, migration, seeder, dan Repository Pattern agar logika query/CRUD tidak menumpuk di controller.

## Fitur Utama

- Login administrator.
- CRUD data companies.
- CRUD data employees.
- Relasi employee ke company menggunakan foreign key.
- Pagination pada daftar companies dan employees, 5 data per halaman.
- Validasi input menggunakan Laravel Form Request.
- Penyimpanan logo company ke `storage/app/company`.
- Repository Pattern untuk memisahkan proses query, create, update, delete, dan upload file dari controller.

## Akun Administrator

Akun admin dibuat melalui database seeder.

```text
Email    : admin@transisi.id
Password : transisi
```

Jalankan seeder dengan perintah:

```bash
php artisan db:seed
```

Atau jalankan bersamaan dengan migration:

```bash
php artisan migrate --seed
```

## Kebutuhan Sistem

- PHP 8.2 atau lebih baru.
- Composer.
- MySQL atau database lain yang didukung Laravel.
- Ekstensi PHP umum untuk Laravel, termasuk `pdo`, `mbstring`, `openssl`, `fileinfo`, dan ekstensi database yang digunakan.

## Instalasi

1. Install dependency PHP.

```bash
composer install
```

2. Salin file environment jika belum ada.

```bash
cp .env.example .env
```

3. Generate application key.

```bash
php artisan key:generate
```

4. Sesuaikan konfigurasi database di `.env`.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=user_database
DB_PASSWORD=password_database
```

5. Jalankan migration dan seeder.

```bash
php artisan migrate --seed
```

6. Pastikan folder storage dapat ditulis oleh web server.

```bash
chmod -R 775 storage bootstrap/cache
```

7. Jalankan aplikasi.

```bash
php artisan serve
```

Akses aplikasi melalui:

```text
http://127.0.0.1:8000
```

## Alur Penggunaan

1. Buka halaman aplikasi.
2. Login menggunakan akun administrator.
3. Masuk ke menu `Companies` untuk mengelola data company.
4. Masuk ke menu `Employees` untuk mengelola data employee.
5. Untuk membuat employee, minimal harus ada 1 data company terlebih dahulu karena employee wajib terhubung ke company.

## Data Companies

Field company yang dikelola:

- `Nama`: wajib diisi.
- `Email`: wajib diisi, format email valid, unik di tabel companies.
- `Logo`: wajib saat membuat company, harus PNG, minimal 100x100 px, maksimal 2 MB.
- `Website`: wajib diisi, harus berupa URL valid, contoh `https://example.com`.

Saat edit company, logo tidak wajib diunggah ulang. Jika logo baru diunggah, logo lama akan dihapus dari storage.

## Data Employees

Field employee yang dikelola:

- `Nama`: wajib diisi.
- `Company`: wajib dipilih, mengacu ke data company.
- `Email`: wajib diisi, format email valid, unik di tabel employees.

Jika company dihapus, seluruh employees yang terhubung ke company tersebut juga ikut terhapus karena foreign key menggunakan cascade delete.

## Penyimpanan Logo

Logo company disimpan pada:

```text
storage/app/company
```

Logo tidak disimpan langsung di folder `public`. Aplikasi menyediakan route khusus yang terproteksi autentikasi untuk menampilkan logo:

```text
/companies/{company}/logo
```

Dengan pendekatan ini, logo hanya dapat diakses setelah user login.

## Struktur Teknis Penting

Controller resource:

- `app/Http/Controllers/CompanyController.php`
- `app/Http/Controllers/EmployeeController.php`

Model:

- `app/Models/Company.php`
- `app/Models/Employee.php`

Form Request:

- `app/Http/Requests/StoreCompanyRequest.php`
- `app/Http/Requests/UpdateCompanyRequest.php`
- `app/Http/Requests/StoreEmployeeRequest.php`
- `app/Http/Requests/UpdateEmployeeRequest.php`

Repository:

- `app/Repositories/CompanyRepository.php`
- `app/Repositories/EmployeeRepository.php`

Views:

- `resources/views/companies`
- `resources/views/employees`
- `resources/views/auth`
- `resources/views/layouts/app.blade.php`

Routes:

- `routes/web.php`

Migrations:

- `database/migrations/*_create_companies_table.php`
- `database/migrations/*_create_employees_table.php`

Seeder:

- `database/seeders/DatabaseSeeder.php`

## Routes Utama

- `GET /login`: halaman login.
- `POST /login`: proses login.
- `POST /logout`: proses logout.
- `GET /companies`: daftar companies.
- `GET /companies/create`: form tambah company.
- `POST /companies`: simpan company.
- `GET /companies/{company}`: detail company.
- `GET /companies/{company}/edit`: form edit company.
- `PUT/PATCH /companies/{company}`: update company.
- `DELETE /companies/{company}`: hapus company.
- `GET /employees`: daftar employees.
- `GET /employees/create`: form tambah employee.
- `POST /employees`: simpan employee.
- `GET /employees/{employee}`: detail employee.
- `GET /employees/{employee}/edit`: form edit employee.
- `PUT/PATCH /employees/{employee}`: update employee.
- `DELETE /employees/{employee}`: hapus employee.

## Catatan UI

Project menggunakan `laravel/ui` sebagai basis autentikasi dan tampilan. Layout saat ini memuat Bootstrap melalui CDN agar aplikasi tetap dapat langsung digunakan meskipun dependency frontend belum dikompilasi dengan Vite.

Jika ingin memakai asset lokal dari Vite, jalankan:

```bash
npm install
npm run build
```

Lalu sesuaikan kembali layout agar menggunakan directive Vite:

```blade
@vite(['resources/sass/app.scss', 'resources/js/app.js'])
```

## Testing dan Validasi

Jalankan test:

```bash
php artisan test
```

Cek route:

```bash
php artisan route:list
```

Cek compile Blade:

```bash
php artisan view:cache
```

Jika setelah mengubah view ingin membersihkan cache:

```bash
php artisan view:clear
```

## Hal yang Perlu Diperhatikan End User

- Gunakan format website lengkap dengan `http://` atau `https://`.
- Logo company harus PNG, minimal 100x100 px, dan maksimal 2 MB.
- Email company dan employee tidak boleh duplikat.
- Employee tidak bisa dibuat tanpa memilih company.
- Menghapus company akan menghapus employee yang berada di bawah company tersebut.
- Simpan kredensial admin dengan aman dan ubah password setelah deploy ke environment produksi.

## Catatan Produksi

Sebelum digunakan di production:

- Ubah `APP_ENV=production`.
- Ubah `APP_DEBUG=false`.
- Pastikan `APP_KEY` sudah dibuat.
- Gunakan password admin yang kuat.
- Pastikan permission folder `storage` dan `bootstrap/cache` benar.
- Jalankan migration secara terkontrol.
- Konfigurasikan web server agar mengarah ke folder `public`.
- Gunakan HTTPS.

