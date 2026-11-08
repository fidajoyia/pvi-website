<?php
/**
 * Edit screens for courses, notifications, results and applications.
 *
 * @package pvi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Course text fields: key => [ label, placeholder ].
 *
 * @return array
 */
function pvi_course_fields() {
	return array(
		'code'        => array( 'Course Code *', 'BSIT' ),
		'duration'    => array( 'Duration', '6 Mahine' ),
		'fee'         => array( 'Fee', 'Rs. 15,000' ),
		'seats'       => array( 'Seats', '30' ),
		'eligibility' => array( 'Eligibility', 'Matric pass' ),
		'note'        => array( 'Note', 'ICS ke barabar · Approved by PBTE' ),
	);
}

/**
 * Application statuses.
 *
 * @return string[]
 */
function pvi_statuses() {
	return array( 'Pending', 'Accepted', 'Rejected' );
}

/**
 * Application detail fields: meta key => label.
 *
 * @return array
 */
function pvi_application_fields() {
	return array(
		'app_id'      => 'Application ID',
		'name'        => 'Naam',
		'father'      => 'Walid ka Naam',
		'cnic'        => 'CNIC / B-Form',
		'dob'         => 'Date of Birth',
		'gender'      => 'Gender',
		'phone'       => 'Phone',
		'email'       => 'Email',
		'address'     => 'Address',
		'course'      => 'Course',
		'last_degree' => 'Pichli Degree',
		'obtained'    => 'Obtained Marks',
		'total'       => 'Total Marks',
		'percentage'  => 'Percentage',
	);
}

/**
 * Queue a one-time admin notice for the current user.
 *
 * @param string $msg  Message.
 * @param string $type error|warning|success.
 */
function pvi_flash( $msg, $type = 'warning' ) {
	$key   = 'pvi_flash_' . get_current_user_id();
	$items = get_transient( $key );
	$items = is_array( $items ) ? $items : array();
	$items[] = array( $msg, $type );
	set_transient( $key, $items, 120 );
}

add_action(
	'admin_notices',
	function () {
		$key   = 'pvi_flash_' . get_current_user_id();
		$items = get_transient( $key );
		if ( ! $items ) {
			return;
		}
		delete_transient( $key );
		foreach ( $items as $item ) {
			printf( '<div class="notice notice-%s is-dismissible"><p>%s</p></div>', esc_attr( $item[1] ), esc_html( $item[0] ) );
		}
	}
);

/**
 * Common save checks.
 *
 * @param int    $post_id Post ID.
 * @param string $nonce   Nonce name/action.
 * @return bool
 */
function pvi_can_save( $post_id, $nonce ) {
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
		return false;
	}
	if ( empty( $_POST[ $nonce ] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ $nonce ] ) ), $nonce ) ) {
		return false;
	}
	return current_user_can( 'edit_post', $post_id );
}

/**
 * Posted text value.
 *
 * @param string $key Field name.
 * @return string
 */
function pvi_post_text( $key ) {
	return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
}

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box( 'pvi_course_details', 'Course Details', 'pvi_course_box', 'pvi_course', 'normal', 'high' );
		add_meta_box( 'pvi_notice_options', 'Options', 'pvi_notice_box', 'pvi_notice', 'side', 'default' );
		add_meta_box( 'pvi_result_details', 'Result Card', 'pvi_result_box', 'pvi_result', 'normal', 'high' );
		add_meta_box( 'pvi_app_details', 'Application Details', 'pvi_app_box', 'pvi_application', 'normal', 'high' );
		add_meta_box( 'pvi_app_status', 'Status', 'pvi_app_status_box', 'pvi_application', 'side', 'high' );
	}
);

add_filter(
	'enter_title_here',
	function ( $text, $post ) {
		if ( 'pvi_course' === $post->post_type ) {
			return 'Course ka poora naam';
		}
		if ( 'pvi_notice' === $post->post_type ) {
			return 'Notification ka title';
		}
		return $text;
	},
	10,
	2
);

/* ---------- Courses ---------- */

/**
 * Course meta box.
 *
 * @param WP_Post $post Post.
 */
function pvi_course_box( $post ) {
	wp_nonce_field( 'pvi_course_nonce', 'pvi_course_nonce' );
	echo '<table class="form-table" role="presentation">';
	foreach ( pvi_course_fields() as $k => $f ) {
		printf(
			'<tr><th><label for="pvi_%1$s">%2$s</label></th><td><input class="regular-text" id="pvi_%1$s" name="pvi_%1$s" value="%3$s" placeholder="%4$s"%5$s></td></tr>',
			esc_attr( $k ),
			esc_html( $f[0] ),
			esc_attr( get_post_meta( $post->ID, '_pvi_' . $k, true ) ),
			esc_attr( $f[1] ),
			'code' === $k ? ' required' : ''
		);
	}
	printf(
		'<tr><th><label for="pvi_features">Features</label></th><td><textarea class="large-text" rows="5" id="pvi_features" name="pvi_features">%s</textarea><p class="description">Har line par ek point. Khali chhor dein to nahi dikhega.</p></td></tr>',
		esc_textarea( get_post_meta( $post->ID, '_pvi_features', true ) )
	);
	echo '</table><p class="description">Jo field maloom na ho (fee, seats waghera) usay khali chhor dein — website par nahi dikhegi. Category right side se chunein; "Order" se category ke andar tarteeb badlein.</p>';
}

add_action(
	'save_post_pvi_course',
	function ( $post_id ) {
		if ( ! pvi_can_save( $post_id, 'pvi_course_nonce' ) ) {
			return;
		}
		foreach ( array_keys( pvi_course_fields() ) as $k ) {
			update_post_meta( $post_id, '_pvi_' . $k, pvi_post_text( 'pvi_' . $k ) );
		}
		$features = isset( $_POST['pvi_features'] ) ? sanitize_textarea_field( wp_unslash( $_POST['pvi_features'] ) ) : '';
		update_post_meta( $post_id, '_pvi_features', $features );

		$code = pvi_post_text( 'pvi_code' );
		if ( '' === $code ) {
			pvi_flash( 'Course code khali hai — Apply form mein yeh course tab tak nahi dikhega jab tak code na likhein.', 'error' );
		} else {
			$dupes = get_posts(
				array(
					'post_type'    => 'pvi_course',
					'post_status'  => 'any',
					'numberposts'  => 1,
					'fields'       => 'ids',
					'post__not_in' => array( $post_id ),
					'meta_key'     => '_pvi_code', // phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_value'   => $code, // phpcs:ignore WordPress.DB.SlowDBQuery
				)
			);
			if ( $dupes ) {
				pvi_flash( 'Yeh course code (' . $code . ') pehle se kisi aur course ka hai. Baraye meharbani alag code rakhein.', 'error' );
			}
		}
	}
);

/* ---------- Notifications ---------- */

/**
 * Notification options box.
 *
 * @param WP_Post $post Post.
 */
function pvi_notice_box( $post ) {
	wp_nonce_field( 'pvi_notice_nonce', 'pvi_notice_nonce' );
	printf(
		'<label><input type="checkbox" name="pvi_important" value="1"%s> Important</label><p class="description">Important notifications golden border aur menu badge ke saath dikhti hain. Tareekh "Publish" box se badlein.</p>',
		checked( pvi_is_important( $post->ID ), true, false )
	);
}

add_action(
	'save_post_pvi_notice',
	function ( $post_id ) {
		if ( ! pvi_can_save( $post_id, 'pvi_notice_nonce' ) ) {
			return;
		}
		update_post_meta( $post_id, '_pvi_important', empty( $_POST['pvi_important'] ) ? '0' : '1' );
	}
);

/* ---------- Results ---------- */

/**
 * Result meta box.
 *
 * @param WP_Post $post Post.
 */
function pvi_result_box( $post ) {
	wp_nonce_field( 'pvi_result_nonce', 'pvi_result_nonce' );
	$roll     = get_post_meta( $post->ID, '_pvi_roll', true );
	$name     = get_post_meta( $post->ID, '_pvi_student', true );
	$course   = get_post_meta( $post->ID, '_pvi_course', true );
	$semester = get_post_meta( $post->ID, '_pvi_semester', true );
	$subjects = get_post_meta( $post->ID, '_pvi_subjects', true );
	$subjects = is_array( $subjects ) && $subjects ? $subjects : array(
		array(
			'name'     => '',
			'total'    => 100,
			'obtained' => '',
		),
		array(
			'name'     => '',
			'total'    => 100,
			'obtained' => '',
		),
	);
	$options  = pvi_course_options();
	if ( $course && ! isset( $options[ $course ] ) ) {
		$options[ $course ] = $course;
	}
	?>
	<table class="form-table" role="presentation">
		<tr><th><label for="pvi_roll">Roll Number *</label></th><td><input class="regular-text" id="pvi_roll" name="pvi_roll" value="<?php echo esc_attr( $roll ); ?>" required></td></tr>
		<tr><th><label for="pvi_student">Student Naam *</label></th><td><input class="regular-text" id="pvi_student" name="pvi_student" value="<?php echo esc_attr( $name ); ?>" required></td></tr>
		<tr><th><label for="pvi_course">Course *</label></th><td>
			<select id="pvi_course" name="pvi_course" required>
				<option value="">-- Course chunein --</option>
				<?php foreach ( $options as $code => $label ) : ?>
					<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $course, $code ); ?>><?php echo esc_html( $code . ' — ' . $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</td></tr>
		<tr><th><label for="pvi_semester">Semester / Class *</label></th><td><input class="regular-text" id="pvi_semester" name="pvi_semester" value="<?php echo esc_attr( $semester ); ?>" placeholder="1st Semester" required></td></tr>
	</table>
	<h4>Subjects</h4>
	<table class="widefat striped" id="pvi-subjects" style="max-width:640px">
		<thead><tr><th>Subject</th><th style="width:110px">Total</th><th style="width:110px">Obtained</th><th style="width:40px"></th></tr></thead>
		<tbody>
		<?php foreach ( $subjects as $s ) : ?>
			<tr>
				<td><input class="widefat" name="pvi_sub_name[]" value="<?php echo esc_attr( $s['name'] ); ?>"></td>
				<td><input type="number" min="1" class="widefat" name="pvi_sub_total[]" value="<?php echo esc_attr( $s['total'] ); ?>"></td>
				<td><input type="number" min="0" step="any" class="widefat" name="pvi_sub_obt[]" value="<?php echo esc_attr( $s['obtained'] ); ?>"></td>
				<td><button type="button" class="button-link pvi-remove-row" aria-label="Remove subject">✕</button></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<p><button type="button" class="button" id="pvi-add-subject">+ Subject</button></p>
	<p class="description">Wohi Roll Number dobara alag semester ke saath add kar sakte hain — website par us roll number ke tamam results dikhenge.</p>
	<script>
	(function () {
		var body = document.querySelector('#pvi-subjects tbody');
		document.getElementById('pvi-add-subject').addEventListener('click', function () {
			var row = body.rows[0].cloneNode(true);
			row.querySelectorAll('input').forEach(function (i) { i.value = i.name === 'pvi_sub_total[]' ? '100' : ''; });
			body.appendChild(row);
		});
		body.addEventListener('click', function (e) {
			if (e.target.classList.contains('pvi-remove-row') && body.rows.length > 1) { e.target.closest('tr').remove(); }
		});
	})();
	</script>
	<?php
}

add_filter(
	'wp_insert_post_data',
	function ( $data ) {
		if ( 'pvi_result' === $data['post_type'] && ! empty( $_POST['pvi_result_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['pvi_result_nonce'] ) ), 'pvi_result_nonce' ) ) {
			$sem                = pvi_post_text( 'pvi_semester' );
			$title              = 'Roll ' . pvi_post_text( 'pvi_roll' ) . ' — ' . pvi_post_text( 'pvi_student' ) . ( $sem ? ' (' . $sem . ')' : '' );
			$data['post_title'] = wp_slash( $title );
		}
		return $data;
	}
);

add_action(
	'save_post_pvi_result',
	function ( $post_id ) {
		if ( ! pvi_can_save( $post_id, 'pvi_result_nonce' ) ) {
			return;
		}
		update_post_meta( $post_id, '_pvi_roll', pvi_post_text( 'pvi_roll' ) );
		update_post_meta( $post_id, '_pvi_student', pvi_post_text( 'pvi_student' ) );
		update_post_meta( $post_id, '_pvi_course', pvi_post_text( 'pvi_course' ) );
		update_post_meta( $post_id, '_pvi_semester', pvi_post_text( 'pvi_semester' ) );

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per item below.
		$names  = isset( $_POST['pvi_sub_name'] ) ? (array) wp_unslash( $_POST['pvi_sub_name'] ) : array();
		$totals = isset( $_POST['pvi_sub_total'] ) ? (array) wp_unslash( $_POST['pvi_sub_total'] ) : array();
		$obts   = isset( $_POST['pvi_sub_obt'] ) ? (array) wp_unslash( $_POST['pvi_sub_obt'] ) : array();
		// phpcs:enable
		$subjects = array();
		foreach ( $names as $i => $n ) {
			$n = sanitize_text_field( $n );
			if ( '' === $n ) {
				continue;
			}
			$total    = isset( $totals[ $i ] ) ? (float) $totals[ $i ] : 0;
			$obtained = isset( $obts[ $i ] ) ? (float) $obts[ $i ] : 0;
			if ( $total <= 0 ) {
				pvi_flash( '"' . $n . '" ke total marks 0 se zyada hone chahiye.', 'error' );
				$total = 100;
			}
			if ( $obtained > $total ) {
				pvi_flash( '"' . $n . '" mein obtained marks total se zyada hain — baraye meharbani theek karein.', 'error' );
			}
			$subjects[] = array(
				'name'     => $n,
				'total'    => $total,
				'obtained' => $obtained,
			);
		}
		if ( ! $subjects ) {
			pvi_flash( 'Kam az kam ek subject add karein.', 'error' );
		}
		update_post_meta( $post_id, '_pvi_subjects', $subjects );
	}
);

/**
 * Totals for a result post.
 *
 * @param int $post_id Result ID.
 * @return array [ total, obtained, pct, passed, subjects ]
 */
function pvi_result_totals( $post_id ) {
	$subjects = get_post_meta( $post_id, '_pvi_subjects', true );
	$subjects = is_array( $subjects ) ? $subjects : array();
	$total    = 0;
	$obtained = 0;
	$passed   = (bool) $subjects;
	foreach ( $subjects as $s ) {
		$total    += (float) $s['total'];
		$obtained += (float) $s['obtained'];
		if ( (float) $s['total'] > 0 && ( (float) $s['obtained'] / (float) $s['total'] ) < 0.4 ) {
			$passed = false;
		}
	}
	$pct = $total > 0 ? ( $obtained / $total ) * 100 : 0;
	return array( $total, $obtained, $pct, $passed, $subjects );
}

/* ---------- Applications ---------- */

/**
 * Application details (read-only).
 *
 * @param WP_Post $post Post.
 */
function pvi_app_box( $post ) {
	echo '<table class="widefat striped"><tbody>';
	foreach ( pvi_application_fields() as $k => $label ) {
		$val = (string) get_post_meta( $post->ID, '_pvi_' . $k, true );
		if ( 'percentage' === $k && '' !== $val ) {
			$val .= '%';
		}
		if ( 'phone' === $k && $val ) {
			$val = '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $val ) ) . '">' . esc_html( $val ) . '</a>';
		} elseif ( 'email' === $k && $val ) {
			$val = '<a href="mailto:' . esc_attr( $val ) . '">' . esc_html( $val ) . '</a>';
		} else {
			$val = esc_html( $val );
		}
		printf( '<tr><th style="width:180px">%s</th><td>%s</td></tr>', esc_html( $label ), $val ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
	printf( '<tr><th>Submitted</th><td>%s</td></tr>', esc_html( get_the_date( 'j M Y, g:i a', $post ) ) );
	echo '</tbody></table>';
}

/**
 * Application status box.
 *
 * @param WP_Post $post Post.
 */
function pvi_app_status_box( $post ) {
	wp_nonce_field( 'pvi_app_nonce', 'pvi_app_nonce' );
	$current = get_post_meta( $post->ID, '_pvi_status', true );
	echo '<select name="pvi_status" style="width:100%">';
	foreach ( pvi_statuses() as $s ) {
		printf( '<option %s>%s</option>', selected( $current, $s, false ), esc_html( $s ) );
	}
	echo '</select><p class="description">Status badal kar "Update" dabayein.</p>';
}

add_action(
	'save_post_pvi_application',
	function ( $post_id ) {
		if ( ! pvi_can_save( $post_id, 'pvi_app_nonce' ) ) {
			return;
		}
		$status = pvi_post_text( 'pvi_status' );
		if ( in_array( $status, pvi_statuses(), true ) ) {
			update_post_meta( $post_id, '_pvi_status', $status );
		}
	}
);
