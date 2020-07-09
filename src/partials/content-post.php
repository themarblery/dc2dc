<?php
/**
 * Template part for displaying post archives and search results
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Go
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<div class="post__inner alignwide">
		<div class="post__thumbnail">
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail(); ?>
				<?php endif; ?>
			</a>
		</div>

		<div class="post__body">
			<header class="post__header entry-header m-auto px">
				<?php
				if ( is_sticky() && is_home() && ! is_paged() ) {
					printf( '<span class="sticky-post">%s</span>', esc_html_x( 'Featured', 'post', 'go' ) );
				}

				if ( is_singular() ) :
					the_title( '<h1 class="post__title entry-title m-0">', '</h1>' );
				else :
					the_title( sprintf( '<h2 class="post__title entry-title m-0"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
				endif;

				Go\post_meta( get_the_ID(), 'top' );
				?>
			</header>

			<div class="post__content <?php Go\content_wrapper_class( 'content-area__wrapper' ); ?>">
				<div class="content-area entry-content">
					<?php the_excerpt(); ?>
				</div>
			</div>
		</div>
	</div>
</article>
