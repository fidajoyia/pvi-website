<?php
/**
 * wp-admin list screens: columns, status filter, CSV export, pending badge, dashboard box.
 *
 * @package pvi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------- Applications ---------- */

add_filter(
	'manage_pvi_application_posts_columns',
	function () {
		return array(
			'cb'         => '<input type="checkbox" />',
			'title'      => 'Application',
			'pvi_father' => 'Walid',
			'pvi_course' => 'Course',
			'pvi_phone'  => 'Phone',
			'pvi_pct'    => '%',
			'pvi_status' => 'Status',
			'date'       => 'Submitted',
		);
	}
);

add_action(
	'manage_pvi_application_posts_custom_column',
	function ( $col, $id ) {
		switch ( $col ) {
			case 'pvi_father':
				echo esc_html( get_post_meta( $id, '_pvi_father', true ) );
				break;
			case 'pvi_course':
				echo esc_html( get_post_meta( $id, '_pvi_course', true ) );
				break;
			case 'pvi_phone':
				echo esc_html( get_post_meta( $id, '_pvi_phone', true ) );
				break;
			case 'pvi_pct':
				echo esc_html( get_post_meta( $id, '_pvi_percentage', true ) ) . '%';
				break;
			case 'pvi_status':
				$s      = get_post_meta( $id, '_pvi_status', true );
				$colors = array(
					'Pending'  => '#b26a00',
					'Accepted' => '#1d6b61',
					'Rejected' => '#9b1c1c',
				);
				printf( '<strong style="color:%s">%s</strong>', esc_attr( isset( $colors[ $s ] ) ? $colors[ $s ] : '#333' ), esc_html( $s ) );
				break;
		}
	},
	10,
	2
);

// Status filter + CSV export button above the Applications list.
add_action(
	'restrict_manage_posts',
	function ( $post_type ) {
		if ( 'pvi_application' !== $post_type ) {
			return;
		}
		$current = isset( $_GET['pvi_status'] ) ? sanitize_text_field( wp_unslash( $_GET['pvi_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		echo '<select name="pvi_status"><option value="">All statuses</option>';
		foreach ( pvi_statuses() as $s ) {
			printf( '<option %s>%s</option>', selected( $current, $s, false ), esc_html( $s ) );
		}
		echo '</select>';
		printf( ' <a class="button" href="%s">Download Excel (CSV)</a> ', esc_url( pvi_export_url( $current ) ) );
	}
);

add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( ! is_admin() || ! $q->is_main_query() || 'pvi_application' !== $q->get( 'post_type' ) ) {
			return;
		}
		$status = isset( $_GET['pvi_status'] ) ? sanitize_text_field( wp_unslash( $_GET['pvi_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		if ( in_array( $status, pvi_statuses(), true ) ) {
			$q->set( 'meta_key', '_pvi_status' );
			$q->set( 'meta_value', $status );
		}
	}
);

// Search applications by name, CNIC, phone or ID (stored in the title and meta).
add_filter(
	'posts_search',
	function ( $search, $q ) {
		global $wpdb;
		if ( ! is_admin() || ! $q->is_main_query() || 'pvi_application' !== $q->get( 'post_type' ) || '' === $q->get( 's' ) ) {
			return $search;
		}
		$like = '%' . $wpdb->esc_like( $q->get( 's' ) ) . '%';
		return $wpdb->prepare(
			" AND ( {$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key IN ('_pvi_cnic','_pvi_phone','_pvi_father') AND meta_value LIKE %s ) ) ",
			$like,
			$like
		);
	},
	10,
	2
);

add_action(
	'admin_post_pvi_export_apps',
	function () {
		if ( ! current_user_can( 'edit_others_pvi_items' ) ) {
			wp_die( 'Not allowed.' );
		}
		check_admin_referer( 'pvi_export_apps' );
		$status = isset( $_GET['pvi_status'] ) ? sanitize_text_field( wp_unslash( $_GET['pvi_status'] ) ) : '';
		$args   = array(
			'post_type'   => 'pvi_application',
			'post_status' => 'publish',
			'numberposts' => -1,
			'orderby'     => 'date',
			'order'       => 'ASC',
		);
		if ( in_array( $status, pvi_statuses(), true ) ) {
			$args['meta_key']   = '_pvi_status'; // phpcs:ignore WordPress.DB.SlowDBQuery
			$args['meta_value'] = $status; // phpcs:ignore WordPress.DB.SlowDBQuery
		}
		$apps   = get_posts( $args );
		$fields = pvi_application_fields();

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=applications-' . gmdate( 'Y-m-d' ) . '.csv' );
		$out = fopen( 'php://output', 'w' );
		fwrite( $out, "\xEF\xBB\xBF" ); // UTF-8 BOM so Excel shows Urdu/special characters correctly.
		fputcsv( $out, array_merge( array_values( $fields ), array( 'Status', 'Submitted' ) ) );
		foreach ( $apps as $a ) {
			$row = array();
			foreach ( array_keys( $fields ) as $k ) {
				$val = (string) get_post_meta( $a->ID, '_pvi_' . $k, true );
				// Stop spreadsheet formula injection.
				$row[] = preg_match( '/^[=+\-@\t\r]/', $val ) ? "'" . $val : $val;
			}
			$row[] = get_post_meta( $a->ID, '_pvi_status', true );
			$row[] = get_the_date( 'Y-m-d H:i', $a );
			fputcsv( $out, $row );
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		exit;
	}
);

/**
 * Export link for a status ('' = all).
 *
 * @param string $status Status.
 * @return string
 */
function pvi_export_url( $status = '' ) {
	return wp_nonce_url( admin_url( 'admin-post.php?action=pvi_export_apps&pvi_status=' . rawurlencode( $status ) ), 'pvi_export_apps' );
}

// Applications > Export (Excel). Also keeps the menu with two items, which avoids a core
// bug that blocks non-admin roles from a post type menu with a single submenu entry.
add_action(
	'admin_menu',
	function () {
		add_submenu_page(
			'edit.php?post_type=pvi_application',
			'Export Applications',
			'Export (Excel)',
			'edit_others_pvi_items',
			'pvi-export',
			function () {
				echo '<div class="wrap"><h1>Export Applications</h1><p>CSV file Excel mein khulti hai.</p><p>';
				printf( '<a class="button button-primary" href="%s">All applications</a> ', esc_url( pvi_export_url() ) );
				foreach ( pvi_statuses() as $s ) {
					printf( '<a class="button" href="%s">%s</a> ', esc_url( pvi_export_url( $s ) ), esc_html( $s ) );
				}
				echo '</p></div>';
			}
		);
	}
);

/**
 * Number of pending applications.
 *
 * @return int
 */
function pvi_pending_count() {
	$q = new WP_Query(
		array(
			'post_type'      => 'pvi_application',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_pvi_status', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => 'Pending', // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	return (int) $q->found_posts;
}

// Red "pending" bubble on the Applications menu.
add_action(
	'admin_menu',
	function () {
		global $menu;
		if ( ! current_user_can( 'edit_pvi_items' ) || ! is_array( $menu ) ) {
			return;
		}
		$count = pvi_pending_count();
		if ( ! $count ) {
			return;
		}
		foreach ( $menu as $i => $item ) {
			if ( isset( $item[2] ) && 'edit.php?post_type=pvi_application' === $item[2] ) {
				$menu[ $i ][0] .= sprintf( ' <span class="awaiting-mod count-%1$d"><span class="pending-count">%1$d</span></span>', $count ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			}
		}
	},
	99
);

// Dashboard summary box.
add_action(
	'wp_dashboard_setup',
	function () {
		if ( ! current_user_can( 'edit_pvi_items' ) ) {
			return;
		}
		wp_add_dashboard_widget(
			'pvi_summary',
			'Prime Vocational Institute',
			function () {
				$counts = array(
					'Pending applications' => array( pvi_pending_count(), admin_url( 'edit.php?post_type=pvi_application&pvi_status=Pending' ) ),
					'Courses'              => array( (int) wp_count_posts( 'pvi_course' )->publish, admin_url( 'edit.php?post_type=pvi_course' ) ),
					'Notifications'        => array( (int) wp_count_posts( 'pvi_notice' )->publish, admin_url( 'edit.php?post_type=pvi_notice' ) ),
					'Results'              => array( (int) wp_count_posts( 'pvi_result' )->publish, admin_url( 'edit.php?post_type=pvi_result' ) ),
				);
				echo '<ul>';
				foreach ( $counts as $label => $c ) {
					printf( '<li><a href="%s"><strong>%d</strong> %s</a></li>', esc_url( $c[1] ), (int) $c[0], esc_html( $label ) );
				}
				echo '</ul>';
				printf(
					'<p><a class="button button-primary" href="%s">+ Notification</a> <a class="button" href="%s">+ Result</a> <a class="button" href="%s">+ Course</a></p>',
					esc_url( admin_url( 'post-new.php?post_type=pvi_notice' ) ),
					esc_url( admin_url( 'post-new.php?post_type=pvi_result' ) ),
					esc_url( admin_url( 'post-new.php?post_type=pvi_course' ) )
				);
			}
		);
	}
);

/* ---------- Courses ---------- */

add_filter(
	'manage_pvi_course_posts_columns',
	function ( $cols ) {
		$new = array();
		foreach ( $cols as $k => $v ) {
			if ( 'title' === $k ) {
				$new['pvi_code'] = 'Code';
			}
			if ( 'date' === $k ) {
				$new['pvi_duration'] = 'Duration';
				$new['pvi_fee']      = 'Fee';
				continue;
			}
			$new[ $k ] = $v;
		}
		return $new;
	}
);
add_action(
	'manage_pvi_course_posts_custom_column',
	function ( $col, $id ) {
		$map = array(
			'pvi_code'     => '_pvi_code',
			'pvi_duration' => '_pvi_duration',
			'pvi_fee'      => '_pvi_fee',
		);
		if ( isset( $map[ $col ] ) ) {
			$v = get_post_meta( $id, $map[ $col ], true );
			echo esc_html( '' !== $v ? $v : '—' );
		}
	},
	10,
	2
);

/* ---------- Notifications ---------- */

add_filter(
	'manage_pvi_notice_posts_columns',
	function ( $cols ) {
		$date = $cols['date'];
		unset( $cols['date'] );
		$cols['pvi_important'] = 'Important';
		$cols['date']          = $date;
		return $cols;
	}
);
add_action(
	'manage_pvi_notice_posts_custom_column',
	function ( $col, $id ) {
		if ( 'pvi_important' === $col ) {
			echo pvi_is_important( $id ) ? '⭐' : '';
		}
	},
	10,
	2
);

/* ---------- Results ---------- */

add_filter(
	'manage_pvi_result_posts_columns',
	function () {
		return array(
			'cb'           => '<input type="checkbox" />',
			'title'        => 'Result',
			'pvi_course'   => 'Course',
			'pvi_pct'      => '%',
			'pvi_pass'     => 'Status',
			'date'         => 'Date',
		);
	}
);
add_action(
	'manage_pvi_result_posts_custom_column',
	function ( $col, $id ) {
		if ( 'pvi_course' === $col ) {
			echo esc_html( get_post_meta( $id, '_pvi_course', true ) );
			return;
		}
		list( , , $pct, $passed ) = pvi_result_totals( $id );
		if ( 'pvi_pct' === $col ) {
			echo esc_html( number_format( $pct, 2 ) ) . '%';
		} elseif ( 'pvi_pass' === $col ) {
			echo $passed ? '<span style="color:#1d6b61;font-weight:700">PASS</span>' : '<span style="color:#9b1c1c;font-weight:700">FAIL</span>';
		}
	},
	10,
	2
);
