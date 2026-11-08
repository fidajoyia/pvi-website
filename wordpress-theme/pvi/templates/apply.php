<?php
/**
 * Template Name: PVI – Apply Form
 *
 * Submissions are processed in inc/applications.php.
 *
 * @package pvi
 */

get_header();

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only display values.
$pvi_app_param = isset( $_GET['app'] ) ? sanitize_text_field( wp_unslash( $_GET['app'] ) ) : '';
$pvi_selected  = pvi_old( 'course' ) ? pvi_old( 'course' ) : ( isset( $_GET['course'] ) ? sanitize_text_field( wp_unslash( $_GET['course'] ) ) : '' );
// phpcs:enable
$pvi_errors = $GLOBALS['pvi_apply_errors'];

$pvi_select = function ( $name, $options, $placeholder = '-- Chunein --' ) {
	echo '<select name="pvi_' . esc_attr( $name ) . '" id="pvi-' . esc_attr( $name ) . '" required><option value="">' . esc_html( $placeholder ) . '</option>';
	foreach ( $options as $o ) {
		echo '<option' . selected( pvi_old( $name ), $o, false ) . '>' . esc_html( $o ) . '</option>';
	}
	echo '</select>';
};
$pvi_input  = function ( $name, $label, $type = 'text', $attrs = '' ) {
	$required = '*' === substr( $label, -1 );
	printf(
		'<div class="field"><label for="pvi-%1$s">%2$s</label><input type="%3$s" id="pvi-%1$s" name="pvi_%1$s" value="%4$s"%5$s %6$s></div>',
		esc_attr( $name ),
		esc_html( $label ),
		esc_attr( $type ),
		esc_attr( pvi_old( $name ) ),
		$required ? ' required' : '',
		$attrs // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes below.
	);
};
?>
<div class="page-title">
	<h1>Online Admission Form</h1>
	<p>Neeche diya gaya form mukammal karein</p>
</div>

<main class="container" style="max-width:800px">
	<section>
		<?php if ( preg_match( '/^APP-\d{4}-\d{4,}$/', $pvi_app_param ) ) : ?>
			<div class="alert alert-success" id="apply-result">
				✅ <strong>Shukriya!</strong> Aap ki application jama ho gayi hai.<br>
				Application ID: <strong><?php echo esc_html( $pvi_app_param ); ?></strong> — isay sambhal kar rakhein. Hum jald aap se rabta karenge.
			</div>
		<?php elseif ( 'received' === $pvi_app_param ) : ?>
			<div class="alert alert-success" id="apply-result">✅ <strong>Shukriya!</strong> Aap ki application jama ho gayi hai.</div>
		<?php endif; ?>

		<?php if ( $pvi_errors ) : ?>
			<div class="alert alert-error" id="apply-result" role="alert">
				<strong>Baraye meharbani yeh theek karein:</strong><br>
				<?php echo implode( '<br>', array_map( 'esc_html', $pvi_errors ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>

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

		<form method="post" action="<?php echo esc_url( get_permalink( pvi_page_id( 'apply' ) ) ); ?>" class="card" id="apply-form">
			<input type="hidden" name="pvi_apply" value="1">
			<div class="hp-field" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

			<h3 style="margin-bottom:16px">Personal Information</h3>
			<div class="row">
				<?php $pvi_input( 'name', 'Poora Naam *' ); ?>
				<?php $pvi_input( 'father', 'Walid ka Naam *' ); ?>
			</div>
			<div class="row">
				<?php $pvi_input( 'cnic', 'CNIC / B-Form *', 'text', 'placeholder="35202-1234567-1" inputmode="numeric" pattern="\d{5}-?\d{7}-?\d"' ); ?>
				<?php $pvi_input( 'dob', 'Date of Birth *', 'date', 'max="' . esc_attr( wp_date( 'Y-m-d' ) ) . '"' ); ?>
			</div>
			<div class="row">
				<div class="field"><label for="pvi-gender">Gender *</label><?php $pvi_select( 'gender', array( 'Male', 'Female' ) ); ?></div>
				<?php $pvi_input( 'phone', 'Phone *', 'tel', 'placeholder="03XX-XXXXXXX" pattern="03\d{2}-?\d{7}"' ); ?>
			</div>
			<?php $pvi_input( 'email', 'Email', 'email' ); ?>
			<div class="field"><label for="pvi-address">Address *</label><textarea id="pvi-address" name="pvi_address" rows="2" required><?php echo esc_textarea( pvi_old( 'address' ) ); ?></textarea></div>

			<h3 style="margin:10px 0 16px">Academic Information</h3>
			<div class="row">
				<div class="field"><label for="pvi-course">Course *</label>
					<select name="pvi_course" id="pvi-course" required>
						<option value="">-- Course chunein --</option>
						<?php foreach ( pvi_courses_grouped() as $pvi_group ) : ?>
							<optgroup label="<?php echo esc_attr( $pvi_group['name'] ); ?>">
								<?php foreach ( $pvi_group['courses'] as $pvi_c ) : ?>
									<?php if ( $pvi_c['code'] ) : ?>
										<option value="<?php echo esc_attr( $pvi_c['code'] ); ?>" <?php selected( $pvi_selected, $pvi_c['code'] ); ?>><?php echo esc_html( $pvi_c['name'] ); ?></option>
									<?php endif; ?>
								<?php endforeach; ?>
							</optgroup>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="field"><label for="pvi-last_degree">Pichli Degree *</label><?php $pvi_select( 'last_degree', array( 'Primary', 'Middle', 'Matric', 'Intermediate', 'Bachelor' ) ); ?></div>
			</div>
			<div class="row">
				<?php $pvi_input( 'obtained', 'Obtained Marks *', 'number', 'min="0" step="any"' ); ?>
				<?php $pvi_input( 'total', 'Total Marks *', 'number', 'min="1" step="any"' ); ?>
			</div>

			<button type="submit" class="btn btn-accent">Submit Application</button>
		</form>
	</section>
</main>
<?php
get_footer();
