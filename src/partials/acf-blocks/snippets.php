<?php
/**
 * Snippets
 *
 * @package dc2dc
 */

if ( have_rows( 'snippets' ) ) :
	?>
	<div class="dc2dc-snippets__inner">
		<div class="snippets">
			<?php
			while ( have_rows( 'snippets' ) ) :
				the_row();
				$snippet_title = get_sub_field( 'title' );
				$snippet_desc  = get_sub_field( 'description' );
				$snippet_link  = get_sub_field( 'link' );
				?>
				<div class="snippet">
					<header class="snippet__header">
						<h3 class="snippet__title">
							<?php if ( $snippet_link ) : ?>
								<a <?php dc2dc_href_attrs( $snippet_link['url'] ); ?>>
							<?php endif; ?>
							<?php echo wp_kses_post( $snippet_title ); ?>
							<?php if ( $snippet_link ) : ?>
								</a>
							<?php endif; ?>
						</h3>
					</header>
					<div class="snippet__description"><?php echo wp_kses_post( $snippet_desc ); ?></div>
					<?php if ( $snippet_link ) : ?>
						<a class="snippet__link" <?php dc2dc_href_attrs( $snippet_link['url'] ); ?>><?php echo wp_kses_post( $snippet_link['title'] ); ?></a>
					<?php endif; ?>
				</div>
				<?php
			endwhile;
			?>
		</div>
	</div>
	<?php
endif;
