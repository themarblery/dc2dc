<?php
/**
 * File: archive.php (for archives and blog landing).
 *
 * @package dc2dc
 */

/**
 * Change the page title.
 *
 * @param array $args Array of title args.
 * @return array
 */
function dc2dc_go_page_title_args( $args ) {
	$args['custom'] = false;
	return $args;
}
add_filter( 'go_page_title_args', 'dc2dc_go_page_title_args' );

/**
 * Add Blog to the body class of all pages.
 *
 * @param array $classes The array of body classes.
 * @return array
 */
function dc2dc_blog_body_class( $classes ) {
	// If not the home page add the "blog" class to the body tag.
	if ( ! in_array( 'blog', $classes, true ) ) {
		$classes[] = 'blog';
	}

	return $classes;
}
add_filter( 'body_class', 'dc2dc_blog_body_class' );

get_header();

Go\page_title();

if ( have_posts() ) {

	// Start the Loop.
	while ( have_posts() ) :
		the_post();
		get_template_part( 'partials/content', 'post' );
	endwhile;

	// Previous/next page navigation.
	get_template_part( 'partials/pagination' );

} else {

	// If no content, include the "No posts found" template.
	get_template_part( 'partials/content', 'none' );
}

get_footer();
