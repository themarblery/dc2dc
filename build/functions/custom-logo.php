<?php
/**
 * Custom Logo
 *
 * @package dc2dc
 * @since   1.0.0
 */

/**
 * Inline svg logos using dc2dc_image.
 *
 * @param string $html The current logo html.
 * @return string
 */
function dc2dc_get_custom_logo( $html ) {
	// Get the custom logo attachment id.
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		// Begin building new HTML markup.
		$html = '<a href="' . home_url() . '" class="custom-logo-link" rel="home">';
		// Begin output buffering for dc2dc_image function.
		ob_start();
		dc2dc_image( $custom_logo_id );
		$html .= ob_get_clean();
		$html .= '</a>';
	}
	return $html;
}
add_filter( 'get_custom_logo', 'dc2dc_get_custom_logo' );
