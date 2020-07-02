<?php
/**
 * Child Page List
 *
 * @package dc2dc
 */

$data = get_fields();
if ( ! empty( $data['parent_page'] ) ) :
	$args = array(
		'post_type'      => 'page',
		'post_parent'    => $data['parent_page'],
		'posts_per_page' => 99,
	);

	if ( get_the_ID() !== $data['parent_page'] ) {
		$args['post__not_in'] = array( get_the_ID() );
	}

	$child_pages = get_posts( $args );

	if ( $child_pages ) :
		$parent_page = false;
		if ( ! empty( $data['include_parent_page'] ) ) {
			$parent_page = get_post( $data['parent_page'] );
			if ( $parent_page ) {
				array_unshift( $child_pages, $parent_page );
			}
		}
		?>
		<div class="dc2dc-child-page-links__inner alignwide">
			<?php if ( ! empty( $data['title'] ) ) : ?>
				<h2><?php echo wp_kses_post( $data['title'] ); ?></h2>
			<?php endif; ?>
			<ul>
				<?php foreach ( $child_pages as $child_page ) : ?>
					<li>
						<div class="wp-block-button">
							<a class="wp-block-button__link" <?php dc2dc_href_attrs( get_permalink( $child_page ) ); ?>>
								<?php echo wp_kses_post( $child_page === $parent_page ? __( 'Overview', 'dc2dc' ) : get_the_title( $child_page ) ); ?>
							</a>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	endif;
endif;
