# QA Master Guide: PRD, Scope, and Test Cases
## Third-Party Booking Engine POC — New Auth Suite (OAuth 2.1) & Transaction V2

---

## 1. Executive Summary & Testing Directives for QA

Dokumen ini adalah **panduan pengujian terpusat (Master Test Plan)** untuk tim Quality Assurance (QA). Fitur yang diuji adalah **integrasi otentikasi generasi baru (OAuth 2.1) dan gateway transaksi V2** antara sistem keanggotaan (*Membership Platform Staging*) dengan aplikasi pihak ketiga (disimulasikan oleh *Booking Engine POC*).

### ⚠️ PERHATIAN: Apa yang TIDAK PERLU Difokuskan oleh QA
Aplikasi Booking Engine ini adalah **Proof of Concept (POC)** yang dibangun untuk memvalidasi kelayakan fungsional (*functional feasibility*), **bukan aplikasi akhir komersial**. Oleh karena itu:
* **JANGAN FOKUS PADA:**
  1. **Tampilan visual / Desain UI**: Detail margin, padding, responsive layout pixel-perfect, animasi transisi, atau estetika kartu kamar.
  2. **Payment Gateway**: Pembayaran di POC sengaja disimulasikan sukses otomatis (*instant confirmed*) tanpa kartu kredit/VA riil.
  3. **Inventaris Hotel Riil**: Allotment kamar dan fasilitas hotel di sisi POC bersifat simulasi/dummy.
* **FOKUS UTAMA QA (100% Core Target):**
  1. **Modul Otentikasi V2 (Auth Module)**: Password Login, Email OTP, Registrasi Baru (+ Kode Referral), dan Google SSO.
  2. **Sinkronisasi Sesi & Profil Member**: Validasi nama, status tingkatan (*tier*), saldo poin, dan avatar yang ditarik dari server staging.
  3. **Keuntungan Member Dinamis**: Harga coret diskon member yang berubah seketika di katalog kamar.
  4. **Push Transaksi V2**: Suksesnya transmisi data reservasi ke server staging dan perhitungan estimasi poin member.
  5. **Mekanisme Pelepasan Poin (Extranet)**: Verifikasi koneksi OAuth dan pengujian mode pelepasan poin (Immediate, Upon Check-in, Upon Check-out).

> 💡 **Keuntungan untuk QA**: Semua pengujian dapat dilakukan **100% melalui Antarmuka Web (UI POC)** tanpa perlu membuka Postman atau menjalankan perintah cURL terminal.

---

## 2. Product Requirements Document (PRD)

### 2.1 Latar Belakang & Masalah yang Diselesaikan
Sebelumnya, integrasi Booking Engine dengan Membership masih mengandalkan mekanisme token manual atau sesi iframe yang rentan terhadap masalah cookie pihak ketiga (*third-party cookies deprecation*). 

Dengan arsitektur **OAuth 2.1 Suite (Route V2)**:
* Booking Engine bertindak sebagai *OAuth Client* resmi menggunakan pasangan kredensial aman (`client_id` dan `client_secret`).
* Member dapat masuk atau mendaftar langsung di dalam modal Booking Engine tanpa meninggalkan halaman pemesanan.
* Transaksi reservasi didorong ke Membership Platform secara aman menggunakan *Dual-Authentication Middleware* (OAuth 2.1 Bearer Token).

### 2.2 Target Pengguna dalam Pengujian
* **Calon Tamu (Guest / Non-Member)**: Melihat harga reguler publik, melihat potensi hemat jika menjadi member, dan dapat melakukan registrasi instan dengan kode referral.
* **Member Terdaftar (Active Member)**: Masuk akun, otomatis menikmati tarif eksklusif sesuai tingkatan (*tier*), dan mengumpulkan poin loyalitas dari reservasi.
* **Admin Properti / Extranet Manager**: Memeriksa status koneksi OAuth 2.1 ke Membership Platform, mengatur mode pelepasan poin (*Immediate* vs *Check-in/out*), dan memvalidasi riwayat pemesanan.

### 2.3 Lingkungan Pengujian (Test Environment)
* **Aplikasi Booking Engine POC (Client UI)**: Akses link publik Cloudflare Tunnel yang diberikan (atau `http://localhost:4173`).
* **Membership Platform API (OAuth Provider)**: `https://membership-staging-2-api.omnihotelier.net`
* **Tenant Uji Resmi**: `jeevawasa.omnihotelier.net` (Corporate: JEEVAWASA, Properti: Adiwana Unagi).

---

## 3. Matriks Rekomendasi Test Cases (QA Test Matrix)

Gunakan daftar skenario uji berikut untuk memvalidasi alur end-to-end langsung dari browser:

```
===================================================================================
FASE 1: IDENTITAS PROPERTI & DISCOVERY (PROPERTY CONTEXT)
===================================================================================
```

### [TC-CTX-01] Discovery Konteks Properti Staging (Happy Path)
* **Tujuan**: Memastikan Booking Engine berhasil membaca konfigurasi dinamis properti dari server staging.
* **Langkah Pengujian**:
  1. Buka halaman utama Booking Engine.
  2. Pilih properti **Adiwana Unagi** (atau Unagi Mas Villas Ubud).
* **Hasil yang Diharapkan**:
  * Logo resmi properti / Jeevawasa termuat dengan benar di header.
  * Warna aksen brand (*primary color*) menyesuaikan tema korporat (Amber `#D97706`).
  * Modal login otomatis mengaktifkan opsi login yang didukung: Email/Password, Email OTP, dan Google.

---

```
===================================================================================
FASE 2: AUTENTIKASI MEMBER (OAUTH 2.1 AUTH MODULE)
===================================================================================
```

### [TC-AUTH-01] Sign In dengan Email & Password Valid (Happy Path)
* **Tujuan**: Memvalidasi login member terdaftar menggunakan kredensial yang sah.
* **Langkah Pengujian**:
  1. Klik tombol **"Member Sign In"** di pojok kanan atas.
  2. Pilih tab **"Email & Password"**.
  3. Masukkan email member staging aktif (contoh: akun terdaftar Anda) dan password yang benar.
  4. Klik tombol **"Log In"**.
* **Hasil yang Diharapkan**:
  * Modal login tertutup secara otomatis.
  * Tombol navigasi berubah menjadi kartu profil member: menampilkan Nama Lengkap, Badge Tingkatan (misal: *Silver / Gold / Diamond*), dan Saldo Poin aktif.
  * Katalog kamar otomatis mengkalkulasi ulang harga menjadi tarif khusus member.

### [TC-AUTH-02] Sign In dengan Password Salah (Negative Case)
* **Tujuan**: Memastikan penolakan akses dan pesan peringatan yang tepat saat kata sandi salah.
* **Langkah Pengujian**:
  1. Buka modal login, masukkan email yang terdaftar.
  2. Masukkan password sembarang/salah.
  3. Klik **"Log In"**.
* **Hasil yang Diharapkan**:
  * Muncul banner pesan error berwarna merah di dalam modal: `"Invalid email or password."`
  * Sesi login tidak dibuat; tamu tetap berstatus non-member.

### [TC-AUTH-03] Sign In dengan Email Tidak Terdaftar (Negative Case)
* **Tujuan**: Memvalidasi respons sistem terhadap akun yang belum pernah didaftarkan.
* **Langkah Pengujian**:
  1. Masukkan email acak (contoh: `random_user_999@test.com`).
  2. Masukkan password sembarang, klik **"Log In"**.
* **Hasil yang Diharapkan**:
  * Muncul pesan error yang jelas: `"The account was not found"` atau `"OAUTH.LOGIN.ACCOUNT_NOT_FOUND"`.

---

```
===================================================================================
FASE 3: PASSWORDLESS LOGIN (EMAIL OTP)
===================================================================================
```

### [TC-OTP-01] Permintaan Kode OTP Email (Happy Path)
* **Tujuan**: Menguji pengiriman kode OTP 6-digit ke email member terdaftar.
* **Langkah Pengujian**:
  1. Buka modal login, pilih tab **"Email OTP (Passwordless)"**.
  2. Masukkan email member yang terdaftar di staging.
  3. Klik tombol **"Send OTP Code"**.
* **Hasil yang Diharapkan**:
  * Tampilan modal beralih ke input 6-digit kode OTP.
  * Muncul timer hitung mundur masa berlaku OTP (120 detik).
  * Email notifikasi berisi 6 digit kode OTP terkirim ke inbox email member (atau Mailpit staging).

### [TC-OTP-02] Verifikasi OTP dengan Kode Benar (Happy Path)
* **Langkah Pengujian**:
  1. Lanjutkan dari [TC-OTP-01], ketik 6-digit kode yang diterima di email.
  2. Klik **"Verify & Sign In"**.
* **Hasil yang Diharapkan**:
  * Modal tertutup, token sesi OAuth 2.1 tersimpan, dan member berhasil masuk.
  * Profil member dan saldo poin tampil di bilah navigasi.

### [TC-OTP-03] Verifikasi OTP dengan Kode Salah / Kedaluwarsa (Negative Case)
* **Langkah Pengujian**:
  1. Masukkan 6-digit kode acak yang salah (contoh: `000000`).
  2. Klik **"Verify & Sign In"**.
* **Hasil yang Diharapkan**:
  * Muncul pesan peringatan: `"Invalid OTP code"` atau `"OTP code has expired"`.
  * Tamu tetap berada di form verifikasi OTP untuk mencoba kembali.

---

```
===================================================================================
FASE 4: REGISTRASI MEMBER BARU DENGAN KODE REFERRAL
===================================================================================
```

### [TC-REG-01] Registrasi Member Baru dengan Kode Referral (Happy Path)
* **Tujuan**: Memvalidasi pembuatan akun member baru beserta atribusi program referral.
* **Langkah Pengujian**:
  1. Buka modal login, klik link **"Sign Up"** di bagian bawah.
  2. Masukkan email baru yang belum pernah terdaftar.
  3. Klik **"Send Verification Code"** dan masukkan kode OTP yang dikirimkan ke email baru tersebut.
  4. Isi formulir profil: Nama Lengkap, Nomor Telepon, Kata Sandi.
  5. Isi kolom opsional **"Referral Code"** dengan kode referral yang valid.
  6. Klik tombol **"Complete Registration"**.
* **Hasil yang Diharapkan**:
  * Akun member baru berhasil dibuat di tenant staging Jeevawasa.
  * Member otomatis masuk (*auto-login*) dan mendapatkan tingkatan awal (*Silver*).
  * Atribusi referral tercatat di database tracking staging.

### [TC-REG-02] Registrasi dengan Email yang Sudah Ada (Duplicate Validation)
* **Langkah Pengujian**:
  1. Pada form Sign Up, masukkan email yang sudah pernah terdaftar sebelumnya.
  2. Klik tombol minta kode verifikasi.
* **Hasil yang Diharapkan**:
  * Sistem memblokir proses dengan pesan error: `"Email is already registered"`.

---

```
===================================================================================
FASE 5: GOOGLE SSO (SINGLE SIGN ON)
===================================================================================
```

### [TC-SSO-01] Google Sign In Resmi (Google Identity Services)
* **Tujuan**: Memverifikasi login menggunakan tombol Google resmi.
* **Langkah Pengujian**:
  1. Di modal login, klik tombol resmi **"Continue with Google"**.
  2. Pilih akun Google Anda di jendela popup Google.
* **Hasil yang Diharapkan**:
  * Token ID Google dikirim ke server staging, diverifikasi secara kriptografis oleh `Google_Client`.
  * Jika akun baru: otomatis didaftarkan sebagai member, foto profil Google dijadikan avatar, dan member langsung masuk.

### [TC-SSO-02] Simulasi Dev 1-Click Google SSO (Fallback Testing)
* **Tujuan**: Menguji flow Google SSO jika domain pengujian belum didaftarkan di Google Cloud Console.
* **Langkah Pengujian**:
  1. Klik tombol alternatif **"Lanjutkan dengan Google (Dev/Simulasi)"**.
* **Hasil yang Diharapkan**:
  * Alur simulasi Google SSO berhasil mengeksekusi login dan menyajikan profil member.

---

```
===================================================================================
FASE 6: HARGA DINAMIS & KERANJANG (TIER RATE CALCULATION)
===================================================================================
```

### [TC-RATE-01] Perbandingan Harga Publik vs Diskon Member (Real-Time)
* **Langkah Pengujian**:
  1. Buka katalog kamar dalam status **Belum Login (Guest)**: Catat harga kamar (contoh: IDR 2.000.000 / malam).
  2. Lakukan **Login** sebagai member dengan Tier tertentu (misal: *Gold / Diamond*).
* **Hasil yang Diharapkan**:
  * Harga kamar seketika berubah tanpa refresh halaman penuh.
  * Harga lama (IDR 2.000.000) dicoret dan ditampilkan harga hemat khusus member (misal Diskon 15% = IDR 1.700.000).
  * Muncul label keuntungan: *"Member Exclusive Rate"*.

### [TC-RATE-02] Logout Sesi Member
* **Langkah Pengujian**:
  1. Klik menu profil di kanan atas, pilih **"Sign Out"**.
* **Hasil yang Diharapkan**:
  * Sesi OAuth 2.1 dihapus dari penyimpanan lokal browser.
  * Bilah navigasi kembali menampilkan tombol "Member Sign In".
  * Seluruh harga kamar kembali normal ke harga reguler non-anggota.

---

```
===================================================================================
FASE 7: RESERVASI & PUSH TRANSACTION V2 (DUAL-AUTH GATEWAY)
===================================================================================
```

### [TC-TRX-01] Pemesanan Kamar oleh Member (Push Transaksi ke Staging)
* **Tujuan**: Memastikan transaksi reservasi berhasil dikirim dan tersimpan di database staging.
* **Langkah Pengujian**:
  1. Pastikan dalam kondisi **sudah login sebagai member**.
  2. Pilih tipe kamar, tentukan tanggal check-in dan check-out, klik **"Book Now"**.
  3. Masuk ke halaman Checkout: periksa rincian biaya (Pajak, Layanan, Diskon Member, dan Estimasi Poin yang Didapat).
  4. Isi data tamu dan klik tombol **"Confirm Reservation"**.
* **Hasil yang Diharapkan**:
  * Modal Konfirmasi Reservasi muncul dengan kode booking (contoh: `RSV-20260915-XXXXX`).
  * Backend POC berhasil mengeksekusi `POST /api/transaction/push/v2` ke server staging dengan respon `HTTP 200 SUCCESS`.
  * Nilai estimasi poin yang didapat tampil pada ringkasan pemesanan.

### [TC-TRX-02] Pengecekan Riwayat Transaksi Member di UI
* **Langkah Pengujian**:
  1. Klik nama profil member di kanan atas, buka menu **"My Transactions"** (atau ikon riwayat transaksi).
* **Hasil yang Diharapkan**:
  * Kode booking yang baru saja dibuat tampil di daftar riwayat transaksi member.
  * Rincian nominal transaksi dan status transaksi sesuai dengan yang tercatat di staging.

---

```
===================================================================================
FASE 8: EXTRANET & POINT RELEASE MECHANISM
===================================================================================
```

### [TC-EXT-01] Verifikasi Koneksi OAuth 2.1 di Extranet
* **Tujuan**: Memastikan admin properti dapat menguji keabsahan kredensial OAuth.
* **Langkah Pengujian**:
  1. Buka halaman Extranet: `/extranet`.
  2. Pilih properti **Adiwana Unagi**.
  3. Pastikan kolom `Client ID`, `Client Secret`, dan `Tenant Domain` terisi.
  4. Klik tombol **"Test Connection"**.
* **Hasil yang Diharapkan**:
  * Muncul badge hijau dengan status **"Connected"**.
  * Data Corporate, Branch, dan daftar Tier yang aktif di staging berhasil ditarik dan ditampilkan di Extranet.

### [TC-EXT-02] Simulasi Pelepasan Poin (Point Release)
* **Langkah Pengujian**:
  1. Di halaman Extranet, buka tab **"Reservations & Point Release"**.
  2. Cari reservasi yang baru saja dibuat di [TC-TRX-01].
  3. Periksa status pelepasan poin:
     * Jika mode **Immediate**: Poin langsung berstatus *Released / Materialized*.
     * Jika mode **Upon Check-in / Check-out**: Poin berstatus *Pending*, dan terdapat tombol aksi untuk simulasi Check-in atau Check-out.
  4. Klik tombol aksi pelepasan poin (misal: **"Simulate Check-in & Release Points"**).
* **Hasil yang Diharapkan**:
  * Status reservasi berubah menjadi *Released*.
  * Saldo poin di akun member otomatis bertambah saat dicek kembali di halaman Booking Engine.

---

## 4. Tabel Referensi Kode Respon & Error (Expected Error Codes)

Gunakan tabel ini untuk memastikan bahwa respon sistem sesuai dengan standar *Clean Architecture*:

| Skenario | Kode Respon / Error Code | Keterangan untuk QA |
| :--- | :--- | :--- |
| **Login Berhasil** | `OAUTH.LOGIN.SUCCESS` (HTTP 200) | Kredensial valid, token diterbitkan |
| **Password Salah** | `OAUTH.LOGIN.INVALID_PASSWORD` | Kata sandi tidak cocok |
| **Akun Tidak Ditemukan** | `OAUTH.LOGIN.ACCOUNT_NOT_FOUND` | Email belum terdaftar di tenant |
| **OTP Terkirim** | `OAUTH.OTP.REQUEST_SENT` (HTTP 200) | Kode 6-digit sukses diantrekan ke email |
| **OTP Salah** | `OAUTH.OTP.INVALID` | Kode verifikasi salah input |
| **OTP Kedaluwarsa** | `OAUTH.OTP.EXPIRED` | Melewati batas waktu 120 detik |
| **Koneksi Sukses** | `OAUTH.PROPERTY_CONTEXT.SUCCESS` | Kredensial client & tenant valid |
| **Client ID Salah** | `OAUTH.PROPERTY_CONTEXT.CLIENT_NOT_FOUND` | Client ID belum terdaftar di Passport staging |
| **Transaksi Sukses** | `TRANSACTION.PUSH_V2.SUCCESS` (HTTP 200) | Reservasi sukses masuk ke sistem loyalitas |

---

## 5. QA Sign-Off Checklist

Centang item ini saat pengujian selesai dilaksanakan:

- [ ] **[P0]** Member terdaftar dapat login via Password dan profil ter-update di header.
- [ ] **[P0]** Member dapat login via Email OTP tanpa kendala.
- [ ] **[P0]** Diskon tarif member otomatis memotong harga kamar secara real-time.
- [ ] **[P0]** Pemesanan kamar sukses mendorong data ke `POST /api/transaction/push/v2` staging.
- [ ] **[P0]** Poin member bertambah sesuai aturan Point Plan staging setelah status reservasi selesai.
- [ ] **[P1]** Registrasi member baru dengan kode referral berhasil tercatat di database staging.
- [ ] **[P1]** Riwayat transaksi member dapat dilihat langsung dari antarmuka web POC.
- [ ] **[P1]** Test Connection di Extranet menampilkan status *Connected*.
