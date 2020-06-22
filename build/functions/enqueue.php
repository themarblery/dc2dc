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
	wp_enqueue_style( 'dc2dc-google-fonts-css', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&family=Open+Sans&display=swap', array(), '1.0.0' );
	wp_enqueue_style( 'dc2dc-css', get_stylesheet_directory_uri() . '/css/main.css', array( 'go-style', 'dc2dc-google-fonts-css' ), '1.0.0' );
}
add_action( 'enqueue_block_assets', 'dc2dc_enqueue_styles' );
