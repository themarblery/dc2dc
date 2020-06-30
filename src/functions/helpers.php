<?php
/**
 * Helper functions.
 *
 * @package dc2dc
 * @since   1.0.0
 */

/**
 * Get image html with srcset and sizes attributes. If the image is an SVG,
 * then the SVG code will be returned instead.
 *
 * @param int   $img_id The attachment id for the image.
 * @param array $args Array of function options.
 * @return string Image HTML.
 */
function dc2dc_image( $img_id, $args = array() ) {
	/**
	 * Merge args with defaults.
	 */
	$args = wp_parse_args(
		$args,
		array(
			// An array of media queries for the image's 'sizes' attribute.
			'breakpoints'  => array(),
			// Image size. Accepts any valid image size, or an array of width and height
			// values in pixels (in that order). Default value: 'medium'.
			'image_size'   => 'medium',
			// Value for the img tags "class" attribute.
			'class'        => '',
			// Whether to wrap the image in a container using the images height and width
			// to create an aspect ratio. Good for lazy loading images and having the
			// container take up the correct amount of space while the image loads.
			'aspect_ratio' => false,
			// Whether to inline svg images or not.
			'svg_inline'   => true,
		)
	);

	/**
	 * Get the image url using the image id and size.
	 */
	$img_src = wp_get_attachment_image_url( $img_id, $args['image_size'] );

	/**
	 * Bail early, if we don't have a src.
	 */
	if ( ! $img_src ) {
		return;
	}

	/**
	 * If the image is an SVG, simply include it and be done. Otherwise, build out
	 * the image markup and output throught wp_kses.
	 */
	$image_mime_type = get_post_mime_type( $img_id );
	$is_svg          = 'image/svg+xml' === $image_mime_type || 'image/svg' === $image_mime_type;
	if ( $args['svg_inline'] && $is_svg ) {
		include get_attached_file( $img_id );
	} else {
		$meta      = wp_get_attachment_metadata( $img_id );
		$alt_text  = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
		$img_attrs = array(
			'src' => $img_src,
			'alt' => $alt_text ? $alt_text : '',
		);

		// Add "srcset" attribute if this is not an SVG.
		if ( ! $is_svg ) {
			$img_attrs['srcset'] = wp_get_attachment_image_srcset( $img_id, $args['image_size'] );
		}

		// Update the width / height values if $meta data is available.
		if ( $meta ) {
			$img_attrs['width']  = ! empty( $meta['sizes'][ $args['image_size'] ] ) ? $meta['sizes'][ $args['image_size'] ]['width'] : $meta['width'];
			$img_attrs['height'] = ! empty( $meta['sizes'][ $args['image_size'] ] ) ? $meta['sizes'][ $args['image_size'] ]['height'] : $meta['height'];
		}

		/**
		 * Add 'sizes' attribute if breakpoints are provided.
		 */
		if ( ! empty( $args['breakpoints'] ) ) {
			$img_attrs['sizes'] = implode( ', ', $args['breakpoints'] );
		}

		/**
		 * Add 'sizes' attribute if breakpoints are provided.
		 */
		if ( ! empty( $args['class'] ) ) {
			$img_attrs['class'] = $args['class'];
		}

		/**
		 * Build img html with all attributes.
		 */
		$img_html = '<img';
		foreach ( $img_attrs as $attr => $value ) {
			$img_html .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
		}
		$img_html .= ' />';

		/**
		 * Wrap the image/svg in an aspect ratio container if these conditions are met.
		 * This is helpful when lazy-loading images, and you want the container take up
		 * the same space the image will once loaded. Avoids jumping when images are loading.
		 */
		if ( $args['aspect_ratio'] && ! empty( $img_attrs['width'] ) && ! empty( $img_attrs['height'] ) ) {
			$ratio    = ( $img_attrs['height'] / $img_attrs['width'] ) * 100;
			$img_html = "<div class='em-image' style='max-width:{$img_attrs['width']}px;'>
				<div class='em-image__ratio' style='padding-bottom:{$ratio}%;'>{$img_html}</div>
			</div>";
		}

		$allowed_html = array(
			'div' => array(
				'class' => array(),
				'style' => array(),
			),
			'img' => array(
				'alt'         => array(),
				'class'       => array(),
				'src'         => array(),
				'srcset'      => array(),
				'sizes'       => array(),
				'height'      => array(),
				'width'       => array(),
				'data-src'    => array(),
				'data-srcset' => array(),
				'data-sizes'  => array(),
			),
		);

		/**
		 * Safely output image markup using wp_kses.
		 */
		echo wp_kses( $img_html, apply_filters( 'dc2dc_image_allowed_html', $allowed_html ) );
	}
}

/**
 * Uses the $wp_filesystem to safely get the contents of a file.
 *
 * @link   https://github.com/markjaquith/feedback/issues/33#issuecomment-50975788
 *
 * @param  string $path The server path to a local file.
 * @return string $contents The contents collected from the local file using output buffers.
 */
function dc2dc_file_get_contents( $path = '' ) {

	global $wp_filesystem;

	if ( ! $path ) {
		return;
	}

	if ( file_exists( $path ) ) {
		/**
		 * Initialize the WP filesystem.
		 */
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		/**
		 * Use WP filesystem to get the svg's contents.
		 */
		return $wp_filesystem->get_contents( $path );
	}
}

/**
 * Check if passed link is external.
 *
 * Intended to determine if a link should have a link `target` attribute.
 * Used by `dc2dc_link_target( $url )` function.
 *
 * @param    string $url   Valid URL.
 * @return   boolean       True for external. False for internal.
 */
function dc2dc_is_external_url( $url ) {
	$link_url = wp_parse_url( $url );
	$home_url = wp_parse_url( home_url() );

	// Fixes bug if you pass in hash links.
	if ( empty( $link_url['host'] ) ) {
		return false;
	}

	if ( ! empty( $link_url['path'] ) && substr( $link_url['path'], -4 ) === '.pdf' ) {
		return true;
	}

	/**
	 * Remove "www." from beginning of url hosts to normalize results.
	 *
	 * EX: If $link_url['host'] is example.com and $home_url['host']
	 * is www.example.com, they will be treated as not equal, and
	 * this function will return true, when it should return false.
	 */
	$link_host = preg_replace( '/^www./', '', $link_url['host'] );
	$home_host = preg_replace( '/^www./', '', $home_url['host'] );

	return $link_host !== $home_host;
}

/**
 * Determines the link `target` attribute safely.
 *
 * Adds the `rel="noopener noreferrer"` attribute in addition to the
 * link `target` attribute if the supplied URL is not on the same host
 * as this WordPress install.
 *
 * Helpful for avoiding the `target="_blank"` phishing hack.
 *
 * @link     https://www.jitbit.com/alexblog/256-targetblank---the-most-underestimated-vulnerability-ever/
 * @see      dc2dc_is_external_url( $url )
 *
 * @param    string  $url               Valid URL.
 * @param    boolean $force_new_window  Whether to force the link to open in a new window.
 * @return   string                     HTML target attributes if true. Empty string if false.
 */
function dc2dc_get_link_target_attr( $url, $force_new_window = false ) {
	return dc2dc_is_external_url( $url ) || $force_new_window ? 'target="_blank" rel="noopener noreferrer"' : '';
}

/**
 * Get href and possible target and rel attributes for use on an anchore element.
 *
 * @param string  $url Valid URL.
 * @param boolean $force_new_window Whether to force the link to open in a new window.
 * @param boolean $echo Whether to ouput or return the attributes.
 * @return string/void
 */
function dc2dc_href_attrs( $url, $force_new_window = false, $echo = true ) {
	$target = dc2dc_get_link_target_attr( $url, $force_new_window );
	$attrs  = 'href="' . $url . '"' . ( $target ? ' ' . $target : '' );
	if ( $echo ) {
		echo wp_kses_post( $attrs );
	} else {
		return $attrs;
	}
}

/**
 * Given an array of classes, output or return the classes
 * as a single string with a space between each class.
 *
 * @param array   $classes Array of classes.
 * @param boolean $echo Whether to echo or return.
 * @return void|string
 */
function dc2dc_classes( $classes = array(), $echo = true ) {
	// Convert $classes to array if it isn't already.
	if ( is_string( $classes ) ) {
		$classes = explode( ' ', $classes );
	}

	// Echo or return the class string.
	if ( $echo ) {
		echo esc_attr( implode( ' ', $classes ) );
	} else {
		return implode( ' ', $classes );
	}
}
