<?php
/**
 * Feature pages — config, helpers, frontend grid, admin generate.
 *
 * @package Temadesa
 */

defined('ABSPATH') || exit;

/**
 * Return feature pages configuration.
 * Each maps to a WP-Desa shortcode.
 */
function temadesa_get_feature_pages(): array
{
	return [
		'layanan' => [
			'title'    => 'Layanan Mandiri',
			'desc'     => 'Ajukan surat keterangan dan layanan administrasi secara online.',
			'shortcode' => '[wp_desa_layanan]',
			'slug'     => 'layanan-mandiri',
			'icon'     => 'clipboard-list',
		],
		'produk-hukum' => [
			'title'    => 'Produk Hukum',
			'desc'     => 'Kumpulan peraturan desa, perdes, dan keputusan kepala desa.',
			'shortcode' => '[wp_desa_produk_hukum]',
			'slug'     => 'produk-hukum',
			'icon'     => 'award',
		],
		'keuangan' => [
			'title'    => 'Pembangunan',
			'desc'     => 'Informasi APBDes, anggaran, dan realisasi pembangunan desa.',
			'shortcode' => '[wp_desa_keuangan style="minimal"]',
			'slug'     => 'keuangan-desa',
			'icon'     => 'trending-up',
		],
		'umkm' => [
			'title'    => 'Lapak UMKM',
			'desc'     => 'Direktori produk usaha mikro, kecil, dan menengah desa.',
			'shortcode' => '[wp_desa_umkm]',
			'slug'     => 'umkm-desa',
			'icon'     => 'store',
		],
		'berita' => [
			'title'    => 'Berita Desa',
			'desc'     => 'Arsip berita, artikel, dan informasi kegiatan desa.',
			'shortcode' => '[wp_desa_berita]',
			'slug'     => 'berita-desa',
			'icon'     => 'edit',
		],
		'aparatur' => [
			'title'    => 'Aparatur Desa',
			'desc'     => 'Struktur organisasi perangkat desa dan pemerintah desa.',
			'shortcode' => '[wp_desa_struktur]',
			'slug'     => 'aparatur-desa',
			'icon'     => 'users',
		],
		'pengaduan' => [
			'title'    => 'Pengaduan',
			'desc'     => 'Sampaikan aspirasi, pengaduan, dan usulan warga desa.',
			'shortcode' => '[wp_desa_aduan]',
			'slug'     => 'pengaduan',
			'icon'     => 'message-circle',
		],
		'profil' => [
			'title'    => 'Profil Desa',
			'desc'     => 'Informasi umum desa, visi misi, dan data demografi penduduk.',
			'shortcode' => '[wp_desa_profil]',
			'slug'     => 'profil-desa',
			'icon'     => 'home',
		],
	];
}

/**
 * Check if a page exists by slug.
 */
function temadesa_page_exists(string $slug): ?int
{
	$page = get_page_by_path(sanitize_title($slug));
	return $page ? (int) $page->ID : null;
}

/**
 * Get URL of a page by slug, or '#' if not exists.
 */
function temadesa_page_url(string $slug): string
{
	$id = temadesa_page_exists($slug);
	return $id ? get_permalink($id) : '#';
}

/**
 * Generate all feature pages that don't exist yet.
 * Returns array of generated page IDs keyed by feature key.
 */
function temadesa_generate_feature_pages(): array
{
	$features = temadesa_get_feature_pages();
	$created  = [];

	foreach ($features as $key => $feat) {
		$slug = sanitize_title($feat['slug']);
		if (temadesa_page_exists($slug)) {
			continue;
		}

		$id = wp_insert_post([
			'post_title'   => $feat['title'],
			'post_name'    => $slug,
			'post_content' => $feat['shortcode'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
		]);

		if ($id && !is_wp_error($id)) {
			$created[$key] = $id;
		}
	}

	return $created;
}

/**
 * Render feature grid for front-page.
 */
function temadesa_render_feature_grid(): void
{
	$features = temadesa_get_feature_pages();
	?>
	<section class="desa-features">
		<div class="container">
			<div class="desa-features-header text-center">
				<h2 class="desa-features-title">Layanan & Informasi Desa</h2>
				<p class="desa-features-subtitle">
					Portal pelayanan terpadu <?php echo esc_html(get_bloginfo('name')); ?>
				</p>
			</div>
			<div class="row g-4 justify-content-center">
				<?php foreach ($features as $key => $feat) :
					$url  = temadesa_page_url($feat['slug']);
					$icon = '';
					if (class_exists('WpDesa\Frontend\Icons')) {
						$icon = WpDesa\Frontend\Icons::svg($feat['icon'], 'width:32px;height:32px;');
					}
				?>
				<div class="col-6 col-md-4 col-lg-3">
					<a href="<?php echo esc_url($url); ?>" class="desa-feature-card">
						<span class="desa-feature-icon">
							<?php if ($icon) : ?>
								<?php echo $icon; ?>
							<?php else : ?>
								<span class="dashicons dashicons-admin-generic"></span>
							<?php endif; ?>
						</span>
						<span class="desa-feature-label"><?php echo esc_html($feat['title']); ?></span>
					</a>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}
