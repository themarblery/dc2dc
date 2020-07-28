<?php
/**
 * Enqueues theme specific JavaScript and CSS files
 *
 * @package dc2dc
 * @since   1.0.0
 */

/**
 * Enqueues front-end assets.
 */
function dc2dc_enqueue_block_assets() {
	$deps = is_admin() ? array() : array( 'go-style' );
	wp_enqueue_style( 'dc2dc-css', get_stylesheet_directory_uri() . '/css/main.css', $deps, DC2DC_THEME_VERSION );
}
add_action( 'enqueue_block_assets', 'dc2dc_enqueue_block_assets', 9999 );

/**
 * Enqueues block editor assets.
 */
function dc2dc_enqueue_block_editor_assets() {
	wp_enqueue_script( 'dc2dc-editor-js', get_stylesheet_directory_uri() . '/js/editor.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-edit-post', 'wp-data', 'wp-editor' ), DC2DC_THEME_VERSION, true );
}
add_action( 'enqueue_block_editor_assets', 'dc2dc_enqueue_block_editor_assets', 9999 );

/**
 * Update font family for modern design.
 *
 * @param array $styles Array of Go theme styles.
 * @return array
 */
function dc2dc_go_design_styles( $styles ) {
	$styles['modern']['fonts'] = array(
		'Roboto' => array(
			'300',
			'400',
			'700',
			'900',
		),
	);
	return $styles;
}
add_filter( 'go_design_styles', 'dc2dc_go_design_styles' );

/**
 * Enqueue scripts for the admin.
 *
 * @return void
 */
function dc2dc_admin_enqueue_scripts() {
	wp_enqueue_script( 'jquery-mask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js', array( 'jquery' ), '1.14.16', true );
}
add_action( 'admin_enqueue_scripts', 'dc2dc_admin_enqueue_scripts' );

/**
 * Output scripts in the <head>.
 *
 * @return void
 */
function dc2dc_header_scripts() {
	global $post;
	$scripts = get_field( 'header_scripts', $post->ID );
	if ( $scripts ) {
		echo $scripts; // phpcs:ignore
	}
}
add_action( 'wp_head', 'dc2dc_header_scripts' );

/**
 * Output scripts in the <head>.
 *
 * @return void
 */
function dc2dc_footer_scripts() {
	$scripts = get_field( 'footer_scripts' );
	if ( $scripts ) {
		echo $scripts; // phpcs:ignore
	}
}
add_action( 'wp_footer', 'dc2dc_footer_scripts' );
