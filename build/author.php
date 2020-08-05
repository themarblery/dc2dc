<?php
/**
 * Category template
 *
 * @package dc2dc
 * @since   1.0.0
 */

/**
 * Change the page title.
 *
 * @param array $args Array of title args.
 * @return array
 */
function dc2dc_category_title( $args ) {
	$args['title'] = sprintf( __( 'Author: %s', 'dc2dc' ), get_the_author_meta( 'display_name' ) ); // phpcs:ignore
	return $args;
}
add_filter( 'go_page_title_args', 'dc2dc_category_title' );

/** Calls the default index template. */
locate_template( 'home.php', true );
