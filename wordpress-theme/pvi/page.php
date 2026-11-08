<?php
/**
 * Any other page added from wp-admin > Pages.
 *
 * @package pvi
 */

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<div class="page-title"><h1><?php the_title(); ?></h1></div>
	<main class="container" style="max-width:850px">
		<section class="entry-content"><?php the_content(); ?></section>
	</main>
	<?php
endwhile;
get_footer();
