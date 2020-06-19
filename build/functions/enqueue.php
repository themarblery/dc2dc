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
add_action( 'wp_enqueue_scripts', 'dc2dc_enqueue_styles' );
