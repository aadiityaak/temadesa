<?php
/**
 * Tema Desa Options — unified theme settings page.
 * UI styled like WP-Desa admin panel.
 *
 * @package Temadesa
 */

defined('ABSPATH') || exit;

/**
 * 1. Register admin menu.
 */
add_action('admin_menu', 'temadesa_options_add_page');

/**
 * 2. Register settings.
 */
add_action('admin_init', 'temadesa_options_register_settings');

/**
 * 3. Enqueue assets on this page only.
 */
add_action('admin_enqueue_scripts', 'temadesa_options_admin_scripts');

/**
 * 4. Admin body class for our page.
 */
add_filter('admin_body_class', 'temadesa_options_body_class');

/**
 * Add top-level menu page.
 */
function temadesa_options_add_page()
{
	add_menu_page(
		__('Tema Desa Options', 'temadesa'),
		__('Tema Desa', 'temadesa'),
		'manage_options',
		'temadesa-options',
		'temadesa_options_render_page',
		'dashicons-building',
		60
	);
}

/**
 * Add body class for admin styling.
 */
function temadesa_options_body_class($classes)
{
	$screen = get_current_screen();
	if ($screen && $screen->id === 'toplevel_page_temadesa-options') {
		$classes .= ' temadesa-options-page';
	}
	return $classes;
}

/**
 * Register settings group + sanitizer.
 */
function temadesa_options_register_settings()
{
	register_setting(
		'temadesa_options_group',
		'temadesa_carousel_slides',
		'temadesa_options_sanitize_carousel'
	);
}

/**
 * Sanitize carousel slides array.
 */
function temadesa_options_sanitize_carousel($input)
{
	if (!is_array($input)) {
		return [];
	}

	$sanitized = [];
	foreach ($input as $key => $slide) {
		$i             = absint($key);
		$sanitized[$i] = [
			'image'     => esc_url_raw($slide['image'] ?? ''),
			'heading'   => sanitize_text_field($slide['heading'] ?? ''),
			'text'      => sanitize_textarea_field($slide['text'] ?? ''),
			'btn1_text' => sanitize_text_field($slide['btn1_text'] ?? ''),
			'btn1_url'  => esc_url_raw($slide['btn1_url'] ?? ''),
			'btn2_text' => sanitize_text_field($slide['btn2_text'] ?? ''),
			'btn2_url'  => esc_url_raw($slide['btn2_url'] ?? ''),
		];
	}

	return $sanitized;
}

/**
 * Enqueue media uploader + WP-Desa admin styles.
 */
function temadesa_options_admin_scripts($hook)
{
	if (strpos($hook, 'temadesa-options') === false) {
		return;
	}

	// Enqueue WP-Desa admin CSS if plugin is active.
	if (wp_style_is('wp-desa-admin-css', 'registered')) {
		wp_enqueue_style('wp-desa-admin-css');
	}

	// Our admin overrides.
	wp_add_inline_style('wp-desa-admin-css', '
		.temadesa-slide-box {
			background: var(--cloud, #f0f0f1);
			padding: var(--sp-xl, 20px);
			margin-bottom: var(--sp-xl, 20px);
			border-radius: var(--rounded-lg, 8px);
			border: 1px solid var(--fog, #dcdcde);
		}
		.temadesa-slide-box h2 {
			font-family: var(--font-display, inherit);
			font-size: 18px;
			font-weight: 500;
			margin: 0 0 var(--sp-md, 16px) 0;
		}
		.temadesa-field-row {
			margin-bottom: var(--sp-sm, 12px);
		}
		.temadesa-img-preview img {
			max-width: 200px;
			max-height: 100px;
			border-radius: 6px;
		}
		body.temadesa-options-page #wpcontent {
			padding-left: 0;
		}
	');

	wp_enqueue_media();

	// Media uploader JS.
	wp_add_inline_script('media-upload', '
	jQuery(function($){
		$(".temadesa-upload-btn").on("click",function(e){
			e.preventDefault();
			var btn=$(this), container=btn.closest(".temadesa-slide-box");
			var imgInput=container.find(".temadesa-img-input"), preview=container.find(".temadesa-img-preview");
			var frame=wp.media({title:btn.data("title")||"Pilih Gambar",button:{text:"Pilih"},multiple:false});
			frame.on("select",function(){
				var att=frame.state().get("selection").first().toJSON();
				imgInput.val(att.url);
				preview.html("<img src=\""+att.url+"\">");
			});
			frame.open();
		});
		$(".temadesa-remove-img").on("click",function(){
			var c=$(this).closest(".temadesa-slide-box");
			c.find(".temadesa-img-input").val("");
			c.find(".temadesa-img-preview").html("<span class=\"dashicons dashicons-format-image\" style=\"color:#c2c2c2;font-size:32px;width:32px;height:32px;\"></span>");
		});
	});');
}

/**
 * Render the admin page.
 */
function temadesa_options_render_page()
{
	$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'carousel';
	?>
	<div class="wrap wp-desa-wrapper">
		<!-- Header -->
		<div class="wp-desa-header" style="padding-top:var(--sp-xl);">
			<h1 class="wp-desa-title">Tema Desa</h1>
		</div>

		<!-- Subnav tabs (pill-style) -->
		<div class="wp-desa__subnav" style="margin:0 -20px 0;">
			<div class="wp-desa__subnav-title">Pengaturan</div>
			<nav class="wp-desa__subnav-tabs">
				<a href="?page=temadesa-options&amp;tab=carousel"
				   class="wp-desa__subnav-tab <?php echo $active_tab === 'carousel' ? 'is-active' : ''; ?>">
					Carousel
				</a>
				<!-- Future tabs here -->
			</nav>
		</div>

		<form method="post" action="options.php" style="margin-top:var(--sp-xxl);">
			<?php settings_fields('temadesa_options_group'); ?>

			<div style="display:flex;gap:var(--sp-lg);align-items:flex-start;">

				<!-- Main card -->
				<div class="wp-desa-card" style="flex:1;max-width:800px;">
					<?php
					if ($active_tab === 'carousel') {
						temadesa_options_render_carousel_tab();
					}
					?>
				</div>

				<!-- Sidebar: Save -->
				<div class="wp-desa-card" style="width:260px;flex-shrink:0;position:sticky;top:100px;">
					<div style="padding:var(--sp-md);">
						<h3 style="margin:0 0 var(--sp-xs);font-size:14px;font-weight:600;">Publikasikan</h3>
						<p style="color:var(--graphite);font-size:12px;margin:0 0 var(--sp-md);">
							Simpan perubahan pengaturan tema desa.
						</p>
						<?php submit_button('Simpan Pengaturan', 'wp-desa-btn wp-desa-btn-primary', 'submit', false, [
							'style' => 'width:100%;',
						]); ?>
					</div>
				</div>

			</div>
		</form>
	</div>
	<?php
}

/**
 * Render Carousel tab.
 */
function temadesa_options_render_carousel_tab()
{
	$slides  = get_option('temadesa_carousel_slides', []);
	$default = [
		'image'     => '',
		'heading'   => '',
		'text'      => '',
		'btn1_text' => '',
		'btn1_url'  => '',
		'btn2_text' => '',
		'btn2_url'  => '',
	];

	for ($i = 1; $i <= 3; $i++) {
		if (!isset($slides[$i])) {
			$slides[$i] = $default;
		}
	}
	?>

	<div class="wp-desa-tab-content">
		<p class="wp-desa-helper" style="margin-bottom:var(--sp-xl);">
			Atur slide hero di halaman depan. Kosongkan heading / text untuk memakai data default desa.
		</p>

		<?php for ($i = 1; $i <= 3; $i++) :
			$s = $slides[$i];
		?>
		<div class="temadesa-slide-box">
			<h2>Slide <?php echo $i; ?></h2>

			<!-- Image -->
			<div class="temadesa-field-row">
				<label class="wp-desa-label">Background Image</label>
				<div class="wp-desa-image-preview temadesa-img-preview" style="width:180px;height:100px;">
					<?php if ($s['image']) : ?>
						<img src="<?php echo esc_url($s['image']); ?>">
					<?php else : ?>
						<span class="dashicons dashicons-format-image" style="color:#c2c2c2;font-size:32px;width:32px;height:32px;"></span>
					<?php endif; ?>
				</div>
				<input type="hidden" name="temadesa_carousel_slides[<?php echo $i; ?>][image]"
					   class="temadesa-img-input" value="<?php echo esc_attr($s['image']); ?>">
				<div class="wp-desa-flex-gap-8" style="margin-top:var(--sp-xs);">
					<button type="button" class="wp-desa-btn wp-desa-btn-secondary temadesa-upload-btn"
							data-title="Slide <?php echo $i; ?> Background">
						<span class="dashicons dashicons-upload"></span> Pilih Gambar
					</button>
					<button type="button" class="wp-desa-btn wp-desa-btn-danger temadesa-remove-img <?php echo empty($s['image']) ? 'wp-desa-hidden' : ''; ?>">
						Hapus
					</button>
				</div>
			</div>

			<!-- Heading -->
			<div class="temadesa-field-row">
				<label class="wp-desa-label" for="heading_<?php echo $i; ?>">Heading</label>
				<input type="text" class="wp-desa-input" id="heading_<?php echo $i; ?>"
					   name="temadesa_carousel_slides[<?php echo $i; ?>][heading]"
					   value="<?php echo esc_attr($s['heading']); ?>"
					   placeholder="Kosongi untuk nama desa default">
			</div>

			<!-- Text -->
			<div class="temadesa-field-row">
				<label class="wp-desa-label" for="text_<?php echo $i; ?>">Text / Description</label>
				<textarea class="wp-desa-textarea" id="text_<?php echo $i; ?>" rows="3"
						  name="temadesa_carousel_slides[<?php echo $i; ?>][text]"
						  placeholder="Deskripsi slide"><?php echo esc_textarea($s['text']); ?></textarea>
			</div>

			<!-- Buttons -->
			<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--sp-md);">
				<div>
					<label class="wp-desa-label">Button 1 — Text</label>
					<input type="text" class="wp-desa-input"
						   name="temadesa_carousel_slides[<?php echo $i; ?>][btn1_text]"
						   value="<?php echo esc_attr($s['btn1_text']); ?>"
						   placeholder="cth: Profil Desa">
				</div>
				<div>
					<label class="wp-desa-label">Button 1 — URL</label>
					<input type="text" class="wp-desa-input"
						   name="temadesa_carousel_slides[<?php echo $i; ?>][btn1_url]"
						   value="<?php echo esc_attr($s['btn1_url']); ?>"
						   placeholder="cth: #profil">
				</div>
				<div>
					<label class="wp-desa-label">Button 2 — Text</label>
					<input type="text" class="wp-desa-input"
						   name="temadesa_carousel_slides[<?php echo $i; ?>][btn2_text]"
						   value="<?php echo esc_attr($s['btn2_text']); ?>"
						   placeholder="cth: Layanan">
				</div>
				<div>
					<label class="wp-desa-label">Button 2 — URL</label>
					<input type="text" class="wp-desa-input"
						   name="temadesa_carousel_slides[<?php echo $i; ?>][btn2_url]"
						   value="<?php echo esc_attr($s['btn2_url']); ?>"
						   placeholder="cth: #layanan">
				</div>
			</div>
		</div>
		<?php endfor; ?>
	</div>
	<?php
}
