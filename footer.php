<?php
/**
 * The template for displaying the footer - Desa override
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Temadesa
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Load identitas desa dari plugin WP-Desa.
$wsdesa       = get_option('wp_desa_settings', []);
$desa_nama    = !empty($wsdesa['nama_desa']) ? $wsdesa['nama_desa'] : get_bloginfo('name');
$desa_logo    = !empty($wsdesa['logo_kabupaten']) ? $wsdesa['logo_kabupaten'] : '';
$desa_alamat  = !empty($wsdesa['alamat_kantor']) ? $wsdesa['alamat_kantor'] : '';
$desa_email   = !empty($wsdesa['email_desa']) ? $wsdesa['email_desa'] : '';
$desa_telepon = !empty($wsdesa['telepon_desa']) ? $wsdesa['telepon_desa'] : '';
?>

	<!-- Footer Main -->
	<footer class="desa-footer">
		<div class="desa-footer-main py-5">
			<div class="container">
				<div class="row g-4">

					<!-- Column 1: Profil Desa -->
					<div class="col-lg-4 col-md-6">
						<div class="desa-footer-brand">
							<?php if ($desa_logo) : ?>
								<div class="footer-logo mb-3">
									<img class="desa-footer-logo-img"
										 src="<?php echo esc_url($desa_logo); ?>"
										 alt="<?php echo esc_attr($desa_nama); ?>">
								</div>
							<?php elseif (has_custom_logo()) : ?>
								<div class="footer-logo mb-3">
									<?php the_custom_logo(); ?>
								</div>
							<?php else : ?>
								<h4 class="desa-footer-title mb-3">
									<a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
										<?php echo esc_html($desa_nama); ?>
									</a>
								</h4>
							<?php endif; ?>
							<p class="desa-footer-desc small">
								<?php
								echo esc_html(
									!empty($wsdesa['nama_kecamatan']) && !empty($wsdesa['nama_kabupaten'])
									? "Kec. {$wsdesa['nama_kecamatan']}, {$wsdesa['nama_kabupaten']}"
									: (get_bloginfo('description') ?: "Website resmi $desa_nama")
								);
								?>
							</p>
						</div>
					</div>

					<!-- Column 2: Menu Cepat -->
					<div class="col-lg-4 col-md-6">
						<h5 class="desa-footer-heading mb-3">Menu Cepat</h5>
						<?php
						if (has_nav_menu('footer')) :
							wp_nav_menu(array(
								'theme_location' => 'footer',
								'container'      => false,
								'menu_class'     => 'desa-footer-menu list-unstyled',
								'depth'          => 1,
								'fallback_cb'    => false,
							));
						else :
							wp_nav_menu(array(
								'theme_location' => 'primary',
								'container'      => false,
								'menu_class'     => 'desa-footer-menu list-unstyled',
								'depth'          => 1,
								'fallback_cb'    => false,
							));
						endif;
						?>
					</div>

					<!-- Column 3: Kontak Desa -->
					<div class="col-lg-4 col-md-12">
						<h5 class="desa-footer-heading mb-3">Kontak Desa</h5>
						<ul class="desa-footer-contact list-unstyled small">
							<li class="mb-2 d-flex align-items-start gap-2">
								<span class="desa-footer-icon">📍</span>
								<span><?php echo esc_html($desa_alamat ?: get_theme_mod('wsbase_desa_address', 'Alamat Kantor Desa')); ?></span>
							</li>
							<li class="mb-2 d-flex align-items-start gap-2">
								<span class="desa-footer-icon">📞</span>
								<span><?php echo esc_html($desa_telepon ?: get_theme_mod('wsbase_desa_phone', '(021) 1234-5678')); ?></span>
							</li>
							<li class="mb-2 d-flex align-items-start gap-2">
								<span class="desa-footer-icon">✉️</span>
								<span><?php echo esc_html($desa_email ?: get_theme_mod('wsbase_desa_email', get_bloginfo('admin_email'))); ?></span>
							</li>
						</ul>

						<!-- Social Media -->
						<div class="desa-footer-social mt-3">
							<?php
							$socials = array(
								'facebook'  => get_theme_mod('wsbase_facebook', ''),
								'twitter'   => get_theme_mod('wsbase_twitter', ''),
								'instagram' => get_theme_mod('wsbase_instagram', ''),
								'youtube'   => get_theme_mod('wsbase_youtube', ''),
							);
							foreach ($socials as $platform => $url) :
								if (!empty($url)) :
							?>
									<a href="<?php echo esc_url($url); ?>"
									   class="desa-social-link"
									   target="_blank"
									   rel="noopener noreferrer"
									   aria-label="<?php echo esc_attr(ucfirst($platform)); ?>">
										<i class="fab fa-<?php echo esc_attr($platform); ?>"></i>
									</a>
							<?php
								endif;
							endforeach;
							?>
						</div>
					</div>

				</div>
			</div>
		</div>

		<!-- Footer Bottom -->
		<div class="desa-footer-bottom py-3">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-md-6 text-center text-md-start">
						<div class="desa-copyright small">
							<?php wsbase_site_info(); ?>
						</div>
					</div>
					<div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
						<?php
						if (has_nav_menu('footer-bottom')) :
							wp_nav_menu(array(
								'theme_location' => 'footer-bottom',
								'container'      => false,
								'menu_class'     => 'desa-footer-bottom-menu list-inline small mb-0',
								'depth'          => 1,
								'fallback_cb'    => false,
							));
						endif;
						?>
					</div>
				</div>
			</div>
		</div>
	</footer>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
