<?php
/**
 * Online admission form: validation, saving to the database, email alert.
 *
 * The form posts back to the Apply page. No nonce is used on purpose: the form is
 * public (logged-out visitors) and Hostinger's page cache would serve stale nonces.
 * Spam is limited with a honeypot field and a per-visitor rate limit instead.
 * Field names are prefixed with pvi_ because WordPress treats a posted "name" as a query var (404).
 *
 * @package pvi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$GLOBALS['pvi_apply_errors'] = array();
$GLOBALS['pvi_apply_old']    = array();

/**
 * Apply form field labels (required ones end with *).
 *
 * @return array
 */
function pvi_apply_labels() {
	return array(
		'name'        => 'Poora Naam *',
		'father'      => 'Walid ka Naam *',
		'cnic'        => 'CNIC / B-Form *',
		'dob'         => 'Date of Birth *',
		'gender'      => 'Gender *',
		'phone'       => 'Phone *',
		'email'       => 'Email',
		'address'     => 'Address *',
		'course'      => 'Course *',
		'last_degree' => 'Pichli Degree *',
		'obtained'    => 'Obtained Marks *',
		'total'       => 'Total Marks *',
	);
}

/**
 * Previously submitted value (when the form is shown again with errors).
 *
 * @param string $key Field.
 * @return string
 */
function pvi_old( $key ) {
	return isset( $GLOBALS['pvi_apply_old'][ $key ] ) ? (string) $GLOBALS['pvi_apply_old'][ $key ] : '';
}

add_action(
	'template_redirect',
	function () {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- public form, see file header.
		if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) || empty( $_POST['pvi_apply'] ) ) {
			return;
		}
		$page = pvi_page_id( 'apply' );
		if ( ! $page || ! is_page( $page ) ) {
			return;
		}
		$back = get_permalink( $page );

		// Bots fill the hidden field; pretend success and save nothing.
		if ( ! empty( $_POST['website'] ) ) {
			wp_safe_redirect( add_query_arg( 'app', 'received', $back ), 303 );
			exit;
		}

		$data = array();
		foreach ( array_keys( pvi_apply_labels() ) as $k ) {
			$raw        = isset( $_POST[ 'pvi_' . $k ] ) ? wp_unslash( $_POST[ 'pvi_' . $k ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$data[ $k ] = ( 'address' === $k ) ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
			$data[ $k ] = trim( $data[ $k ] );
		}
		// phpcs:enable

		$errors = array();
		foreach ( pvi_apply_labels() as $k => $label ) {
			if ( '*' === substr( $label, -1 ) && '' === $data[ $k ] ) {
				$errors[] = rtrim( $label, ' *' ) . ( in_array( $k, array( 'gender', 'course', 'last_degree' ), true ) ? ' chunein' : ' likhein' );
			}
		}
		if ( '' !== $data['cnic'] && ! preg_match( '/^\d{5}-?\d{7}-?\d$/', $data['cnic'] ) ) {
			$errors[] = 'CNIC sahi format mein likhein (35202-1234567-1)';
		}
		if ( '' !== $data['phone'] && ! preg_match( '/^03\d{2}-?\d{7}$/', $data['phone'] ) ) {
			$errors[] = 'Phone number sahi likhein (03XX-XXXXXXX)';
		}
		if ( '' !== $data['email'] && ! is_email( $data['email'] ) ) {
			$errors[] = 'Email sahi likhein';
		}
		if ( '' !== $data['dob'] ) {
			$dob = DateTime::createFromFormat( 'Y-m-d', $data['dob'] );
			if ( ! $dob || $dob->format( 'Y-m-d' ) !== $data['dob'] || $dob > new DateTime() ) {
				$errors[] = 'Date of Birth sahi likhein';
			}
		}
		if ( '' !== $data['gender'] && ! in_array( $data['gender'], array( 'Male', 'Female' ), true ) ) {
			$errors[] = 'Gender chunein';
		}
		if ( '' !== $data['last_degree'] && ! in_array( $data['last_degree'], array( 'Primary', 'Middle', 'Matric', 'Intermediate', 'Bachelor' ), true ) ) {
			$errors[] = 'Pichli Degree chunein';
		}
		$courses = pvi_course_options();
		if ( '' !== $data['course'] && ! isset( $courses[ $data['course'] ] ) ) {
			$errors[] = 'Course list mein se chunein';
		}
		$obtained = is_numeric( $data['obtained'] ) ? (float) $data['obtained'] : -1;
		$total    = is_numeric( $data['total'] ) ? (float) $data['total'] : 0;
		if ( '' !== $data['obtained'] && $obtained < 0 ) {
			$errors[] = 'Obtained marks sahi likhein';
		}
		if ( '' !== $data['total'] && $total <= 0 ) {
			$errors[] = 'Total marks sahi likhein';
		}
		if ( $obtained > $total && $total > 0 ) {
			$errors[] = 'Obtained marks total se zyada nahi ho sakte';
		}

		// Max 5 applications per visitor per hour.
		$ip       = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$rate_key = 'pvi_apply_' . md5( $ip . wp_salt( 'nonce' ) );
		$count    = (int) get_transient( $rate_key );
		if ( ! $errors && $count >= 5 ) {
			$errors[] = 'Bohat zyada applications bheji gayi hain. Thori der baad dobara koshish karein ya call karein: ' . pvi_opt( 'phone' );
		}

		if ( $errors ) {
			$GLOBALS['pvi_apply_errors'] = $errors;
			$GLOBALS['pvi_apply_old']    = $data;
			return; // Show the form again with the messages.
		}

		$seq    = (int) get_option( 'pvi_app_seq', 0 ) + 1;
		$app_id = sprintf( 'APP-%s-%04d', wp_date( 'Y' ), $seq );
		update_option( 'pvi_app_seq', $seq, false );

		$data['percentage'] = number_format( ( $obtained / $total ) * 100, 1, '.', '' );
		$data['app_id']     = $app_id;

		$post_id = wp_insert_post(
			array(
				'post_type'   => 'pvi_application',
				'post_status' => 'publish',
				'post_title'  => $app_id . ' — ' . $data['name'],
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			$GLOBALS['pvi_apply_errors'] = array( 'Application save nahi ho saki. Baraye meharbani dobara koshish karein ya call karein: ' . pvi_opt( 'phone' ) );
			$GLOBALS['pvi_apply_old']    = $data;
			return;
		}
		foreach ( $data as $k => $v ) {
			update_post_meta( $post_id, '_pvi_' . $k, $v );
		}
		update_post_meta( $post_id, '_pvi_status', 'Pending' );
		set_transient( $rate_key, $count + 1, HOUR_IN_SECONDS );

		// Email alert to the office.
		$to = pvi_opt( 'notify_email' );
		$to = is_email( $to ) ? $to : get_option( 'admin_email' );
		if ( $to ) {
			$lines = array();
			foreach ( pvi_application_fields() as $k => $label ) {
				$lines[] = $label . ': ' . ( isset( $data[ $k ] ) ? $data[ $k ] : '' );
			}
			$lines[] = '';
			$lines[] = 'Dashboard: ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' );
			wp_mail( $to, 'Nayi Application: ' . $app_id . ' — ' . $data['name'] . ' (' . $data['course'] . ')', implode( "\n", $lines ) );
		}

		wp_safe_redirect( add_query_arg( 'app', rawurlencode( $app_id ), $back ), 303 );
		exit;
	}
);
