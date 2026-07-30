# Plan: Fix Header — Logo & Nama Desa Muncul, Sub-menu Dropdown

## Summary

2 masalah di header child theme:
1. **Logo & nama desa tidak muncul** — saat ini cuma nampilin salah satu (logo ATAU nama), harusnya keduanya (logo + nama desa) biar khas web desa.
2. **Sub-menu (dropdown) selalu muncul** — karena `wp_nav_menu()` di header.php tidak pakai `wsbase_WP_Bootstrap_Navwalker`, jadinya markup menu nggak punya class Bootstrap (`.dropdown`, `.dropdown-menu`, `data-bs-toggle`), Bootstrap JS nggak jalan, sub-menu tampil terus.

## Current State

- **header.php**: Brand section pake `if/else` — logo ATAU site title. Menu desktop pake `wp_nav_menu()` tanpa walker.
- **desa.css**: Ada styling `.desa-nav .sub-menu` tapi nggak ada `display: none`, jadinya sub-menu kelihatan terus.
- **Parent theme** punya `wsbase_WP_Bootstrap_Navwalker` di `inc/class-wp-bootstrap-navwalker.php`, udah diload otomatis.

## Proposed Changes

### 1. `header.php` — Brand + Nav walker

**Brand section (lines 57-66)**: Ubah dari if/else jadi tampilkan keduanya:
- Custom logo (thumbnail/kecil) + nama desa, side by side
- Fallback: icon desa (svg) + nama desa kalo belum set logo

**Desktop nav menu (line 70)**: Tambahkan `'walker' => new wsbase_WP_Bootstrap_Navwalker()`:
- Biar menu items pake class Bootstrap: `.dropdown` di `<li>`, `.dropdown-menu` di `<ul>`, `data-bs-toggle="dropdown"` di link parent
- Bootstrap Dropdown JS udah ada di `theme.min.js` parent, tinggal pakai

**Mobile offcanvas nav**: Tetap tanpa walker (menu flat untuk mobile), nggak perlu dropdown di offcanvas.

### 2. `css/desa.css` — Fix sub-menu visibility

- Pastikan `.desa-nav .dropdown-menu` punya `display: none` (Bootstrap udah punya ini via `.dropdown-menu` class)
- Tambah hover style untuk desktop dropdown
- Hapus styling `.desa-nav .sub-menu` yang nggak perlu karena skrg pake class Bootstrap `.dropdown-menu`

### 3. (Optional) `js/desa.js` — Kalo perlu custom JS

Bisa tambah file JS baru kalo perlu custom hover behavior. Tapi Bootstrap Dropdown + parent theme JS (`custom-javascript.js`) udah cukup untuk hover effect.

## Files to Change

| File | Change |
|------|--------|
| `header.php` | Brand: logo + nama desa selalu tampil. Desktop nav: tambah `walker`. |
| `css/desa.css` | Update sub-menu/dropdown styling, pastikan `display: none` default. |

## Verification

1. Buka halaman — logo + nama desa muncul di navbar
2. Hover menu item yang punya sub-menu — dropdown muncul
3. Klik di luar dropdown — dropdown menutup
4. Mobile view — hamburger menu, offcanvas jalan

## Assumptions & Decisions

- Parent theme `wsbase_WP_Bootstrap_Navwalker` class udah auto-loaded (parent functions.php jalan duluan).
- Bootstrap Dropdown JS udah enqueued via parent `theme.min.js`.
- Nggak perlu file JS baru — parent JS udah handle hover dropdown.
