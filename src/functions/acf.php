<?php
/**
 * Set of functions related to Advanced Custom Fields
 *
 * @package dc2dc
 * @since   1.0.0
 */

if ( function_exists( 'acf_register_block' ) ) {
	/**
	 * Register Custom Block Category
	 *
	 * @since 1.0.0
	 * @param array   $categories Array of current block categories.
	 * @param WP_Post $post The current post object.
	 * @return array
	 */
	function dc2dc_block_categories( $categories, $post ) {
		$categories[] = array(
			'slug'  => 'dc2dc-blocks',
			'title' => __( 'DC2DC Blocks', 'dc2dc' ),
		);
		return $categories;
	}
	add_filter( 'block_categories', 'dc2dc_block_categories', 10, 2 );

	/**
	 * ACF callback function that loads the proper partial based on the block type.
	 *
	 * @since 1.0.0
	 * @param string $block The name of the block.
	 * @return void
	 */
	function dc2dc_acf_block_render_callback( $block ) {
		// Convert name ("acf/testimonial") into path friendly slug ("testimonial").
		$slug = str_replace( 'acf/', '', $block['name'] );

		// Add the default block class.
		$block_classes = array( "dc2dc-{$slug}" );

		if ( ! empty( $block['data']['classes'] ) ) {
			$block_classes = array_merge( $block_classes, $block['data']['classes'] );
		}

		// Add the block's alignment class, if available.
		if ( ! empty( $block['supports']['align'] ) ) {
			// Fall back to the first alignment option set in the supported alignments if none are found.
			$align = empty( $block['align'] ) ? $block['supports']['align'][0] : $block['align'];
			// Add the alignment class to the array.
			$block_classes[] = 'align' . $align;
		}

		// Include a template part from within the "template-parts/block" folder.
		if ( file_exists( get_theme_file_path( "/partials/acf-blocks/{$slug}.php" ) ) ) :
			?>
			<div id="<?php echo esc_attr( $block['id'] ); ?>" class="<?php dc2dc_classes( $block_classes ); ?>">
				<?php include get_theme_file_path( "/partials/acf-blocks/{$slug}.php" ); ?>
			</div>
			<?php
		endif;
	}

	/**
	 * Register ACF Blocks
	 *
	 * @since 1.0.0
	 * @return void
	 */
	function dc2dc_acf_register_blocks() {
		$blocks = array(
			array(
				'name'        => 'child-page-links',
				'title'       => __( 'Child Page Links', 'dc2dc' ),
				'description' => __( 'Show child page links for the selected page in a list.', 'dc2dc' ),
				'icon'        => 'admin-links',
				'keywords'    => array( 'menu', 'page', 'list', 'link' ),
				'align'       => 'full',
				'supports'    => array(
					'align' => array( 'full' ),
				),
			),
			array(
				'name'        => 'snippets',
				'title'       => __( 'Snippets', 'dc2dc' ),
				'description' => __( 'Grid of items with title, description and link.', 'dc2dc' ),
				'icon'        => 'editor-alignleft',
				'keywords'    => array( 'feature' ),
				'align'       => 'wide',
				'supports'    => array(
					'align' => array( 'wide' ),
				),
			),
		);

		foreach ( $blocks as $args ) {
			$args['category']        = 'dc2dc-blocks';
			$args['render_callback'] = 'dc2dc_acf_block_render_callback';
			acf_register_block_type( $args );
		}
	}
	add_action( 'acf/init', 'dc2dc_acf_register_blocks' );
}

/**
 * Adds ACF capabilities for options page.
 *
 * @since   1.0.0
 */
if ( class_exists( 'acf' ) ) {

	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page();
	}

	/**
	 * Save acf-json to the stylesheet directory.
	 *
	 * @since 1.0.0
	 * @link https://support.advancedcustomfields.com/forums/topic/acf-json-fields-not-loading-from-parent-theme/
	 * @return string
	 */
	function dc2dc_acf_save_json_path() {
		return get_stylesheet_directory() . '/acf-json';
	}
	add_filter( 'acf/settings/save_json', 'dc2dc_acf_save_json_path' );

	/**
	 * Load acf-json from parent theme and child theme, if available.
	 *
	 * @since 1.0.0
	 * @link https://support.advancedcustomfields.com/forums/topic/acf-json-fields-not-loading-from-parent-theme/
	 * @param array $paths Array of acf-json paths.
	 * @return array
	 */
	function dc2dc_acf_load_json_paths( $paths ) {
		$paths = array( get_template_directory() . '/acf-json' );

		if ( is_child_theme() ) {
			$paths[] = get_stylesheet_directory() . '/acf-json';
		}

		return $paths;
	}
	add_filter( 'acf/settings/load_json', 'dc2dc_acf_load_json_paths' );
}

/*
 * The following functions prevents Fatal Errors
 * when ACF is inactive.
 *
 * Intended for pages such as user activation notices, where
 * only network active plugins are loaded. If ACF is not loaded,
 * return false on any built-in ACF function calls to prevent
 * Fatal errors.
 */
if ( ! is_admin() && ! class_exists( 'acf' ) ) {
	/**
	 * ACF fallback function: get_field
	 *
	 * @since  1.0.0
	 *
	 * @param string $key  Fieldname.
	 * @param int    $post Post ID.
	 * @return false
	 */
	function get_field( $key = null, $post = null ) {
		return false;
	}

	/**
	 * ACF fallback function: get_sub_field
	 *
	 * @since  1.0.0
	 *
	 * @param string $key  Fieldname.
	 * @param int    $post Post ID.
	 * @return false
	 */
	function get_sub_field( $key = null, $post = null ) {
		return false;
	}

	/**
	 * ACF fallback function: the_field
	 *
	 * @since  1.0.0
	 *
	 * @param string $key  Fieldname.
	 * @param int    $post Post ID.
	 * @return false
	 */
	function the_field( $key = null, $post = null ) {
		return false;
	}

	/**
	 * ACF fallback function: have_rows
	 *
	 * @since  1.0.0
	 *
	 * @param string $key  Fieldname.
	 * @param int    $post Post ID.
	 * @return false
	 */
	function have_rows( $key = null, $post = null ) {
		return false;
	}

	/**
	 * ACF fallback function: acf_add_local_field_group
	 *
	 * @since  1.0.0
	 *
	 * @param string $key  Fieldname.
	 * @param int    $post Post ID.
	 * @return false
	 */
	function acf_add_local_field_group( $key = null, $post = null ) {
		return false;
	}
}
