# Override Header, Footer & Template untuk Web Desa Spec

## Why

Parent theme wsbase punya header/footer generic. Web desa butuh identitas desa (logo desa, nama desa, alamat, kontak) dan integrasi mulus dengan plugin wp-desa (shortcode statistik, profil, keuangan, dll). Override header/footer/template di child theme biar tampilan cocok untuk website desa.

## What Changes

- **Override `header.php`** — header dengan navbar desa: logo desa, nama desa, navigasi utama, zona hero opsional
- **Override `footer.php`** — footer desa: identitas desa, kontak, sosial media, copyright, link cepat
- **Tambah CSS desa** di `style.css` — styling khas desa (warna hijau/biru alam, tipografi lokal) yg selaras dgn CSS variables plugin wp-desa
- **Override template `page.php`** — layout halaman desa dgn sidebar kiri (profil) + konten utama + widget
- **Update `functions.php`** — register menu baru (desa-top), enqueue custom CSS/JS, override fungsi parent via pluggable functions

## Impact

- Affected specs: child theme temadesa layout & styling
- Affected code: `header.php`, `footer.php`, `page.php`, `style.css`, `functions.php` di child theme

## ADDED Requirements

### Requirement: Header Desa
The system SHALL display header desa dengan elemen:
- Logo desa (custom logo) + nama desa di kiri
- Navigasi utama (primary menu) di kanan
- Navbar sticky di atas, responsive (mobile hamburger)
- Warna navbar menggunakan warna utama desa (default: hijau tua #1B5E20 atau biru #024AD8)

#### Scenario: Desktop header
- **WHEN** user mengakses website di layar >= 768px
- **THEN** navbar menampilkan logo di kiri, menu horizontal di kanan

#### Scenario: Mobile header
- **WHEN** user mengakses website di layar < 768px
- **THEN** navbar menampilkan toggle button, menu muncul via offcanvas/dropdown

### Requirement: Footer Desa
The system SHALL display footer desa dengan 3 kolom:
- Kolom 1: Logo desa + deskripsi singkat
- Kolom 2: Menu cepat (footer menu)
- Kolom 3: Kontak desa (alamat, telepon, email) + sosial media

#### Scenario: Footer bottom
- **WHEN** user scroll ke bawah halaman
- **THEN** footer bottom menampilkan copyright desa dan link kebijakan

### Requirement: Page Template Desa
The system SHALL override `page.php` dengan layout 2 kolom:
- Sidebar kiri (lebar 4 col) untuk widget profil desa, statistik
- Konten utama (lebar 8 col) untuk konten halaman
- Container responsif menggunakan Bootstrap classes

### Requirement: CSS Desa
The system SHALL menambahkan CSS khas desa:
- Warna: hijau alam (#2E7D32) atau biru (#024AD8) sebagai primary, krem (#F5F0E8) sebagai background
- Font: mempertahankan Google Fonts dari parent (Space Grotesk + Inter)
- Integrasi dgn CSS variables plugin wp-desa (--primary, --ink, dll)
- Styling tombol, link, border yg selaras dgn tema desa

### Requirement: wp-desa Integration
The system SHALL memastikan shortcode wp-desa tampil rapi di dalam layout desa:
- Shortcode profil desa, statistik, keuangan, aduan, layanan surat
- Wrapper `wp-desa-wrapper` mengikuti warna tema desa
- Tidak ada konflik CSS antara parent theme dan plugin

## MODIFIED Requirements

### Requirement: Functions yang Diupdate
**Child theme functions.php** — tambahkan:
- Enqueue custom CSS (`temadesa-desa-style`) setelah `temadesa-style`
- Filter body class untuk tambah class `page-desa`
- Override `wsbase_add_site_info()` untuk copyright khas desa
- Register menu location baru: `desa-top` (untuk link atas)

## REMOVED Requirements
Tidak ada.
