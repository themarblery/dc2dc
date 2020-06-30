<?php
/**
 * Child Page List
 *
 * @package dc2dc
 */

$parent_page = get_field( 'parent_page' );
if ( $parent_page ) :
	$args = array(
		'post_type'      => 'page',
		'post_parent'    => $parent_page,
		'posts_per_page' => 99,
	);

	if ( get_the_ID() !== $parent_page ) {
		$args['post__not_in'] = get_the_ID();
	}

	$child_pages = get_posts( $args );
	if ( $child_pages ) :
		?>
		<div class="dc2dc-child-page-links__inner alignwide">
			<?php
			$block_title = get_field( 'title' );
			if ( $block_title ) :
				?>
				<h2><?php echo wp_kses_post( $block_title ); ?></h2>
			<?php endif; ?>
			<ul>
				<?php foreach ( $child_pages as $child_page ) : ?>
					<li>
						<div class="wp-block-button">
							<a class="wp-block-button__link" <?php dc2dc_href_attrs( get_permalink( $child_page ) ); ?>><?php echo wp_kses_post( get_the_title( $child_page ) ); ?></a>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	endif;
endif;
