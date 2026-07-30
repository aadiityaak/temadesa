<?php
/**
 * Front page / Beranda — Desa template
 *
 * @package Temadesa
 */

defined('ABSPATH') || exit;

$wsdesa = get_option('wp_desa_settings', []);

$desa_nama      = !empty($wsdesa['nama_desa']) ? $wsdesa['nama_desa'] : get_bloginfo('name');
$desa_kec       = !empty($wsdesa['nama_kecamatan']) ? $wsdesa['nama_kecamatan'] : '';
$desa_kab       = !empty($wsdesa['nama_kabupaten']) ? $wsdesa['nama_kabupaten'] : '';
$desa_kades     = !empty($wsdesa['kepala_desa']) ? $wsdesa['kepala_desa'] : '';
$desa_foto_kades = !empty($wsdesa['foto_kepala_desa']) ? $wsdesa['foto_kepala_desa'] : '';
$desa_logo      = !empty($wsdesa['logo_kabupaten']) ? $wsdesa['logo_kabupaten'] : '';
$desa_alamat    = !empty($wsdesa['alamat_kantor']) ? $wsdesa['alamat_kantor'] : '';

$shortcode_statistik = '[wp_desa_statistik]';
$shortcode_berita    = '[wp_desa_berita limit="3"]';
$desa_desc           = get_bloginfo('description') ?: "Portal resmi $desa_nama — informasi pelayanan, potensi, dan pembangunan desa.";

// Carousel slides from theme options (dynamic count).
$carousel     = get_option('temadesa_carousel_slides', []);
if (empty($carousel)) {
	$carousel = [1 => []];
}

// Generate fallback gradient for any slide index.
$bg_gradients = [
	'linear-gradient(135deg,#0e3191 0%,#024ad8 60%,#1a5a9e 100%)',
	'linear-gradient(135deg,#0d47a1 0%,#1565c0 50%,#1976d2 100%)',
	'linear-gradient(135deg,#1a237e 0%,#283593 50%,#3949ab 100%)',
	'linear-gradient(135deg,#004d40 0%,#00695c 50%,#00897b 100%)',
	'linear-gradient(135deg,#4a148c 0%,#6a1b9a 50%,#8e24aa 100%)',
	'linear-gradient(135deg,#bf360c 0%,#d84315 50%,#e64a19 100%)',
];

// Build final slide data for JS.
$slide_data = [];
foreach ($carousel as $i => $s) {
	$is_slide1  = $i === 1;
	$has_image  = !empty($s['image']);
	$has_heading = !empty($s['heading']);

	// Background.
	$bg_idx  = min($i, count($bg_gradients)) - 1;
	$bg_style = $has_image
		? 'background:url(' . esc_url($s['image']) . ') center/cover no-repeat;'
		: 'background:' . $bg_gradients[$bg_idx] . ';';

	// Heading.
	$heading = '';
	if ($has_heading) {
		$heading = $s['heading'];
	} elseif ($is_slide1) {
		$heading = $desa_nama;
	} else {
		$heading = 'Slide ' . $i;
	}

	// Text.
	if (!empty($s['text'])) {
		$text = $s['text'];
	} elseif ($is_slide1) {
		$text = $desa_desc;
	} else {
		$text = $desa_kades
			? "\"Bersama membangun {$desa_nama} menuju desa yang mandiri, maju, dan sejahtera.\""
			: "Portal resmi {$desa_nama} — transparansi informasi dan pelayanan untuk masyarakat.";
	}

	// Buttons.
	$btn1_t = !empty($s['btn1_text']) ? $s['btn1_text'] : ($is_slide1 ? 'Profil Desa' : '');
	$btn1_u = !empty($s['btn1_url']) ? $s['btn1_url'] : ($is_slide1 ? '#profil' : '');
	$btn2_t = !empty($s['btn2_text']) ? $s['btn2_text'] : ($is_slide1 ? 'Layanan' : '');
	$btn2_u = !empty($s['btn2_url']) ? $s['btn2_url'] : ($is_slide1 ? '#layanan' : '');

	$slide_data[] = [
		'bg_style'     => $bg_style,
		'has_overlay'  => !$has_image,
		'show_logo'    => false,
		'logo_url'     => $desa_logo,
		'heading'      => $heading,
		'show_subtitle' => $is_slide1 && $desa_kec && $desa_kab && !$has_heading,
		'subtitle'     => "Kecamatan {$desa_kec}, {$desa_kab}",
		'text'         => $text,
		'btn1_text'    => $btn1_t,
		'btn1_url'     => $btn1_u,
		'btn2_text'    => $btn2_t,
		'btn2_url'     => $btn2_u,
		'show_cta'     => !empty($btn1_t) || !empty($btn2_t),
	];
}

get_header();
?>

	<!-- ========== HERO CAROUSEL (clone) ========== -->
	<section id="desaHeroCarousel" class="carousel slide desa-hero-carousel" data-bs-ride="carousel"
			 data-bs-interval="5000" data-bs-pause="false">
		<!-- Indicators (filled by JS) -->
		<div class="carousel-indicators" id="desa-indicators"></div>

		<!-- Carousel inner (filled by JS) -->
		<div class="carousel-inner" id="desa-carousel-inner"></div>

		<!-- Slide template -->
		<template id="desa-slide-tpl">
			<div class="carousel-item">
				<div class="desa-hero-slide d-flex align-items-center">
					<div class="desa-hero-overlay"></div>
					<div class="container position-relative text-center text-white">
						<img class="desa-hero-logo mb-3" height="72" alt="">
						<h1 class="desa-hero-title"></h1>
						<p class="desa-hero-subtitle"></p>
						<p class="desa-hero-desc lead"></p>
						<div class="desa-hero-cta mt-4">
							<a class="btn btn-light btn-lg me-2 desa-btn1"></a>
							<a class="btn btn-outline-light btn-lg desa-btn2"></a>
						</div>
					</div>
				</div>
			</div>
		</template>

		<!-- Controls -->
		<button class="carousel-control-prev" type="button" data-bs-target="#desaHeroCarousel" data-bs-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="visually-hidden">Previous</span>
		</button>
		<button class="carousel-control-next" type="button" data-bs-target="#desaHeroCarousel" data-bs-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="visually-hidden">Next</span>
		</button>
	</section>

	<script>
	<?php
	// Escape slide data for JSON.
	$json_data = wp_json_encode($slide_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
	?>
	var desaSlides = <?php echo $json_data; ?>;
	var tpl = document.getElementById('desa-slide-tpl');
	var inner = document.getElementById('desa-carousel-inner');
	var indicators = document.getElementById('desa-indicators');

	desaSlides.forEach(function(slide, i) {
		var clone = tpl.content.cloneNode(true);
		var item = clone.querySelector('.carousel-item');
		if (i === 0) item.classList.add('active');

		// Background
		var slideEl = clone.querySelector('.desa-hero-slide');
		slideEl.setAttribute('style', slide.bg_style);

		// Overlay — always visible (dark gradient)
		// (no conditional, overlay is in template)

		// Logo
		var logo = clone.querySelector('.desa-hero-logo');
		if (slide.show_logo) {
			logo.src = slide.logo_url;
			logo.alt = slide.heading;
		} else {
			logo.remove();
		}

		// Heading
		clone.querySelector('.desa-hero-title').textContent = slide.heading;

		// Subtitle
		var sub = clone.querySelector('.desa-hero-subtitle');
		if (slide.show_subtitle) {
			sub.textContent = slide.subtitle;
		} else {
			sub.remove();
		}

		// Text
		clone.querySelector('.desa-hero-desc').textContent = slide.text;

		// CTA buttons
		var btn1 = clone.querySelector('.desa-btn1');
		var btn2 = clone.querySelector('.desa-btn2');
		if (slide.show_cta) {
			if (slide.btn1_text) {
				btn1.textContent = slide.btn1_text;
				btn1.href = slide.btn1_url;
			} else { btn1.remove(); }
			if (slide.btn2_text) {
				btn2.textContent = slide.btn2_text;
				btn2.href = slide.btn2_url;
			} else { btn2.remove(); }
		} else {
			clone.querySelector('.desa-hero-cta').remove();
		}

		inner.appendChild(clone);

		// Indicator
		var btn = document.createElement('button');
		btn.type = 'button';
		btn.setAttribute('data-bs-target', '#desaHeroCarousel');
		btn.setAttribute('data-bs-slide-to', i);
		if (i === 0) btn.className = 'active';
		indicators.appendChild(btn);
	});
	</script>

	<!-- ========== FITUR / LAYANAN ========== -->
	<?php temadesa_render_feature_grid(); ?>

	<!-- ========== STATISTIK ========== -->
	<?php if (shortcode_exists('wp_desa_statistik')) : ?>
	<section class="desa-section desa-section-stats py-5">
		<div class="container">
			<?php echo do_shortcode($shortcode_statistik); ?>
		</div>
	</section>
	<?php else : ?>
	<section class="desa-section desa-section-stats py-5">
		<div class="container">
			<div class="row g-4 text-center">
				<div class="col-6 col-md-3">
					<div class="desa-stat-card">
						<div class="desa-stat-number">--</div>
						<div class="desa-stat-label">Jumlah Penduduk</div>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<div class="desa-stat-card">
						<div class="desa-stat-number">--</div>
						<div class="desa-stat-label">Luas Wilayah</div>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<div class="desa-stat-card">
						<div class="desa-stat-number">--</div>
						<div class="desa-stat-label">Dusun</div>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<div class="desa-stat-card">
						<div class="desa-stat-number">--</div>
						<div class="desa-stat-label">RT / RW</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- ========== WELCOME / PROFIL ========== -->
	<section id="profil" class="desa-section desa-section-welcome py-5">
		<div class="container">
			<div class="row align-items-center g-5">
				<div class="col-lg-6">
					<span class="desa-section-tag">SELAMAT DATANG</span>
					<h2 class="desa-section-title">
						<?php echo esc_html($desa_nama); ?>
					</h2>
					<?php if ($desa_kec && $desa_kab) : ?>
						<p class="desa-section-location text-muted mb-3">
							Kecamatan <?php echo esc_html($desa_kec); ?>, <?php echo esc_html($desa_kab); ?>
						</p>
					<?php endif; ?>
					<div class="desa-section-desc">
						<?php if (shortcode_exists('wp_desa_profil')) : ?>
							<?php echo do_shortcode('[wp_desa_profil]'); ?>
						<?php else : ?>
							<p>Selamat datang di portal resmi <?php echo esc_html($desa_nama); ?>.
							Website ini menyajikan informasi tentang pelayanan, potensi, dan pembangunan desa.</p>
						<?php endif; ?>
					</div>
					<a href="<?php echo esc_url(get_permalink(get_page_by_path('profil-desa')) ?: home_url('/profil')); ?>"
					   class="btn btn-primary mt-3">Selengkapnya &rarr;</a>
				</div>
				<div class="col-lg-6">
					<div class="desa-section-image rounded-4 overflow-hidden shadow-sm">
						<?php if (has_custom_logo()) : ?>
							<div class="p-5 text-center bg-light">
								<?php the_custom_logo(); ?>
							</div>
						<?php elseif ($desa_logo) : ?>
							<img src="<?php echo esc_url($desa_logo); ?>"
								 alt="<?php echo esc_attr($desa_nama); ?>"
								 class="img-fluid p-5 bg-light">
						<?php else : ?>
							<div class="desa-section-placeholder d-flex align-items-center justify-content-center">
								<span><?php echo esc_html($desa_nama); ?></span>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- ========== LAYANAN / SERVICES ========== -->
	<section id="layanan" class="desa-section desa-section-services py-5 bg-light">
		<div class="container">
			<div class="text-center mb-5">
				<span class="desa-section-tag">LAYANAN</span>
				<h2 class="desa-section-title">Layanan Desa</h2>
				<p class="text-muted">Akses layanan administrasi dan informasi desa</p>
			</div>

			<?php if (shortcode_exists('wp_desa_layanan')) : ?>
				<?php echo do_shortcode('[wp_desa_layanan style="grid"]'); ?>
			<?php else : ?>
			<div class="row g-4">
				<div class="col-md-4 col-6">
					<div class="desa-service-card text-center p-4 bg-white rounded-4 shadow-sm h-100">
						<div class="desa-service-icon">📋</div>
						<h5>Surat Keterangan</h5>
						<p class="small text-muted">Pengajuan surat keterangan desa</p>
					</div>
				</div>
				<div class="col-md-4 col-6">
					<div class="desa-service-card text-center p-4 bg-white rounded-4 shadow-sm h-100">
						<div class="desa-service-icon">📊</div>
						<h5>Data Penduduk</h5>
						<p class="small text-muted">Informasi data kependudukan</p>
					</div>
				</div>
				<div class="col-md-4 col-6">
					<div class="desa-service-card text-center p-4 bg-white rounded-4 shadow-sm h-100">
						<div class="desa-service-icon">💰</div>
						<h5>Informasi Keuangan</h5>
						<p class="small text-muted">Laporan APBDes dan keuangan desa</p>
					</div>
				</div>
				<div class="col-md-4 col-6">
					<div class="desa-service-card text-center p-4 bg-white rounded-4 shadow-sm h-100">
						<div class="desa-service-icon">📢</div>
						<h5>Pengaduan</h5>
						<p class="small text-muted">Sampaikan aspirasi dan pengaduan</p>
					</div>
				</div>
				<div class="col-md-4 col-6">
					<div class="desa-service-card text-center p-4 bg-white rounded-4 shadow-sm h-100">
						<div class="desa-service-icon">🏡</div>
						<h5>Potensi Desa</h5>
						<p class="small text-muted">Informasi potensi dan UMKM</p>
					</div>
				</div>
				<div class="col-md-4 col-6">
					<div class="desa-service-card text-center p-4 bg-white rounded-4 shadow-sm h-100">
						<div class="desa-service-icon">📅</div>
						<h5>Agenda Desa</h5>
						<p class="small text-muted">Jadwal kegiatan dan agenda desa</p>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- ========== BERITA ========== -->
	<section class="desa-section desa-section-news py-5">
		<div class="container">
			<div class="d-flex justify-content-between align-items-center mb-5">
				<div>
					<span class="desa-section-tag">BERITA</span>
					<h2 class="desa-section-title mb-0">Berita Desa</h2>
				</div>
				<a href="<?php echo esc_url(get_post_type_archive_link('post') ?: home_url('/berita')); ?>"
				   class="btn btn-outline-primary">Lihat Semua &rarr;</a>
			</div>

			<?php if (shortcode_exists('wp_desa_berita')) : ?>
				<?php echo do_shortcode($shortcode_berita); ?>
			<?php else : ?>
				<?php
				$recent = new WP_Query([
					'post_type'      => 'post',
					'posts_per_page' => 3,
					'ignore_sticky_posts' => true,
				]);
				?>
				<?php if ($recent->have_posts()) : ?>
				<div class="row g-4">
					<?php while ($recent->have_posts()) : $recent->the_post(); ?>
					<div class="col-md-4">
						<article class="desa-news-card rounded-4 overflow-hidden shadow-sm h-100 bg-white">
							<?php if (has_post_thumbnail()) : ?>
								<?php the_post_thumbnail('medium', ['class' => 'w-100', 'style' => 'height:200px;object-fit:cover;']); ?>
							<?php else : ?>
								<div class="desa-news-thumb-placeholder d-flex align-items-center justify-content-center"
									 style="height:200px;background:var(--desa-bg-warm);color:var(--desa-text-light);">
									<span><?php echo esc_html($desa_nama); ?></span>
								</div>
							<?php endif; ?>
							<div class="p-3">
								<span class="badge bg-primary mb-2"><?php echo get_the_date('d M Y'); ?></span>
								<h5 class="desa-news-title"><?php the_title(); ?></h5>
								<p class="small text-muted"><?php echo wp_trim_words(get_the_excerpt() ?: get_the_content(), 15); ?></p>
								<a href="<?php the_permalink(); ?>" class="stretched-link">Baca selengkapnya</a>
							</div>
						</article>
					</div>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
				<?php else : ?>
				<p class="text-muted">Belum ada berita.</p>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</section>

	<!-- ========== KEPALA DESA ========== -->
	<?php if ($desa_kades || shortcode_exists('wp_desa_kepala_desa')) : ?>
	<section class="desa-section desa-section-kades py-5 bg-primary text-white">
		<div class="container text-center">
			<span class="desa-section-tag text-white-50">PEMERINTAH DESA</span>
			<h2 class="desa-section-title text-white">Kepala Desa</h2>

			<?php if (shortcode_exists('wp_desa_kepala_desa')) : ?>
				<?php echo do_shortcode('[wp_desa_kepala_desa]'); ?>
			<?php else : ?>
			<div class="row justify-content-center mt-4">
				<div class="col-md-6 col-lg-4">
					<div class="desa-kades-card">
						<?php if ($desa_foto_kades) : ?>
							<img src="<?php echo esc_url($desa_foto_kades); ?>"
								 alt="<?php echo esc_attr($desa_kades); ?>"
								 class="rounded-circle shadow mb-3"
								 width="150" height="150"
								 style="object-fit:cover;border:4px solid rgba(255,255,255,0.3);">
						<?php endif; ?>
						<h4 class="text-white mb-1"><?php echo esc_html($desa_kades); ?></h4>
						<p class="text-white-50 mb-0">Kepala Desa <?php echo esc_html($desa_nama); ?></p>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<!-- ========== PETA / LOKASI ========== -->
	<?php if (shortcode_exists('wp_desa_peta')) : ?>
	<section class="desa-section desa-section-map py-5">
		<div class="container">
			<div class="text-center mb-5">
				<span class="desa-section-tag">LOKASI</span>
				<h2 class="desa-section-title">Peta Desa</h2>
			</div>
			<?php echo do_shortcode('[wp_desa_peta]'); ?>
		</div>
	</section>
	<?php endif; ?>

<?php
get_footer();
