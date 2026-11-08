<?php
/**
 * Template Name: PVI – Results
 *
 * @package pvi
 */

get_header();

$pvi_roll    = isset( $_GET['roll'] ) ? trim( sanitize_text_field( wp_unslash( $_GET['roll'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$pvi_results = array();
if ( '' !== $pvi_roll ) {
	$pvi_results = get_posts(
		array(
			'post_type'   => 'pvi_result',
			'post_status' => 'publish',
			'numberposts' => 20,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'meta_key'    => '_pvi_roll', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => $pvi_roll, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
}
?>
<div class="page-title">
	<h1>Result Check Karein</h1>
	<p>Apna Roll Number likh kar result dekhein</p>
</div>

<main class="container" style="max-width:850px">
	<section>
		<form id="result-form" class="card" method="get" action="<?php echo esc_url( get_permalink( pvi_page_id( 'results' ) ) ); ?>" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end">
			<div class="field" style="flex:1;min-width:200px;margin:0">
				<label for="roll">Roll Number</label>
				<input id="roll" name="roll" placeholder="Maslan: 1001" value="<?php echo esc_attr( $pvi_roll ); ?>" required>
			</div>
			<button class="btn btn-primary">Result Dekhein</button>
		</form>

		<div id="output" style="margin-top:24px">
			<?php if ( '' !== $pvi_roll && ! $pvi_results ) : ?>
				<div class="alert alert-error">Roll number <strong><?php echo esc_html( $pvi_roll ); ?></strong> ka result nahi mila.</div>
			<?php endif; ?>

			<?php foreach ( $pvi_results as $pvi_r ) : ?>
				<?php list( $pvi_total, $pvi_obt, $pvi_pct, $pvi_passed, $pvi_subjects ) = pvi_result_totals( $pvi_r->ID ); ?>
				<div class="card result-card">
					<h3 style="text-align:center;margin-bottom:16px"><?php echo esc_html( pvi_opt( 'name' ) ); ?> — Result Card</h3>
					<div class="result-head">
						<div><strong>Naam:</strong> <?php echo esc_html( get_post_meta( $pvi_r->ID, '_pvi_student', true ) ); ?></div>
						<div><strong>Roll No:</strong> <?php echo esc_html( get_post_meta( $pvi_r->ID, '_pvi_roll', true ) ); ?></div>
						<div><strong>Course:</strong> <?php echo esc_html( get_post_meta( $pvi_r->ID, '_pvi_course', true ) ); ?></div>
						<div><strong>Semester:</strong> <?php echo esc_html( get_post_meta( $pvi_r->ID, '_pvi_semester', true ) ); ?></div>
					</div>
					<div class="table-wrap">
						<table>
							<tr><th>Subject</th><th>Total</th><th>Obtained</th><th>Grade</th></tr>
							<?php foreach ( $pvi_subjects as $pvi_s ) : ?>
								<tr>
									<td><?php echo esc_html( $pvi_s['name'] ); ?></td>
									<td><?php echo esc_html( 0 + $pvi_s['total'] ); ?></td>
									<td><?php echo esc_html( 0 + $pvi_s['obtained'] ); ?></td>
									<td><?php echo esc_html( pvi_grade( $pvi_s['total'] > 0 ? $pvi_s['obtained'] / $pvi_s['total'] * 100 : 0 ) ); ?></td>
								</tr>
							<?php endforeach; ?>
							<tr><th>Total</th><th><?php echo esc_html( 0 + $pvi_total ); ?></th><th><?php echo esc_html( 0 + $pvi_obt ); ?></th><th><?php echo esc_html( pvi_grade( $pvi_pct ) ); ?></th></tr>
						</table>
					</div>
					<p style="margin-top:16px;font-size:18px">
						Percentage: <strong><?php echo esc_html( number_format( $pvi_pct, 2 ) ); ?>%</strong> &nbsp;|&nbsp;
						Status: <span class="<?php echo $pvi_passed ? 'pass' : 'fail'; ?>"><?php echo $pvi_passed ? 'PASS' : 'FAIL'; ?></span>
					</p>
				</div>
			<?php endforeach; ?>

			<?php if ( $pvi_results ) : ?>
				<button type="button" class="btn btn-primary no-print" style="margin-top:12px" data-print>🖨 Print Result</button>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php
get_footer();
