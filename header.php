<?php
/**
 * The header for our theme - Desa override
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Temadesa
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php do_action('wp_body_open'); ?>
<div class="site" id="page">

	<!-- Desa Top Bar -->
	<?php if (has_nav_menu('desa-top')) : ?>
		<div class="desa-top-bar d-none d-md-block">
			<div class="container d-flex justify-content-between align-items-center py-1 small">
				<div class="desa-top-bar-left">
					<span class="text-white">
						<?php echo esc_html(get_bloginfo('description') ?: 'Website Desa ' . get_bloginfo('name')); ?>
					</span>
				</div>
				<div class="desa-top-bar-right">
					<?php
					wp_nav_menu(array(
						'theme_location' => 'desa-top',
						'container'      => false,
						'menu_class'     => 'desa-top-menu list-inline mb-0',
						'depth'          => 1,
						'fallback_cb'    => false,
					));
					?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<!-- Desa Navbar -->
	<nav id="main-nav" class="navbar navbar-expand-md navbar-dark desa-navbar sticky-top" aria-labelledby="main-nav-label">
		<h2 id="main-nav-label" class="screen-reader-text">
			<?php esc_html_e('Main Navigation', 'temadesa'); ?>
		</h2>

		<div class="container">
			<!-- Brand -->
			<div class="navbar-brand d-flex align-items-center">
				<?php if (has_custom_logo()) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a class="desa-site-title" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
						<?php bloginfo('name'); ?>
					</a>
				<?php endif; ?>
			</div>

			<!-- Toggler -->
			<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#desaOffcanvasNav" aria-controls="desaOffcanvasNav" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'temadesa'); ?>">
				<span class="navbar-toggler-icon"></span>
			</button>

			<!-- Desktop Menu -->
			<div class="collapse navbar-collapse d-none d-md-flex" id="desaDesktopNav">
				<?php
				wp_nav_menu(array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'navbar-nav ms-auto desa-nav',
					'fallback_cb'    => false,
					'depth'          => 3,
				));
				?>
			</div>

			<!-- Mobile Offcanvas -->
			<div class="offcanvas offcanvas-end d-md-none" tabindex="-1" id="desaOffcanvasNav">
				<div class="offcanvas-header">
					<h5 class="offcanvas-title"><?php bloginfo('name'); ?></h5>
					<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
				</div>
				<div class="offcanvas-body">
					<?php
					wp_nav_menu(array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'navbar-nav ms-auto desa-nav-mobile',
						'fallback_cb'    => false,
						'depth'          => 3,
					));
					?>
				</div>
			</div>
		</div>
	</nav>
