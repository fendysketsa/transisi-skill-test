# Companies and Employees Management

Aplikasi Laravel untuk mengelola data companies dan employees dengan autentikasi administrator. Aplikasi ini dibuat menggunakan Laravel, `laravel/ui`, Resource Controller, Form Request Validation, migration, seeder, Repository Pattern, export PDF menggunakan `barryvdh/laravel-snappy`, import Excel menggunakan `maatwebsite/excel`, dan Select2 AJAX pagination untuk dropdown company.

## Fitur Utama

- Login administrator.
- CRUD data companies.
- CRUD data employees.
- Relasi employee ke company menggunakan foreign key.
- Pagination pada daftar companies dan employees, 5 data per halaman.
- Export PDF data employees pada setiap company.
- Import Excel data employees dengan minimal 100 records.
- Import Excel diproses menggunakan chunk dan batch insert per 10 records.
- File contoh import 100 records tersedia di folder `public/samples`.
- Dropdown company pada form employee menggunakan Select2, data di-load via AJAX, dan mendukung pagination.
- Validasi input menggunakan Laravel Form Request.
- Old input tetap tampil ketika validasi form gagal.
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
- Ekstensi PHP untuk Excel/Spreadsheet, seperti `zip`, `xml`, `xmlreader`, `xmlwriter`, `gd`, dan `simplexml`.
- Binary `wkhtmltopdf` untuk export PDF menggunakan Laravel Snappy.
- Internet saat pertama kali install dependency Composer dan saat memuat CDN Bootstrap, jQuery, dan Select2.

Pastikan `wkhtmltopdf` tersedia:

```bash
wkhtmltopdf --version
```

Pada environment project ini binary PDF dikonfigurasi di:

```env
WKHTML_PDF_BINARY=/usr/bin/wkhtmltopdf
WKHTML_IMG_BINARY=/usr/bin/wkhtmltoimage
```

Jika lokasi binary berbeda, sesuaikan nilai tersebut di `.env`.

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

5. Sesuaikan lokasi binary Snappy jika diperlukan.

```env
WKHTML_PDF_BINARY=/usr/bin/wkhtmltopdf
WKHTML_IMG_BINARY=/usr/bin/wkhtmltoimage
```

6. Jalankan migration dan seeder.

```bash
php artisan migrate --seed
```

7. Pastikan folder storage dapat ditulis oleh web server.

```bash
chmod -R 775 storage bootstrap/cache
```

8. Jalankan aplikasi.

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
- `Company`: wajib dipilih, mengacu ke data company, menggunakan Select2 AJAX pagination.
- `Email`: wajib diisi, format email valid, unik di tabel employees.

Jika company dihapus, seluruh employees yang terhubung ke company tersebut juga ikut terhapus karena foreign key menggunakan cascade delete.

### Select2 AJAX Company

Dropdown company pada form tambah dan edit employee menggunakan Select2. Data company tidak dimuat sekaligus ke halaman, tetapi diambil melalui endpoint AJAX:

```text
GET /ajax/companies
```

Parameter yang didukung:

- `term`: kata kunci pencarian company berdasarkan nama atau email.
- `page`: nomor halaman data.

Response menggunakan format Select2:

```json
{
  "results": [
    {
      "id": 1,
      "text": "Nama Company"
    }
  ],
  "pagination": {
    "more": true
  }
}
```

Endpoint ini diproteksi oleh middleware `auth`, sehingga hanya user login yang dapat mengakses data company.

### Old Input dan Validasi

Form create dan edit employee menggunakan `old()` pada field `name`, `email`, dan `company_id`. Jika validasi gagal, input sebelumnya tetap tampil dan selected company tetap dipilih kembali selama data company masih ada.

Validasi create employee berada di:

```text
app/Http/Requests/StoreEmployeeRequest.php
```

Validasi update employee berada di:

```text
app/Http/Requests/UpdateEmployeeRequest.php
```

## Export PDF Employees per Company

Aplikasi menyediakan export PDF untuk daftar employees pada setiap company. Fitur ini menggunakan package:

```text
barryvdh/laravel-snappy
```

Dependency Snappy membutuhkan binary `wkhtmltopdf`. Konfigurasi binary berada di:

```text
config/snappy.php
```

Route export PDF:

```text
GET /companies/{company}/employees/pdf
```

Nama route:

```text
companies.employees.pdf
```

Lokasi tombol export PDF:

- Halaman daftar companies, tombol `PDF` pada kolom aksi.
- Halaman detail company, tombol `Export Employees PDF`.

View PDF berada di:

```text
resources/views/companies/pdf/employees.blade.php
```

Isi PDF:

- Nama company.
- Email company.
- Website company.
- Total employee.
- Tabel employee berisi nomor, nama, dan email.

File PDF akan diunduh dengan pola nama:

```text
employees-{company_id}-{slug_company_name}.pdf
```

Contoh:

```text
employees-1-transisi-1.pdf
```

## Import Excel Employees

Aplikasi menyediakan import Excel untuk data employees. Fitur ini menggunakan package:

```text
maatwebsite/excel
```

Halaman import:

```text
GET /employees/import
```

Proses upload import:

```text
POST /employees/import
```

Nama route:

```text
employees.import.form
employees.import
```

File import divalidasi menggunakan Form Request:

```text
app/Http/Requests/ImportEmployeeRequest.php
```

Class importer berada di:

```text
app/Imports/EmployeesImport.php
```

Ketentuan import:

- File wajib bertipe `xlsx`, `xls`, atau `csv`.
- Ukuran maksimal file 10 MB.
- File wajib memiliki minimal 100 record employee.
- Baris pertama wajib berisi heading.
- Insert data diproses per 10 record menggunakan `batchSize(): 10`.
- Pembacaan file diproses per 10 record menggunakan `chunkSize(): 10`.
- Email employee wajib unik di database.
- Email employee tidak boleh duplikat di dalam file yang sama.
- `company_email` wajib cocok dengan email company yang sudah ada di database.

Heading yang wajib digunakan:

```text
name,email,company_email
```

Contoh isi:

| name | email | company_email |
| --- | --- | --- |
| Karyawan Import 1 | karyawan.import1@example.test | company@example.test |
| Karyawan Import 2 | karyawan.import2@example.test | company@example.test |

File contoh import 100 records tersedia di:

```text
public/samples/employees_import_100_records.xlsx
```

File tersebut juga dapat diunduh dari halaman import melalui tombol:

```text
Download contoh 100 records
```

Penting: sebelum memakai file contoh, pastikan ada data company dengan email yang sama seperti nilai `company_email` pada file contoh, yaitu:

```text
company@example.test
```

Jika email company tersebut belum ada, import akan gagal pada validasi `company_email`.

### Error Validasi Import

Jika validasi file gagal, aplikasi akan kembali ke halaman import dan menampilkan error pada field file.

Jika validasi baris Excel gagal, aplikasi akan menampilkan tabel berisi:

- Nomor baris.
- Nama kolom.
- Pesan error.

Dengan cara ini user dapat memperbaiki file Excel tanpa menebak baris mana yang bermasalah.

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

Dependency utama:

- `laravel/framework`: framework aplikasi.
- `laravel/ui`: scaffolding autentikasi.
- `barryvdh/laravel-snappy`: export PDF.
- `maatwebsite/excel`: import Excel.
- `phpoffice/phpspreadsheet`: engine pembacaan file spreadsheet melalui Laravel Excel.

Controller resource:

- `app/Http/Controllers/CompanyController.php`
- `app/Http/Controllers/EmployeeController.php`

Model:

- `app/Models/Company.php`
- `app/Models/Employee.php`

Form Request:

- `app/Http/Requests/CompanySelectRequest.php`
- `app/Http/Requests/ImportEmployeeRequest.php`
- `app/Http/Requests/StoreCompanyRequest.php`
- `app/Http/Requests/UpdateCompanyRequest.php`
- `app/Http/Requests/StoreEmployeeRequest.php`
- `app/Http/Requests/UpdateEmployeeRequest.php`

Import:

- `app/Imports/EmployeesImport.php`

Repository:

- `app/Repositories/CompanyRepository.php`
- `app/Repositories/EmployeeRepository.php`

Views:

- `resources/views/companies`
- `resources/views/companies/pdf/employees.blade.php`
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

Config:

- `config/snappy.php`

Sample file:

- `public/samples/employees_import_100_records.xlsx`

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
- `GET /companies/{company}/logo`: menampilkan logo company.
- `GET /companies/{company}/employees/pdf`: export PDF employees untuk company.
- `GET /employees`: daftar employees.
- `GET /employees/import`: form import Excel employees.
- `POST /employees/import`: proses import Excel employees.
- `GET /employees/create`: form tambah employee.
- `POST /employees`: simpan employee.
- `GET /employees/{employee}`: detail employee.
- `GET /employees/{employee}/edit`: form edit employee.
- `PUT/PATCH /employees/{employee}`: update employee.
- `DELETE /employees/{employee}`: hapus employee.
- `GET /ajax/companies`: endpoint AJAX Select2 untuk dropdown company.

## Catatan UI

Project menggunakan `laravel/ui` sebagai basis autentikasi dan tampilan. Layout saat ini memuat Bootstrap melalui CDN agar aplikasi tetap dapat langsung digunakan meskipun dependency frontend belum dikompilasi dengan Vite.

Form employee memuat Select2 dan jQuery dari CDN hanya pada halaman yang membutuhkan dropdown company. Layout menyediakan `@stack('styles')` dan `@stack('scripts')` agar asset halaman tertentu tidak perlu dimuat di semua halaman.

CDN yang digunakan pada form employee:

- jQuery `3.7.1`.
- Select2 `4.1.0-rc.0`.
- Select2 Bootstrap 5 Theme `1.3.0`.

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

Test yang sudah tersedia mencakup:

- Redirect guest ke login.
- Endpoint Select2 company dengan pagination.
- Import Excel ditolak jika data kurang dari 100 records.
- Import Excel berhasil insert 100 records.
- Export PDF employees per company menggunakan Snappy fake.

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

Cek binary PDF:

```bash
wkhtmltopdf --version
```

Smoke test Snappy manual dapat dilakukan dengan membuat PDF sederhana melalui wrapper Snappy:

```bash
php artisan tinker --execute='$pdf = app("snappy.pdf.wrapper"); $pdf->loadHTML("<h1>Snappy OK</h1>")->save("/tmp/snappy-smoke.pdf", true); echo filesize("/tmp/snappy-smoke.pdf");'
```

Jika perintah di atas menghasilkan ukuran file, Snappy dan `wkhtmltopdf` sudah bisa dipakai.

## Hal yang Perlu Diperhatikan End User

- Gunakan format website lengkap dengan `http://` atau `https://`.
- Logo company harus PNG, minimal 100x100 px, dan maksimal 2 MB.
- Email company dan employee tidak boleh duplikat.
- Employee tidak bisa dibuat tanpa memilih company.
- Dropdown company pada form employee dapat dicari berdasarkan nama atau email company.
- File import employee wajib berisi minimal 100 records.
- Heading file import wajib menggunakan `name`, `email`, dan `company_email`.
- `company_email` pada file import harus sudah terdaftar sebagai email company.
- Gunakan file contoh `public/samples/employees_import_100_records.xlsx` sebagai acuan format import.
- Export PDF employees tersedia dari halaman Companies.
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
- Pastikan binary `wkhtmltopdf` terinstall di server dan path `.env` sudah benar.
- Pastikan ekstensi PHP yang dibutuhkan Laravel Excel aktif, terutama `zip`, `xmlreader`, dan `xmlwriter`.
- Jika server production tidak boleh bergantung ke CDN, pindahkan Bootstrap, jQuery, Select2, dan Select2 Bootstrap Theme ke asset lokal.
- Konfigurasikan web server agar mengarah ke folder `public`.
- Gunakan HTTPS.
