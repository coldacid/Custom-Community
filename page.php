<?php get_header(); ?>

	<div id="content" class="span8">
		<div class="padder">

		<?php do_action( 'bp_before_blog_page' ) ?>

		<div class="page" id="blog-page">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            
                <?php get_posts_titles(get_the_title(), get_the_ID());?>
            
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="entry">
						<?php if ( function_exists( 'sharing_display' ) ) remove_filter( 'the_content', 'sharing_display', 19 ); ?>
						<?php the_content( __( '<p class="serif">Read the rest of this page &rarr;</p>', 'cc' ) ); ?>
						<div class="clear"></div>
						<?php wp_link_pages( array( 'before' => __( '<p class="cc_pagecount"><strong>Pages:</strong> ', 'cc' ), 'after' => '</p>', 'next_or_number' => 'number')); ?>

					</div>
					<div class="clear"></div>
				</div>

			<?php endwhile; endif; ?>

		</div><!-- .page -->
		<?php cc_list_posts_on_page(); ?> 
		<?php if ( function_exists( 'sharing_display' ) ) echo sharing_display(); ?>
		<div class="clear"></div>
		
		<?php do_action( 'bp_after_blog_page' ) ?>
		
		<?php edit_post_link( __( 'Edit this page.', 'cc' ), '<p class="edit-link">', '</p>'); ?>
		
		<!-- instead of comment_form() we use comments_template(). If you want to fall back to wp, change this function call ;-) -->
		<?php comments_template(); ?>
		
		</div><!-- .padder -->
	</div><!-- #content -->

<?php get_footer(); ?>
