<?php
/**
 * Home page.
 *
 * @package pvi
 */

get_header();

$pvi_notices = pvi_notices( 10 );
$pvi_tagline = pvi_opt( 'tagline' );
$pvi_comma   = strpos( $pvi_tagline, ',' );
?>
<?php if ( $pvi_notices ) : ?>
	<div class="ticker"><span>
		<?php
		echo implode(
			'&nbsp;&nbsp;&nbsp;&nbsp;•&nbsp;&nbsp;&nbsp;&nbsp;',
			array_map(
				function ( $n ) {
					return '📢 ' . esc_html( get_the_title( $n ) );
				},
				$pvi_notices
			)
		); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped per item.
		?>
	</span></div>
<?php endif; ?>

<div class="hero">
	<img src="<?php echo esc_url( pvi_image_url( 'logo.png' ) ); ?>" alt="<?php echo esc_attr( pvi_opt( 'name' ) ); ?> logo" class="hero-logo">
	<h1><?php echo esc_html( pvi_opt( 'name' ) . ', ' . pvi_opt( 'city' ) ); ?></h1>
	<?php if ( false !== $pvi_comma ) : ?>
		<p style="font-size:24px;margin-bottom:4px"><?php echo esc_html( substr( $pvi_tagline, 0, $pvi_comma + 1 ) ); ?> <strong class="accent"><?php echo esc_html( strtoupper( trim( substr( $pvi_tagline, $pvi_comma + 1 ) ) ) ); ?>.</strong></p>
	<?php else : ?>
		<p style="font-size:24px;margin-bottom:4px"><strong class="accent"><?php echo esc_html( $pvi_tagline ); ?></strong></p>
	<?php endif; ?>
	<p><?php echo esc_html( pvi_opt( 'sub_tagline' ) . ' · ' . pvi_opt( 'admission_badge' ) ); ?></p>
	<a href="<?php echo esc_url( pvi_page_url( 'apply' ) ); ?>" class="btn btn-accent">Admission Open — Apply Now</a>
	<a href="<?php echo esc_url( pvi_page_url( 'courses' ) ); ?>" class="btn btn-outline">Courses Dekhein</a>
	<br><span class="affiliation"><?php echo esc_html( pvi_opt( 'affiliation' ) ); ?></span>
</div>

<main class="container">
	<?php
	while ( have_posts() ) :
		the_post();
		if ( '' !== trim( get_the_content() ) ) :
			?>
			<section><div class="entry-content"><?php the_content(); ?></div></section>
			<?php
		endif;
	endwhile;
	?>

	<section>
		<div class="stats">
			<?php foreach ( pvi_pairs( 'stats' ) as $pvi_stat ) : ?>
				<div class="card"><strong><?php echo esc_html( $pvi_stat[0] ); ?></strong><span><?php echo esc_html( $pvi_stat[1] ); ?></span></div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="two-col">
		<div>
			<h2 class="section-title" style="text-align:left">College ke baare mein</h2>
			<p><?php echo esc_html( pvi_opt( 'about' ) ); ?></p>
		</div>
		<div class="card">
			<h3>Principal ka Paigham</h3>
			<p><?php echo esc_html( pvi_opt( 'principal_message' ) ); ?></p>
		</div>
	</section>

	<section>
		<div class="navttc-banner">
			<img src="<?php echo esc_url( pvi_image_url( 'navttc-partner.jpg' ) ); ?>" alt="NAVTTC Partner Institute — <?php echo esc_attr( pvi_opt( 'name' ) ); ?>"
				data-lightbox="<?php echo esc_url( pvi_image_url( 'navttc-partner.jpg' ) ); ?>" data-title="NAVTTC Partner Institute">
			<div>
				<span class="course-code" style="background:#e3f3ea;color:#0d5c36">Govt. of Pakistan</span>
				<h2>NAVTTC Partner Institute</h2>
				<p><?php echo esc_html( pvi_opt( 'name' ) ); ?>, National Vocational &amp; Technical Training Commission (NAVTTC) ka partner institute hai. Yahan Hukumat-e-Pakistan ki janib se NAVTTC aur Prime Minister's Youth Programme ke tehat naujawanon ko hunarmand banane ke liye <strong>muft courses</strong> karwaye jate hain.</p>
				<ul class="course-meta">
					<li>👥 <?php echo esc_html( pvi_opt( 'navttc_eligibility' ) ); ?></li>
					<li>📄 Saath layein: <?php echo esc_html( pvi_opt( 'navttc_documents' ) ); ?></li>
					<?php if ( pvi_opt( 'navttc_apply_url' ) ) : ?>
						<li>🌐 Online apply: <a href="<?php echo esc_url( pvi_opt( 'navttc_apply_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( preg_replace( '#^https?://#', '', pvi_opt( 'navttc_apply_url' ) ) ); ?></a></li>
					<?php endif; ?>
				</ul>
				<a href="<?php echo esc_url( pvi_page_url( 'courses' ) . '#navttc' ); ?>" class="btn btn-primary">Free Courses Dekhein</a>
			</div>
		</div>
	</section>

	<?php $pvi_posters = pvi_pairs( 'posters' ); ?>
	<?php if ( $pvi_posters ) : ?>
		<section>
			<h2 class="section-title">Hamare Posters</h2>
			<div class="gallery">
				<?php foreach ( $pvi_posters as $pvi_p ) : ?>
					<figure data-lightbox="<?php echo esc_url( pvi_image_url( $pvi_p[0] ) ); ?>" data-title="<?php echo esc_attr( $pvi_p[1] ); ?>">
						<img src="<?php echo esc_url( pvi_image_url( $pvi_p[0] ) ); ?>" alt="<?php echo esc_attr( $pvi_p[1] ); ?>" loading="lazy">
						<figcaption><?php echo esc_html( $pvi_p[1] ); ?></figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<section>
		<h2 class="section-title">Hamari Facilities</h2>
		<div class="grid">
			<?php foreach ( pvi_lines( 'facilities' ) as $pvi_f ) : ?>
				<div class="card facility"><?php echo esc_html( $pvi_f ); ?></div>
			<?php endforeach; ?>
		</div>
	</section>

	<section>
		<h2 class="section-title">Latest Notifications</h2>
		<?php
		foreach ( array_slice( $pvi_notices, 0, 3 ) as $pvi_n ) {
			pvi_render_notice( $pvi_n );
		}
		?>
		<p style="text-align:center"><a href="<?php echo esc_url( pvi_page_url( 'notifications' ) ); ?>" class="btn btn-primary">Tamam Notifications</a></p>
	</section>
</main>
<?php
get_footer();
