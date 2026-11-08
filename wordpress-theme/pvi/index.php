<?php
/**
 * Fallback template (blog posts, archives, search).
 *
 * @package pvi
 */

get_header();
?>
<div class="page-title"><h1><?php echo is_singular() ? esc_html( get_the_title() ) : esc_html( wp_strip_all_tags( get_the_archive_title() ? get_the_archive_title() : get_bloginfo( 'name' ) ) ); ?></h1></div>
<main class="container" style="max-width:850px">
	<section>
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article class="card" style="margin-bottom:20px">
					<?php if ( ! is_singular() ) : ?>
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<span class="date" style="color:var(--muted);font-size:13px"><?php echo esc_html( get_the_date() ); ?></span>
					<?php endif; ?>
					<div class="entry-content"><?php is_singular() ? the_content() : the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p>Kuch nahi mila.</p>
		<?php endif; ?>
	</section>
</main>
<?php
get_footer();
