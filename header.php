<?php
/**
 * The header for our theme — Desa override
 *
 * @package Temadesa
 */

defined('ABSPATH') || exit;

$wsdesa       = get_option('wp_desa_settings', []);
$desa_nama    = !empty($wsdesa['nama_desa']) ? $wsdesa['nama_desa'] : get_bloginfo('name');
$desa_logo    = !empty($wsdesa['logo_kabupaten']) ? $wsdesa['logo_kabupaten'] : '';
$desa_kec     = !empty($wsdesa['nama_kecamatan']) ? $wsdesa['nama_kecamatan'] : '';
$desa_kab     = !empty($wsdesa['nama_kabupaten']) ? $wsdesa['nama_kabupaten'] : '';

// WhatsApp number from plugin settings (telepon_desa) — 0 → 62.
$desa_wa = !empty($wsdesa['telepon_desa'])
	? preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $wsdesa['telepon_desa']))
	: '';
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

	<!-- Top Bar — kec/kab info -->
	<div class="desa-top-bar">
		<div class="container d-flex justify-content-between align-items-center">
			<span class="desa-top-bar-left">
				<?php
				if ($desa_kec && $desa_kab) {
					echo esc_html("Kec. $desa_kec, $desa_kab");
				} else {
					echo esc_html(get_bloginfo('description') ?: "Website $desa_nama");
				}
				?>
			</span>
			<?php if (has_nav_menu('desa-top')) : ?>
				<?php
				wp_nav_menu(array(
					'theme_location' => 'desa-top',
					'container'      => false,
					'menu_class'     => 'desa-top-menu',
					'depth'          => 1,
					'fallback_cb'    => false,
				));
				?>
			<?php endif; ?>
		</div>
	</div>

	<!-- Navbar — design system: white canvas, ink text, 64px -->
	<nav id="main-nav" class="navbar navbar-expand-md navbar-light desa-navbar sticky-top"
		 aria-labelledby="main-nav-label">
		<h2 id="main-nav-label" class="screen-reader-text">
			<?php esc_html_e('Main Navigation', 'temadesa'); ?>
		</h2>

		<div class="container">
			<!-- Brand -->
			<a class="desa-brand" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
				<?php if ($desa_logo) : ?>
					<img class="desa-brand-logo"
						 src="<?php echo esc_url($desa_logo); ?>"
						 alt="<?php echo esc_attr($desa_nama); ?>"
						 width="32" height="32">
				<?php elseif (has_custom_logo()) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<span class="desa-brand-logo desa-brand-logo--fallback">🏘️</span>
				<?php endif; ?>
				<span class="desa-brand-name"><?php echo esc_html($desa_nama); ?></span>
			</a>

			<!-- Desktop Nav -->
			<div class="desa-nav-desktop" id="desaDesktopNav">
				<?php
				$nav_args = array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'desa-nav',
					'fallback_cb'    => false,
					'depth'          => 3,
					'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s<li class="nav-item dropdown desa-more-menu d-none"><a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . esc_html__('Lainnya', 'temadesa') . '</a><ul class="dropdown-menu dropdown-menu-end"></ul></li></ul>',
				);
				if (class_exists('wsbase_WP_Bootstrap_Navwalker')) {
					$nav_args['walker'] = new wsbase_WP_Bootstrap_Navwalker();
				}
				wp_nav_menu($nav_args);
				?>

				<div class="desa-nav-actions">
					<?php if ($desa_wa) : ?>
						<a href="https://wa.me/<?php echo esc_attr($desa_wa); ?>" target="_blank" rel="noopener"
						   class="desa-btn desa-btn-primary"><?php esc_html_e('WhatsApp', 'temadesa'); ?></a>
					<?php endif; ?>
				</div>
			</div>

			<!-- Toggler (mobile) -->
			<button class="desa-nav-toggle" type="button" data-bs-toggle="offcanvas"
					data-bs-target="#desaOffcanvasNav" aria-controls="desaOffcanvasNav"
					aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'temadesa'); ?>">
				<span class="desa-nav-toggle-bar"></span>
				<span class="desa-nav-toggle-bar"></span>
				<span class="desa-nav-toggle-bar"></span>
			</button>
		</div>
	</nav>

	<!-- Mobile Offcanvas -->
	<div class="offcanvas offcanvas-end" tabindex="-1" id="desaOffcanvasNav"
		 aria-labelledby="desaOffcanvasLabel">
		<div class="offcanvas-header">
			<h5 class="offcanvas-title" id="desaOffcanvasLabel"><?php echo esc_html($desa_nama); ?></h5>
			<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		</div>
		<div class="offcanvas-body">
			<?php
			wp_nav_menu(array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'desa-nav-mobile',
				'fallback_cb'    => false,
				'depth'          => 3,
			));
			?>
			<?php if ($desa_wa) : ?>
				<div class="mt-4">
					<a href="https://wa.me/<?php echo esc_attr($desa_wa); ?>" target="_blank" rel="noopener"
					   class="desa-btn desa-btn-primary d-block text-center"><?php esc_html_e('WhatsApp', 'temadesa'); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>
