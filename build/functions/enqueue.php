<?php
/**
 * Enqueues theme specific JavaScript and CSS files
 *
 * @package dc2dc
 * @since   1.0.0
 */

/**
 * Enqueues CSS files.
 *
 * @since 1.0.0
 *
 * @link  https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/
 * @link  https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 */
function dc2dc_enqueue_styles() {
	wp_enqueue_style( 'dc2dc-css', get_stylesheet_directory_uri() . '/css/main.css', array( 'go-style' ), '1.0.0' );
}
add_action( 'enqueue_block_assets', 'dc2dc_enqueue_styles' );

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
