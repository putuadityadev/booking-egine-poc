# PRD: Third-Party Booking Engine Multi-Property POC Simulation

## 1. Overview
Aplikasi percontohan (Proof of Concept / POC) antarmuka web reservasi hotel multi-properti yang mensimulasikan platform pemesanan kamar pihak ketiga (*Third-Party Booking Engine*). Fitur ini mendemonstrasikan integrasi sistem loyalitas keanggotaan (*Membership Platform*) secara langsung: menampilkan katalog properti, harga reguler vs harga diskon member dinamis, alur masuk/daftar keanggotaan mandiri (kata sandi, OTP, Google), serta simulasi reservasi kamar.

## 2. Background & Context
Selama ini integrasi keanggotaan dengan Booking Engine masih menggunakan mekanisme lama berbasis sesi popup dan token akses layanan. Dalam arsitektur baru, platform keanggotaan telah menyediakan rangkaian otentikasi mandiri modern (OAuth 2.1) yang aman dan multi-tenant. Sebelum diimplementasikan penuh pada sistem produksi Booking Engine utama, CTO dan pemangku kepentingan membutuhkan simulasi visual nyata (POC) untuk memvalidasi alur interaksi pengguna tamu (*Guest Experience*), konsistensi harga berjenjang (*tier rates*), dan kelayakan alur integrasi multi-properti.

## 3. Goals & Success Metrics
- **Tujuan Produk**:
  - Menyediakan media demonstrasi interaktif yang membuktikan integrasi otentikasi keanggotaan berjalan lancar pada aplikasi pemesanan kamar pihak ketiga.
  - Membuktikan dukungan reservasi pada beberapa properti hotel sekaligus (3-5 properti percontohan).
  - Memberikan visualisasi jelas mengenai nilai tambah menjadi anggota (diskon harga otomatis berdasarkan tingkatan loyalitas tamu).
- **Metrik Keberhasilan**:
  - 100% alur masuk anggota (kata sandi, kode verifikasi email OTP, dan akun Google) berhasil diverifikasi dan menampilkan data profil yang akurat.
  - Perubahan harga kamar dari tarif umum menjadi tarif khusus anggota terjadi seketika (*real-time*) setelah tamu berhasil masuk.
  - Tamu dapat beralih di antara 3 hingga 5 properti hotel percontohan dengan konfigurasi dan identitas loyalitas masing-masing.
  - Alur simulasi pemesanan kamar dapat diselesaikan hingga tahap konfirmasi tanpa kendala.

## 4. User Stories / Use Cases
- **Sebagai Calon Tamu Hotel (Belum Masuk)**:
  - Saya ingin melihat katalog properti hotel dan pilihan kamar dengan harga reguler beserta penawaran hemat jika masuk sebagai anggota, agar saya tertarik mendaftar atau masuk ke akun keanggotaan saya.
  - Saya ingin memilih metode masuk yang nyaman bagi saya (kata sandi, kode verifikasi email tanpa kata sandi, atau akun Google) langsung dari halaman pemesanan tanpa dialihkan ke situs lain.
- **Sebagai Anggota Terdaftar (Sudah Masuk)**:
  - Saya ingin melihat informasi tingkatan keanggotaan (*tier*) dan saldo poin saya di bilah navigasi atas, agar saya tahu status loyalitas saya aktif.
  - Saya ingin harga kamar otomatis terpotong sesuai persentase diskon tingkatan saya (harga lama dicoret dan diganti harga hemat anggota), agar saya merasakan keuntungan eksklusif keanggotaan.
  - Saya ingin melakukan reservasi kamar dengan mengonfirmasi data pemesanan secara instan.
- **Sebagai Pengelola Properti / Penguji Sistem (Demo)**:
  - Saya ingin memilih di antara 3-5 properti hotel percontohan yang tersedia untuk menguji isolasi data dan perbedaan pengaturan antar-properti.

## 5. Scope
### In Scope
- Tampilan pemilihan properti hotel (3 sampai 5 properti dummy).
- Bilah navigasi atas dengan indikator status tamu (tombol Masuk saat belum masuk, dan kartu profil ringkas saat sudah masuk: foto, nama, nama tingkatan, saldo poin, dan tombol Keluar).
- Jendela sembulan (*modal dialog*) masuk anggota dengan 3 opsi:
  1. Masuk dengan Email & Kata Sandi
  2. Masuk dengan Kode Verifikasi Email (OTP)
  3. Masuk dengan Akun Google
- Katalog kamar percontohan (2-3 tipe kamar per properti) menampilkan fasilitas, foto, ketersediaan, serta label harga reguler vs harga khusus anggota.
- Kalkulasi dinamis harga kamar anggota: harga asli dicoret dan menampilkan harga diskon sesuai tingkatan member yang aktif.
- Jendela sembulan konfirmasi reservasi percontohan (*dummy booking modal*) berisi ringkasan tamu, tanggal menginap, rincian biaya, dan tombol konfirmasi pemesanan.
- Alur keluar sesi (*logout*) yang mengembalikan tampilan harga ke tarif reguler secara seketika.

### Out of Scope
- Integrasi gerbang pembayaran riil (*payment gateway*) — pembayaran disimulasikan sukses otomatis.
- Sistem inventaris ketersediaan kamar riil (allotment kamar statis/dummy).
- Pengelolaan kamar/tarif di sisi pengelola hotel (manajemen extranet).
- Alur reset kata sandi di dalam aplikasi pemesanan (tetap diarahkan ke portal anggota existing jika diperlukan).

## 6. Functional Requirements
1. **Pemilihan Properti**:
   - Sistem menyediakan menu dropdown untuk memilih 1 dari 3-5 properti hotel percontohan.
   - Setiap kali properti diganti, katalog kamar, identitas properti, dan pengaturan integrasi keanggotaan menyesuaikan dengan properti yang dipilih.
2. **Katalog Kamar & Tampilan Harga**:
   - Setiap kamar memiliki harga publik (harga reguler non-anggota).
   - Kamar yang memenuhi syarat diskon anggota menampilkan penanda *"Harga Khusus Member"*.
   - Saat tamu belum masuk, ditampilkan harga publik dengan informasi potensi hemat.
   - Saat tamu sudah masuk, harga kamar otomatis dihitung ulang sesuai tingkatan member (misal Diskon 10% untuk Silver, 15% untuk Gold, 20% untuk Diamond) dengan menampilkan coretan pada harga reguler.
3. **Autentikasi & Profil Anggota**:
   - Tamu dapat membuka modal masuk kapan saja dari bilah navigasi atau tombol di kartu kamar.
   - Pilihan metode masuk: Email + Kata Sandi, Email OTP (kirim kode 6 digit ke email), atau Akun Google.
   - Setelah berhasil masuk, status tamu tersimpan dan bilah navigasi memperbarui tampilan dengan nama tamu, lencana tingkatan (*tier badge*), dan total poin.
4. **Simulasi Pemesanan**:
   - Menekan tombol "Pesan Sekarang" membuka ringkasan reservasi: nama properti, tipe kamar, nama tamu pemesan, harga akhir per malam, dan estimasi poin yang akan didapatkan.
   - Menekan tombol "Konfirmasi Pemesanan" menampilkan status reservasi berhasil dibuat beserta nomor referensi reservasi percontohan.
5. **Pembersihan Sesi (Logout)**:
   - Tamu dapat keluar kapan saja melalui menu profil.
   - Setelah keluar, tampilan halaman langsung kembali ke status pengunjung umum dan seluruh harga kamar kembali ke harga reguler tanpa perlu memuat ulang seluruh halaman (*no hard refresh*).

## 7. UX Notes
- **Gaya Desain**: Bersih, modern, bernuansa portal pemesanan hotel premium (*hospitality luxury theme*), responsif untuk layar desktop maupun ponsel.
- **Hierarki Informasi**: Penekanan visual pada keuntungan harga anggota (warna aksen khusus untuk harga hemat dan lencana tingkatan) agar kontras dengan harga umum.
- **Umpan Balik Antarmuka**: Indikator loading yang jelas saat proses verifikasi masuk dan pesan kesalahan yang ramah pengguna jika kredensial salah.

## 8. Business Constraints & Dependencies
- Bergantung pada ketersediaan layanan sistem keanggotaan multi-tenant yang sudah mendukung standar otentikasi baru (OAuth 2.1).
- Bersifat percontohan (*Proof of Concept*), sehingga data properti dan kamar dibatasi 3-5 entitas statis agar fokus pada pembuktian alur integrasi tanpa membebani jadwal rilis.

## 9. Timeline / Milestone
- **Fase 1**: Penyusunan dokumen PRD dan rancangan arsitektur teknis (TDD).
- **Fase 2**: Implementasi skema data properti percontohan dan lapisan perantara backend (BFF).
- **Fase 3**: Pembangunan antarmuka web pemesanan (katalog, modal autentikasi, dan kalkulator harga dinamis).
- **Fase 4**: Pengujian terpadu dan demonstrasi di hadapan CTO dan tim pemangku kepentingan.

## 10. Open Questions
- *Tidak ada*: Seluruh alur fungsional telah diselaraskan dengan kebutuhan demonstrasi Epic dan pola integrasi sistem produksi yang ada.
