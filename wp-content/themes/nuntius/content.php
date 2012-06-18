<?php
/**
 * @package Nuntius
 * @since Nuntius 1.0
 */
?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-utility">
		<?php if ( comments_open() || ( '0' != get_comments_number() && ! comments_open() ) ) : ?>
			<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'nuntius' ), __( '1 Comment', 'nuntius' ), __( '% Comments', 'nuntius' ) ); ?></span>
		<?php endif; ?>
		<?php edit_post_link( __( 'Edit', 'nuntius' ), '<span class="edit-link">', '</span>' ); ?>

	</div><!-- .entry-utility -->

	<h2 class="entry-title">
		<?php if ( ! is_single() ) : ?>
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'nuntius' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
		<?php else : ?>
			<?php the_title(); ?>
		<?php endif; ?>
	</h2><!-- .entry-title -->

	<div class="byline">
		<?php nuntius_single_post_meta(); ?>
	</div><!-- .byline -->

	<div class="entry-content">
		<?php the_content( __( 'Continue Reading' , 'nuntius' ) . ' &raquo;'); ?>
		<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'nuntius' ), 'after' => '</p>' ) ); ?>
	</div><!-- .entry-content -->

	<div class="entry-meta">
		<?php nuntius_tag_list(); ?>
	</div><!-- .entry-meta -->
</div><!-- #post-<?php the_ID(); ?> -->