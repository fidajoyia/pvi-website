<?php
/**
 * Template Name: PVI – Contact
 *
 * @package pvi
 */

get_header();
$pvi_q = rawurlencode( pvi_opt( 'map_query' ) );
?>
<div class="page-title">
	<h1>Contact &amp; Location</h1>
	<p>Hum se rabta karein ya college tashreef layein</p>
</div>

<main class="container">
	<section class="two-col">
		<div class="card">
			<h3>College ka Pata</h3>
			<p style="margin:10px 0">📍 <?php echo esc_html( pvi_opt( 'address' ) ); ?></p>
			<p>📞 <?php echo pvi_phone_links(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?></p>
			<?php if ( pvi_opt( 'email' ) ) : ?>
				<p>✉️ <a href="mailto:<?php echo esc_attr( pvi_opt( 'email' ) ); ?>"><?php echo esc_html( pvi_opt( 'email' ) ); ?></a></p>
			<?php endif; ?>
			<?php if ( pvi_opt( 'timing' ) ) : ?>
				<p>🕗 <?php echo esc_html( pvi_opt( 'timing' ) ); ?></p>
			<?php endif; ?>
			<?php
			while ( have_posts() ) :
				the_post();
				if ( '' !== trim( get_the_content() ) ) :
					?>
					<div class="entry-content" style="margin-top:12px"><?php the_content(); ?></div>
					<?php
				endif;
			endwhile;
			?>
			<a href="<?php echo esc_url( 'https://www.google.com/maps/search/?api=1&query=' . $pvi_q ); ?>" target="_blank" rel="noopener" class="btn btn-primary" style="margin-top:16px">📍 Google Maps par Raasta Dekhein</a>
		</div>
		<iframe class="map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="College location"
			src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . $pvi_q . '&z=15&output=embed' ); ?>"></iframe>
	</section>
</main>
<?php
get_footer();
