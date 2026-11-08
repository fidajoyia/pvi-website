<?php
/**
 * First activation: roles, pages, course categories, courses and notifications
 * (copied from the original static site's data.js). Runs once.
 *
 * @package pvi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_switch_theme',
	function () {
		pvi_setup_roles();
		pvi_seed();
		flush_rewrite_rules();
	}
);

/**
 * Original course list.
 *
 * @return array[]
 */
function pvi_seed_courses() {
	$navttc_elig = 'Primary / Middle / Matric / Intermediate · Umar 18-45 saal · Male & Female';
	$cat_dip     = 'Diploma Programs';
	$cat_it      = 'IT & Computer Courses';
	$cat_tech    = 'Technical & Skill Courses';
	$cat_med     = 'Evening Coaching Classes (Medical / Health)';
	$cat_nav     = 'NAVTTC Free Courses (PM Youth Programme)';
	$n           = function ( $code, $name, $duration ) use ( $cat_nav, $navttc_elig ) {
		return array( $code, $cat_nav, $name, $duration, 'Muft (Free)', $navttc_elig );
	};
	// [ code, category, name, duration, fee, eligibility, note, features ].
	return array(
		array( 'DIT', $cat_dip, 'DIT (Diploma of Information Technology)', '2 Saal (Semester System)', '', 'Matric pass', 'ICS ke barabar · Approved by PBTE', "2 Year Semester System Program\nPractical approach ke saath\nPehle saal 9 computer courses\nDoosre saal English, Urdu, Math waghera\nPehle saal students ko project diya jata hai" ),
		array( 'MTECH', $cat_dip, 'Matric Tech', '2 Saal' ),
		array( 'CO', $cat_dip, 'Computer Operator', '1 Saal' ),
		array( 'CCA', $cat_it, 'Certificate in Computer Application (CCA)', '6 Mahine' ),
		array( 'DMSEO', $cat_it, 'Digital Marketing & SEO' ),
		array( 'ECOM', $cat_it, 'E-Commerce' ),
		array( 'AIECOM', $cat_it, 'AI Powered E-Commerce' ),
		array( 'ANDROID', $cat_it, 'Android Mobile Phone Application' ),
		array( 'FASHION', $cat_tech, 'Fashion Designing & Dress Making' ),
		array( 'ELEC', $cat_tech, 'Electrician' ),
		array( 'PLUMB', $cat_tech, 'Plumber & Pipe Fitter' ),
		array( 'SAFETY', $cat_tech, 'Safety Inspector' ),
		array( 'SURVEY', $cat_tech, 'Civil Surveyor' ),
		array( 'LHV', $cat_med, 'LHV (Lady Health Visitor)', '2 Saal' ),
		array( 'CNA', $cat_med, 'CNA (Certified Nursing Assistant)', '2 Saal' ),
		array( 'CMW', $cat_med, 'CMW (Community Midwife)' ),
		array( 'PHARM', $cat_med, 'Pharmacy Technician', '2 Saal' ),
		array( 'LAB', $cat_med, 'Lab Technician' ),
		array( 'OTT', $cat_med, 'OTT (Operation Theatre Technician)' ),
		array( 'DISP', $cat_med, 'Dispenser Technician' ),
		array( 'DENTAL', $cat_med, 'Dental Technician' ),
		array( 'RADIO', $cat_med, 'Radiographer' ),
		$n( 'N-SURVEY', 'Advance Survey (Civil)', '6 Mahine' ),
		$n( 'N-FASHION', 'Fashion Design & Dress Making', '6 Mahine' ),
		$n( 'N-ELEC', 'General Electrician', '6 Mahine' ),
		$n( 'N-ECOM', 'eCommerce', '3 Mahine' ),
		$n( 'N-MASON', 'Mason', '6 Mahine' ),
		$n( 'N-JS', 'JavaScript Fullstack (MEAN / MERN)', '3 Mahine' ),
		$n( 'N-WELD', 'Welder (SMAW, GTAW)', '6 Mahine' ),
		$n( 'N-WEB', 'Advance Web Application Development', '3 Mahine' ),
		$n( 'N-PLUMB', 'Plumber / Pipe Fitter', '6 Mahine' ),
		$n( 'N-AVA', 'Amazon Virtual Assistant', '3 Mahine' ),
		$n( 'N-CNC', 'Advanced CNC', '6 Mahine' ),
		$n( 'N-AI', 'Artificial Intelligence (Machine Learning)', '3 Mahine' ),
		$n( 'N-ARC', 'Electric Arc Welding', '6 Mahine' ),
		$n( 'N-DMSEO', 'Digital Marketing & Search Engine Optimization', '3 Mahine' ),
	);
}

/**
 * Create pages and content once.
 */
function pvi_seed() {
	if ( get_option( 'pvi_seeded' ) ) {
		return;
	}

	// Pages (re-use existing ones with the same slug).
	$pages = array(
		'home'          => array( 'Home', '' ),
		'courses'       => array( 'Courses', 'templates/courses.php' ),
		'apply'         => array( 'Apply Online', 'templates/apply.php' ),
		'notifications' => array( 'Notifications', 'templates/notifications.php' ),
		'results'       => array( 'Results', 'templates/results.php' ),
		'contact'       => array( 'Contact', 'templates/contact.php' ),
	);
	$ids   = array();
	foreach ( $pages as $slug => $p ) {
		$existing = get_page_by_path( $slug );
		$id       = $existing ? $existing->ID : wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $p[0],
				'post_name'    => $slug,
				'post_content' => '',
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			$ids[ $slug ] = (int) $id;
			if ( $p[1] ) {
				update_post_meta( $id, '_wp_page_template', $p[1] );
			}
		}
	}
	update_option( 'pvi_pages', $ids );
	if ( ! empty( $ids['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
	}

	// Course categories in their original order.
	$cats = array(
		'Diploma Programs',
		'IT & Computer Courses',
		'Technical & Skill Courses',
		'Evening Coaching Classes (Medical / Health)',
		'NAVTTC Free Courses (PM Youth Programme)',
	);
	$term_ids = array();
	foreach ( $cats as $i => $name ) {
		$t = term_exists( $name, 'pvi_course_cat' );
		if ( ! $t ) {
			$t = wp_insert_term( $name, 'pvi_course_cat' );
		}
		if ( ! is_wp_error( $t ) ) {
			$term_ids[ $name ] = (int) $t['term_id'];
			update_term_meta( $term_ids[ $name ], 'pvi_order', $i + 1 );
		}
	}

	// Courses.
	foreach ( pvi_seed_courses() as $i => $c ) {
		$c  = array_pad( $c, 8, '' );
		$id = wp_insert_post(
			array(
				'post_type'   => 'pvi_course',
				'post_status' => 'publish',
				'post_title'  => $c[2],
				'menu_order'  => $i,
			)
		);
		if ( ! $id || is_wp_error( $id ) ) {
			continue;
		}
		$meta = array(
			'code'        => $c[0],
			'duration'    => $c[3],
			'fee'         => $c[4],
			'eligibility' => $c[5],
			'note'        => $c[6],
			'features'    => $c[7],
			'seats'       => '',
		);
		foreach ( $meta as $k => $v ) {
			update_post_meta( $id, '_pvi_' . $k, $v );
		}
		if ( isset( $term_ids[ $c[1] ] ) ) {
			wp_set_object_terms( $id, $term_ids[ $c[1] ], 'pvi_course_cat' );
		}
	}

	// Notifications.
	$notices = array(
		array( '2026-10-01 09:00:00', 'Admissions Open — Boys & Girls', 'DIT, Matric Tech, Computer Operator, CCA, Digital Marketing, E-Commerce aur tamam skill courses mein admissions jari hain. Online apply karein ya call karein: 0301-7681248.', '1' ),
		array( '2026-09-25 09:00:00', 'Evening Coaching Classes', 'LHV, CNA, CMW, Pharmacy, Lab, OTT, Dispenser, Dental aur Radiographer ki evening coaching classes shuru ho rahi hain.', '0' ),
		array( '2026-05-01 09:00:00', 'Classes Starting 15 May 2026', 'DIT (2 Year), Computer Operator (1 Year), CCA (6 Month), Digital Marketing aur E-Commerce ki classes 15 May 2026 se shuru.', '0' ),
	);
	foreach ( $notices as $n ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'pvi_notice',
				'post_status'  => 'publish',
				'post_title'   => $n[1],
				'post_content' => $n[2],
				'post_date'    => $n[0],
			)
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_pvi_important', $n[3] );
		}
	}

	// Site basics.
	update_option( 'blogname', 'Prime Vocational Institute' );
	update_option( 'blogdescription', 'Upgrade Your Skills, Build Your Future' );
	update_option( 'timezone_string', 'Asia/Karachi' );
	update_option( 'date_format', 'j M Y' );
	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	update_option( 'default_comment_status', 'closed' );

	// Remove WordPress's sample content on a fresh install.
	$hello = get_post( 1 );
	if ( $hello && 'post' === $hello->post_type && 'Hello world!' === $hello->post_title ) {
		wp_delete_post( 1, true );
	}
	$sample = get_page_by_path( 'sample-page' );
	if ( $sample ) {
		wp_delete_post( $sample->ID, true );
	}

	update_option( 'pvi_seeded', 1 );
}
