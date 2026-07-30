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
 * Sanitize carousel slides array — re-index sequentially.
 */
function temadesa_options_sanitize_carousel($input)
{
	if (!is_array($input)) {
		return [];
	}

	$sanitized = [];
	$idx       = 1;
	foreach ($input as $slide) {
		if (!is_array($slide)) {
			continue;
		}
		$sanitized[$idx] = [
			'image'     => esc_url_raw($slide['image'] ?? ''),
			'heading'   => sanitize_text_field($slide['heading'] ?? ''),
			'text'      => sanitize_textarea_field($slide['text'] ?? ''),
			'btn1_text' => sanitize_text_field($slide['btn1_text'] ?? ''),
			'btn1_url'  => esc_url_raw($slide['btn1_url'] ?? ''),
			'btn2_text' => sanitize_text_field($slide['btn2_text'] ?? ''),
			'btn2_url'  => esc_url_raw($slide['btn2_url'] ?? ''),
		];
		$idx++;
	}

	return $sanitized;
}

/**
 * Enqueue media uploader + admin styles (WP-Desa compatible).
 */
function temadesa_options_admin_scripts($hook)
{
	if (strpos($hook, 'temadesa-options') === false) {
		return;
	}

	// --- Enqueue WP-Desa admin CSS directly ---
	$wp_desa_css = WP_PLUGIN_DIR . '/wp-desa/assets/css/admin/style.css';
	if (file_exists($wp_desa_css)) {
		wp_enqueue_style(
			'temadesa-wpdesa-admin',
			plugins_url('assets/css/admin/style.css', WP_PLUGIN_DIR . '/wp-desa/wp-desa.php'),
			[],
			filemtime($wp_desa_css)
		);
	}

	// Register + enqueue our own handle for inline overrides.
	wp_register_style('temadesa-options-base', false);
	wp_enqueue_style('temadesa-options-base');
	wp_add_inline_style('temadesa-options-base', '
		body.temadesa-options-page #wpcontent { padding-left: 0; }
		.wp-desa-wrapper { margin-top: 0; }
		.temadesa-slide-box {
			background: #f0f0f1;
			padding: 20px; margin-bottom: 20px;
			border-radius: 8px; border: 1px solid #dcdcde;
		}
		.temadesa-slide-box h2 {
			font-size: 18px; font-weight: 500;
			margin: 0 0 16px 0;
		}
		.temadesa-field-row { margin-bottom: 12px; }
		.temadesa-img-preview img { max-width:200px; max-height:100px; border-radius:6px; }
	');

	wp_enqueue_media();

	// Media uploader JS — register dummy handle + inline.
	wp_register_script('temadesa-options-media', false, ['jquery'], false, true);
	wp_enqueue_script('temadesa-options-media');
	wp_add_inline_script('temadesa-options-media', '
	jQuery(function($){
		$("#temadesa-slides-container")
			.on("click",".temadesa-upload-btn",function(e){
				e.preventDefault();
				var btn=$(this), box=btn.closest(".temadesa-slide-box");
				var input=box.find(".temadesa-img-input"), preview=box.find(".temadesa-img-preview");
				var frame=wp.media({title:btn.data("title")||"Pilih Gambar",button:{text:"Pilih"},multiple:false});
				frame.on("select",function(){
					var att=frame.state().get("selection").first().toJSON();
					input.val(att.url);
					preview.html("<img src=\""+att.url+"\">");
					box.find(".temadesa-remove-img").removeClass("wp-desa-hidden");
				});
				frame.open();
			})
			.on("click",".temadesa-remove-img",function(){
				var box=$(this).closest(".temadesa-slide-box");
				box.find(".temadesa-img-input").val("");
				box.find(".temadesa-img-preview").html("<span class=\"dashicons dashicons-format-image\" style=\"color:#c2c2c2;font-size:32px;width:32px;height:32px;\"></span>");
				$(this).addClass("wp-desa-hidden");
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
 * Render Carousel tab — dynamic slides with add/remove.
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

	// Default to 1 slide if empty.
	if (empty($slides)) {
		$slides = [1 => $default];
	}

	$slide_count = count($slides);
	?>

	<div class="wp-desa-tab-content">
		<p class="wp-desa-helper" style="margin-bottom:var(--sp-xl);">
			Atur slide hero di halaman depan. Slide pertama otomatis jadi slide utama (logo + nama desa).
			Kosongkan heading / text untuk memakai data default desa.
		</p>

		<div id="temadesa-slides-container">
			<?php foreach ($slides as $i => $s) :
				temadesa_render_slide_fields($i, $s);
			endforeach; ?>
		</div>

		<button type="button" class="wp-desa-btn wp-desa-btn-secondary" id="temadesa-add-slide" style="margin-top:var(--sp-sm);">
			<span class="dashicons dashicons-plus"></span> Tambah Slide
		</button>
	</div>

	<!-- Template for JS -->
	<template id="temadesa-slide-tpl"><?php
		temadesa_render_slide_fields('__i__', $default, true);
	?></template>

	<script>
	jQuery(function($){
		var nextIdx = <?php echo $slide_count + 1; ?>;
		var tpl = document.getElementById('temadesa-slide-tpl');

		$('#temadesa-add-slide').on('click', function(){
			var html = tpl.innerHTML.replace(/__i__/g, nextIdx);
			var $el = $(html);
			$('#temadesa-slides-container').append($el);
			nextIdx++;
		});

		$('#temadesa-slides-container').on('click', '.temadesa-remove-slide', function(){
			if ($('#temadesa-slides-container > .temadesa-slide-box').length <= 1) {
				alert('Minimal harus ada 1 slide.');
				return;
			}
			$(this).closest('.temadesa-slide-box').remove();
		});
	});
	</script>
	<?php
}

/**
 * Render fields for one slide (used by PHP loop + JS template).
 */
function temadesa_render_slide_fields($i, $s, $is_template = false)
{
	$hidden_class = empty($s['image']) ? 'wp-desa-hidden' : '';
	?>
	<div class="temadesa-slide-box">
		<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
			<h2 style="margin:0;">Slide <span class="temadesa-slide-num"><?php echo esc_html($i); ?></span></h2>
			<button type="button" class="wp-desa-btn wp-desa-btn-danger temadesa-remove-slide">
				<span class="dashicons dashicons-trash" style="font-size:16px;width:16px;height:16px;"></span> Hapus
			</button>
		</div>

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
			<input type="hidden" class="temadesa-img-input"
				   name="temadesa_carousel_slides[<?php echo esc_attr($i); ?>][image]"
				   value="<?php echo esc_attr($s['image']); ?>">
			<div class="wp-desa-flex-gap-8" style="margin-top:var(--sp-xs);">
				<button type="button" class="wp-desa-btn wp-desa-btn-secondary temadesa-upload-btn"
						data-title="Slide <?php echo esc_attr($i); ?> Background">
					<span class="dashicons dashicons-upload"></span> Pilih Gambar
				</button>
				<button type="button" class="wp-desa-btn wp-desa-btn-danger temadesa-remove-img <?php echo $hidden_class; ?>">
					Hapus
				</button>
			</div>
		</div>

		<!-- Heading -->
		<div class="temadesa-field-row">
			<label class="wp-desa-label" for="heading_<?php echo esc_attr($i); ?>">Heading</label>
			<input type="text" class="wp-desa-input" id="heading_<?php echo esc_attr($i); ?>"
				   name="temadesa_carousel_slides[<?php echo esc_attr($i); ?>][heading]"
				   value="<?php echo esc_attr($s['heading']); ?>"
				   placeholder="Kosongi untuk nama desa default">
		</div>

		<!-- Text -->
		<div class="temadesa-field-row">
			<label class="wp-desa-label" for="text_<?php echo esc_attr($i); ?>">Text / Description</label>
			<textarea class="wp-desa-textarea" id="text_<?php echo esc_attr($i); ?>" rows="3"
					  name="temadesa_carousel_slides[<?php echo esc_attr($i); ?>][text]"
					  placeholder="Deskripsi slide"><?php echo esc_textarea($s['text'] ?? ''); ?></textarea>
		</div>

		<!-- Buttons -->
		<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--sp-md);">
			<div>
				<label class="wp-desa-label">Button 1 — Text</label>
				<input type="text" class="wp-desa-input"
					   name="temadesa_carousel_slides[<?php echo esc_attr($i); ?>][btn1_text]"
					   value="<?php echo esc_attr($s['btn1_text'] ?? ''); ?>"
					   placeholder="cth: Profil Desa">
			</div>
			<div>
				<label class="wp-desa-label">Button 1 — URL</label>
				<input type="text" class="wp-desa-input"
					   name="temadesa_carousel_slides[<?php echo esc_attr($i); ?>][btn1_url]"
					   value="<?php echo esc_attr($s['btn1_url'] ?? ''); ?>"
					   placeholder="cth: #profil">
			</div>
			<div>
				<label class="wp-desa-label">Button 2 — Text</label>
				<input type="text" class="wp-desa-input"
					   name="temadesa_carousel_slides[<?php echo esc_attr($i); ?>][btn2_text]"
					   value="<?php echo esc_attr($s['btn2_text'] ?? ''); ?>"
					   placeholder="cth: Layanan">
			</div>
			<div>
				<label class="wp-desa-label">Button 2 — URL</label>
				<input type="text" class="wp-desa-input"
					   name="temadesa_carousel_slides[<?php echo esc_attr($i); ?>][btn2_url]"
					   value="<?php echo esc_attr($s['btn2_url'] ?? ''); ?>"
					   placeholder="cth: #layanan">
			</div>
		</div>
	</div>
	<?php
}
