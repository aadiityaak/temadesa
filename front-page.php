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
			} else {
				btn1.remove();
			}
			if (slide.btn2_text) {
				btn2.textContent = slide.btn2_text;
				btn2.href = slide.btn2_url;
			} else {
				btn2.remove();
			}
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

<!-- ========== RINGKASAN ========== -->
<section class="desa-section desa-section-ringkasan py-5 bg-light">
	<div class="container">
		<div class="text-center mb-5">
			<span class="desa-section-tag">LAYANAN DESA</span>
			<h2 class="desa-section-title">Informasi Pelayanan Desa</h2>
			<p class="text-muted">Informasi jam kerja, statistik penduduk, dan laporan keuangan <?php echo esc_html($desa_nama); ?></p>
		</div>
		<?php echo do_shortcode('[wp_desa_ringkasan]'); ?>
	</div>
</section>

<!-- ========== BLOG / ARTIKEL TERBARU ========== -->
<section id="blog" class="desa-section desa-section-blog py-5">
	<div class="container">
		<div class="text-center mb-5">
			<span class="desa-section-tag">BERITA &amp; ARTIKEL</span>
			<h2 class="desa-section-title">Informasi Terbaru</h2>
			<p class="text-muted">Berita, pengumuman, dan kegiatan <?php echo esc_html($desa_nama); ?></p>
		</div>

		<div class="row g-4">
			<?php
			$blog_query = new WP_Query([
				'posts_per_page' => 6,
				'ignore_sticky_posts' => true,
			]);
			if ($blog_query->have_posts()) :
				while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
					<div class="col-md-6 col-lg-4">
						<article class="desa-blog-card h-100">
							<?php if (has_post_thumbnail()) : ?>
								<a href="<?php the_permalink(); ?>" class="desa-blog-thumb">
									<?php the_post_thumbnail('medium', ['class' => 'img-fluid w-100', 'loading' => 'lazy']); ?>
								</a>
							<?php else : ?>
								<div class="desa-blog-thumb desa-blog-thumb-placeholder d-flex align-items-center justify-content-center bg-light">
									<span class="text-muted" style="font-size:2rem;">📰</span>
								</div>
							<?php endif; ?>
							<div class="desa-blog-body">
								<div class="desa-blog-meta">
									<span class="desa-blog-date"><?php echo get_the_date('d M Y'); ?></span>
									<?php
									$categories = get_the_category();
									if (!empty($categories)) :
										$cat = $categories[0]; ?>
										<span class="desa-blog-cat"><?php echo esc_html($cat->name); ?></span>
									<?php endif; ?>
								</div>
								<h3 class="desa-blog-title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h3>
								<p class="desa-blog-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 18); ?></p>
								<a href="<?php the_permalink(); ?>" class="desa-blog-readmore">Baca Selengkapnya &rarr;</a>
							</div>
						</article>
					</div>
				<?php endwhile;
				wp_reset_postdata();
			else : ?>
				<div class="col-12 text-center py-5">
					<p class="text-muted">Belum ada artikel.</p>
				</div>
			<?php endif; ?>
		</div>

		<div class="text-center mt-4">
			<a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog')); ?>"
				class="btn btn-outline-primary">Lihat Semua Artikel &rarr;</a>
		</div>
	</div>
</section>

<!-- ========== PETA & LOKASI ========== -->
<section class="desa-section desa-section-map py-5">
	<div class="container">
		<div class="text-center mb-5">
			<span class="desa-section-tag">LOKASI</span>
			<h2 class="desa-section-title">Peta &amp; Alamat Desa</h2>
		</div>
		<div class="row g-5 align-items-start">
			<div class="col-lg-5">
				<div class="bg-white rounded-4 shadow-sm p-4 h-100 border">
					<h5 class="fw-bold mb-3" style="color:var(--desa-ink);">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1" style="vertical-align:-2px;">
							<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
							<circle cx="12" cy="10" r="3" />
						</svg>
						<?php echo esc_html($desa_nama); ?>
					</h5>
					<table class="desa-lokasi-table">
						<tbody>
							<?php if ($desa_alamat) : ?>
								<tr>
									<td class="desa-lokasi-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
											<circle cx="12" cy="10" r="3" />
										</svg></td>
									<td><strong>Alamat</strong><br><span class="text-muted"><?php echo esc_html($desa_alamat); ?></span></td>
								</tr>
							<?php endif; ?>
							<?php if ($desa_kec) : ?>
								<tr>
									<td class="desa-lokasi-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<rect x="3" y="3" width="18" height="18" rx="2" />
											<path d="M3 9h18" />
											<path d="M9 3v18" />
										</svg></td>
									<td><strong>Kecamatan</strong><br><span class="text-muted"><?php echo esc_html($desa_kec); ?></span></td>
								</tr>
							<?php endif; ?>
							<?php if ($desa_kab) : ?>
								<tr>
									<td class="desa-lokasi-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
											<polyline points="22 4 12 14.01 9 11.01" />
										</svg></td>
									<td><strong>Kabupaten/Kota</strong><br><span class="text-muted"><?php echo esc_html($desa_kab); ?></span></td>
								</tr>
							<?php endif; ?>
							<?php if (!empty($wsdesa['telepon_desa'])) : ?>
								<tr>
									<td class="desa-lokasi-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
										</svg></td>
									<td><strong>Telepon/WA</strong><br><span class="text-muted"><?php echo esc_html($wsdesa['telepon_desa']); ?></span></td>
								</tr>
							<?php endif; ?>
							<?php if (!empty($wsdesa['email_desa'])) : ?>
								<tr>
									<td class="desa-lokasi-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
											<polyline points="22,6 12,13 2,6" />
										</svg></td>
									<td><strong>Email</strong><br><span class="text-muted"><?php echo esc_html($wsdesa['email_desa']); ?></span></td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-lg-7">
				<?php if (shortcode_exists('wp_desa_peta')) : ?>
					<?php echo do_shortcode('[wp_desa_peta]'); ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
