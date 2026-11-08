<?php
/**
 * Prime Vocational Institute theme.
 *
 * Courses, notifications, results and admission applications are stored in
 * the WordPress database (custom post types) and managed from wp-admin.
 *
 * @package pvi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PVI_VERSION', '1.0.0' );
define( 'PVI_DIR', get_template_directory() );
define( 'PVI_URI', get_template_directory_uri() );

require PVI_DIR . '/inc/helpers.php';
require PVI_DIR . '/inc/post-types.php';
require PVI_DIR . '/inc/settings.php';
require PVI_DIR . '/inc/meta-boxes.php';
require PVI_DIR . '/inc/admin-lists.php';
require PVI_DIR . '/inc/applications.php';
require PVI_DIR . '/inc/setup.php';

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		register_nav_menus( array( 'primary' => __( 'Main menu', 'pvi' ) ) );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'pvi', get_stylesheet_uri(), array(), PVI_VERSION );
		wp_enqueue_script( 'pvi', PVI_URI . '/assets/js/site.js', array(), PVI_VERSION, true );
	}
);

// Use the institute logo as favicon until a Site Icon is set in Appearance > Customize.
add_action(
	'wp_head',
	function () {
		if ( ! has_site_icon() ) {
			echo '<link rel="icon" href="' . esc_url( pvi_image_url( 'logo.png' ) ) . '">' . "\n";
		}
	}
);
