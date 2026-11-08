<?php
/**
 * Template Name: PVI – Courses
 *
 * @package pvi
 */

get_header();

$pvi_meta = function ( $icon, $label, $value ) {
	if ( '' !== $value ) {
		echo '<li>' . esc_html( $icon . ' ' . $label . ': ' . $value ) . '</li>';
	}
};
?>
<div class="page-title">
	<h1>Hamare Courses</h1>
	<p>Apni pasand ka program chunein aur apply karein</p>
</div>

<main class="container">
	<section>
		<?php
		while ( have_posts() ) :
			the_post();
			if ( '' !== trim( get_the_content() ) ) :
				?>
				<div class="entry-content page-intro"><?php the_content(); ?></div>
				<?php
			endif;
		endwhile;
		?>
		<div class="field" style="max-width:400px;margin:0 auto 30px">
			<label for="course-search" class="screen-reader-text">Course talash karein</label>
			<input type="search" id="course-search" placeholder="🔍 Course talash karein...">
		</div>
		<div id="course-list">
			<?php foreach ( pvi_courses_grouped() as $pvi_group ) : ?>
				<div class="course-group">
					<h2 class="section-title" style="margin:30px 0 20px"<?php echo $pvi_group['navttc'] ? ' id="navttc"' : ''; ?>><?php echo esc_html( $pvi_group['name'] ); ?></h2>
					<?php if ( $pvi_group['navttc'] ) : ?>
						<div class="card navttc-note">
							<strong>Muft Dakhla (Free Admission)</strong> — NAVTTC aur Prime Minister's Youth Programme ke tehat.<br>
							👥 <?php echo esc_html( pvi_opt( 'navttc_eligibility' ) ); ?><br>
							📄 Saath layein: <?php echo esc_html( pvi_opt( 'navttc_documents' ) ); ?><br>
							<?php if ( pvi_opt( 'navttc_apply_url' ) ) : ?>
								🌐 Online apply: <a href="<?php echo esc_url( pvi_opt( 'navttc_apply_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( preg_replace( '#^https?://#', '', pvi_opt( 'navttc_apply_url' ) ) ); ?></a> ·
							<?php endif; ?>
							📞 <?php echo pvi_phone_links(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
						</div>
					<?php endif; ?>
					<div class="grid">
						<?php foreach ( $pvi_group['courses'] as $pvi_c ) : ?>
							<div class="card course-card" data-search="<?php echo esc_attr( strtolower( $pvi_c['name'] . ' ' . $pvi_c['code'] ) ); ?>">
								<?php if ( $pvi_c['code'] ) : ?>
									<span class="course-code"><?php echo esc_html( $pvi_c['code'] ); ?></span>
								<?php endif; ?>
								<h3 style="margin-top:8px"><?php echo esc_html( $pvi_c['name'] ); ?></h3>
								<ul class="course-meta">
									<?php
									$pvi_meta( '⏱', 'Duration', $pvi_c['duration'] );
									$pvi_meta( 'ℹ️', 'Note', $pvi_c['note'] );
									$pvi_meta( '🎓', 'Seats', $pvi_c['seats'] );
									$pvi_meta( '💰', 'Fee', $pvi_c['fee'] );
									$pvi_meta( '✅', 'Eligibility', $pvi_c['eligibility'] );
									?>
								</ul>
								<?php if ( $pvi_c['features'] ) : ?>
									<ul class="features">
										<?php foreach ( $pvi_c['features'] as $pvi_feat ) : ?>
											<li><?php echo esc_html( $pvi_feat ); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
								<?php if ( $pvi_c['code'] ) : ?>
									<a href="<?php echo esc_url( pvi_page_url( 'apply', array( 'course' => $pvi_c['code'] ) ) ); ?>" class="btn btn-primary">Apply Karein</a>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
			<p id="course-empty" class="hidden">Koi course nahi mila.</p>
		</div>
	</section>
</main>
<?php
get_footer();
