<?php

/**
 * Temadesa child theme functions and definitions
 *
 * @package Temadesa
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Load unified Tema Desa Options (admin page).
require_once get_stylesheet_directory() . '/inc/theme-options.php';

/**
 * Enqueue parent and child theme styles.
 * Parent styles are already enqueued by wsbase_scripts().
 */
add_action('wp_enqueue_scripts', 'temadesa_enqueue_styles', 20);
function temadesa_enqueue_styles()
{
	wp_enqueue_style(
		'temadesa-style',
		get_stylesheet_uri(),
		array('wsbase-styles'),
		wp_get_theme()->get('Version')
	);
}

/**
 * Enqueue custom desa styles.
 */
add_action('wp_enqueue_scripts', 'temadesa_enqueue_desa_styles', 30);
function temadesa_enqueue_desa_styles()
{
	$css_path = get_stylesheet_directory() . '/css/desa.css';
	$css_url  = get_stylesheet_directory_uri() . '/css/desa.css';
	$version  = file_exists($css_path) ? filemtime($css_path) : wp_get_theme()->get('Version');

	wp_enqueue_style(
		'temadesa-desa-style',
		$css_url,
		array('temadesa-style'),
		$version
	);
}

/**
 * Theme setup.
 */
add_action('after_setup_theme', 'temadesa_setup');
function temadesa_setup()
{
	// Register desa-top menu location.
	register_nav_menus(array(
		'desa-top' => __('Desa Top Bar', 'temadesa'),
	));
}

/**
 * Add desa-specific body classes.
 */
add_filter('body_class', 'temadesa_body_classes');
function temadesa_body_classes($classes)
{
	$classes[] = 'theme-temadesa';
	if (is_page()) {
		$classes[] = 'page-desa';
	}
	return $classes;
}

/**
 * Override site info for desa copyright.
 * Hooks into wsbase_site_info action (plugggable via removal).
 */
add_action('init', function () {
	if (has_action('wsbase_site_info', 'wsbase_add_site_info')) {
		remove_action('wsbase_site_info', 'wsbase_add_site_info');
	}
});
add_action('wsbase_site_info', 'temadesa_site_info');
function temadesa_site_info()
{
	$year       = date('Y');
	$site_title = get_bloginfo('name');
	printf(
		'<div class="text-center">Copyright %s &copy; %s. All rights reserved.</div>',
		esc_html($year),
		esc_html($site_title)
	);
}
