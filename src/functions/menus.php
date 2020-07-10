<?php
/**
 * Menus
 *
 * @package dc2dc
 * @since   1.0.0
 */

/**
 * Register nav menus.
 *
 * @return void
 */
register_nav_menus(
	array(
		'secondary' => esc_html__( 'Secondary', 'dc2dc' ),
	)
);
