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
?>

<div class="wrapper desa-page-wrapper" id="page-wrapper">
	<div class="container" id="content" tabindex="-1">
		<div class="row">

			<!-- Sidebar Kiri (Profil, Statistik, Widget) -->
			<aside class="col-lg-4 col-12 desa-page-sidebar order-2 order-lg-1" id="desa-sidebar">
				<?php if (is_active_sidebar('left-sidebar')) : ?>
					<?php dynamic_sidebar('left-sidebar'); ?>
				<?php else : ?>
					<div class="desa-sidebar-default">
						<div class="card border-0 mb-4">
							<div class="card-body">
								<h5 class="card-title"><?php bloginfo('name'); ?></h5>
								<p class="card-text small text-muted">
									<?php echo esc_html(get_bloginfo('description')); ?>
								</p>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</aside>

			<!-- Konten Utama -->
			<main class="site-main col-lg-8 col-12 order-1 order-lg-2" id="main">
				<?php
				while (have_posts()) {
					the_post();
					?>
					<article <?php post_class('desa-page-content'); ?> id="post-<?php the_ID(); ?>">
						<?php if (!is_front_page()) : ?>
							<header class="entry-header mb-4">
								<?php the_title('<h1 class="entry-title desa-page-title">', '</h1>'); ?>
							</header>
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
