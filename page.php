<?php

/**
 * The template for displaying all pages - Desa layout
 *
 * Layout 2 kolom: sidebar kiri (4) + konten utama (8)
 *
 * @package Temadesa
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$desa_page = get_queried_object();
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
		<div class="row">

			<!-- Sidebar Kiri (hanya jika ada widget) -->
			<?php if (is_active_sidebar('left-sidebar')) : ?>
				<aside class="col-lg-4 col-12 desa-page-sidebar order-2 order-lg-1" id="desa-sidebar">
					<?php dynamic_sidebar('left-sidebar'); ?>
				</aside>
			<?php endif; ?>

			<!-- Konten Utama -->
			<main class="site-main col-lg-<?php echo is_active_sidebar('left-sidebar') ? '8' : '12'; ?> col-12 order-1" id="main">
				<?php
				while (have_posts()) {
					the_post();
				?>
					<article <?php post_class('desa-page-content'); ?> id="post-<?php the_ID(); ?>">
						<div class="entry-content">
							<?php the_content(); ?>
						</div>

						<?php
						wp_link_pages(array(
							'before' => '<div class="page-links">' . __('Pages:', 'temadesa'),
							'after'  => '</div>',
						));
						?>

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

		</div><!-- .row -->
	</div><!-- #content -->
</div><!-- #page-wrapper -->

<?php
get_footer();
