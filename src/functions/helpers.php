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
		echo wp_kses( $img_html, apply_filters( 'em_image_allowed_html', $allowed_html ) );
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
