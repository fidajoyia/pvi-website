<?php
/**
 * Page not found.
 *
 * @package pvi
 */

get_header();
?>
<div class="page-title"><h1>Page nahi mila</h1><p>Yeh page mojood nahi hai.</p></div>
<main class="container" style="text-align:center">
	<section>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">Home Page</a>
		<a href="<?php echo esc_url( pvi_page_url( 'courses' ) ); ?>" class="btn btn-accent">Courses Dekhein</a>
	</section>
</main>
<?php
get_footer();
