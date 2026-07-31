<?php
/**
 * Single Berita Desa — hero + cover + konten.
 *
 * @package Temadesa
 */

defined('ABSPATH') || exit;

get_header();

$desa_page  = get_queried_object();
$desa_cats  = get_the_terms(get_the_ID(), 'desa_berita_cat');
$desa_arch  = get_post_type_archive_link('desa_berita');
?>

<!-- Page Hero — title + breadcrumb -->
<?php if ($desa_page instanceof WP_Post) : ?>
	<section class="desa-page-hero">
		<div class="container">
			<?php temadesa_breadcrumb($desa_page); ?>
			<h1 class="desa-page-hero-title"><?php echo esc_html(get_the_title($desa_page)); ?></h1>
			<div class="desa-single-meta">
				<span><?php echo esc_html(get_the_date()); ?></span>
				<?php if ($desa_cats && !is_wp_error($desa_cats)) : ?>
					<span class="desa-single-cat"><?php echo esc_html($desa_cats[0]->name); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<div class="wrapper desa-page-wrapper" id="page-wrapper">
	<div class="container desa-single-container" id="content" tabindex="-1">
		<main class="site-main" id="main">
			<?php
			while (have_posts()) {
				the_post();
				?>
				<article <?php post_class('desa-single-berita'); ?> id="post-<?php the_ID(); ?>">

					<?php if (has_post_thumbnail()) : ?>
						<div class="desa-single-cover mb-4">
							<?php the_post_thumbnail('large', [
								'class' => 'img-fluid w-100 rounded-4',
								'style' => 'object-fit:cover;max-height:440px;',
							]); ?>
						</div>
					<?php endif; ?>

					<div class="entry-content">
						<?php the_content(); ?>
					</div>

					<?php
					wp_link_pages(array(
						'before' => '<div class="page-links">' . __('Pages:', 'temadesa'),
						'after'  => '</div>',
					));
					?>

					<?php if ($desa_arch) : ?>
						<div class="mt-5">
							<a href="<?php echo esc_url($desa_arch); ?>" class="desa-btn desa-btn-primary">
								&larr; <?php esc_html_e('Kembali ke Berita', 'temadesa'); ?>
							</a>
						</div>
					<?php endif; ?>

					<?php if (comments_open() || get_comments_number()) : ?>
						<div class="mt-5">
							<?php comments_template(); ?>
						</div>
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
