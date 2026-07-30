<?php

/**
 * Temadesa child theme functions and definitions
 *
 * @package Temadesa
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Enqueue parent and child theme styles.
 * Parent styles are already enqueued by wsbase_scripts().
 * Kita enqueue child style dengan dependency ke parent style.
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
 * Load custom functions for wp-desa integration.
 * Override template parts di sini jika diperlukan.
 */
// require_once get_stylesheet_directory() . '/inc/wp-desa-functions.php';
