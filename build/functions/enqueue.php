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
	wp_enqueue_style( 'dc2dc-css', get_stylesheet_directory_uri() . '/css/main.css', array( 'go-style' ), DC2DC_THEME_VERSION );
}
add_action( 'enqueue_block_assets', 'dc2dc_enqueue_block_assets' );

/**
 * Enqueues block editor assets.
 */
function dc2dc_enqueue_block_editor_assets() {
	wp_enqueue_script( 'dc2dc-editor-js', get_stylesheet_directory_uri() . '/js/editor.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-edit-post', 'wp-data', 'wp-editor' ), DC2DC_THEME_VERSION, true );
}
add_action( 'enqueue_block_editor_assets', 'dc2dc_enqueue_block_editor_assets' );

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
