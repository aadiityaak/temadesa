<?php
/**
 * Single Galeri Desa — hero + grid foto + lightbox.
 *
 * @package Temadesa
 */

defined('ABSPATH') || exit;

// Lightbox (sama dengan shortcode wp_desa_galeri / plugin).
wp_enqueue_style('glightbox', 'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css', [], '3.3.0');
wp_enqueue_script('glightbox', 'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js', [], '3.3.0', true);
wp_add_inline_script('glightbox', 'document.addEventListener("DOMContentLoaded", function(){ if (window.GLightbox) GLightbox({ selector: ".glightbox" }); });');

get_header();

$desa_page   = get_queried_object();
$galeri_type = get_post_meta(get_the_ID(), '_desa_galeri_type', true) ?: 'foto';
$galeri_ids  = array_filter(array_map('intval', explode(',', (string) get_post_meta(get_the_ID(), '_desa_galeri_images', true))));
?>

<!-- Page Hero — title + breadcrumb -->
<?php if ($desa_page instanceof WP_Post) : ?>
	<section class="desa-page-hero">
		<div class="container">
			<?php temadesa_breadcrumb($desa_page); ?>
			<h1 class="desa-page-hero-title"><?php echo esc_html(get_the_title($desa_page)); ?></h1>
			<?php if (has_excerpt($desa_page)) : ?>
				<p class="desa-page-hero-desc"><?php echo esc_html(get_the_excerpt($desa_page)); ?></p>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<div class="wrapper desa-page-wrapper" id="page-wrapper">
	<div class="container" id="content" tabindex="-1">
		<main class="site-main" id="main">
			<?php
			while (have_posts()) {
				the_post();
				?>
				<article <?php post_class('desa-galeri-single'); ?> id="post-<?php the_ID(); ?>">

					<?php if (has_post_thumbnail()) : ?>
						<div class="desa-galeri-cover mb-4">
							<?php the_post_thumbnail('large', [
								'class' => 'img-fluid w-100 rounded-4',
								'style' => 'object-fit:cover;max-height:420px;',
							]); ?>
						</div>
					<?php endif; ?>

					<div class="entry-content mb-4">
						<?php the_content(); ?>
					</div>

					<?php if ($galeri_ids) : ?>
						<div class="row g-3 desa-galeri-grid">
							<?php foreach ($galeri_ids as $att_id) :
								$full    = wp_get_attachment_image_url($att_id, 'full');
								$caption = wp_get_attachment_caption($att_id);
								if (!$full) {
									continue;
								}
								?>
								<div class="col-6 col-md-4 col-lg-3">
									<a href="<?php echo esc_url($full); ?>"
									   class="glightbox desa-galeri-item d-block rounded-3 overflow-hidden position-relative"
									   data-gallery="desa-galeri"
										<?php echo $caption ? 'data-glightbox="title: ' . esc_attr($caption) . '"' : ''; ?>>
										<?php echo wp_get_attachment_image($att_id, 'medium', false, [
											'class' => 'w-100',
											'style' => 'aspect-ratio:1;object-fit:cover;',
										]); ?>
										<?php if ($galeri_type === 'video') : ?>
											<span class="desa-galeri-play" aria-hidden="true">&#9654;</span>
										<?php endif; ?>
									</a>
								</div>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<p class="text-muted">Belum ada foto dalam galeri ini.</p>
					<?php endif; ?>

				</article>
				<?php
			}
			?>
		</main>
	</div><!-- #content -->
</div><!-- #page-wrapper -->

<?php
get_footer();
