<?php
/**
 * Shared helpers: institute settings, page links, course/notice queries.
 *
 * @package pvi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default institute settings (editable in wp-admin > Institute Settings).
 */
function pvi_defaults() {
	return array(
		'name'               => 'Prime Vocational Institute',
		'city'               => 'Haroonabad',
		'tagline'            => 'Upgrade Your Skills, Build Your Future',
		'sub_tagline'        => 'Learn from experts and grow with confidence',
		'affiliation'        => 'Registered with NAVTTC (Govt. of Pakistan) & PSDA (Govt. of Punjab) · Affiliated with PBTE Lahore',
		'admission_badge'    => 'Admission Open — Boys & Girls',
		'established'        => '',
		'address'            => 'Main Road, Model Town, Haroonabad',
		'phone'              => '0301-7681248',
		'phone2'             => '0333-6314100',
		'slogan'             => 'Skill Today, Success Tomorrow',
		'email'              => '',
		'timing'             => '',
		'map_query'          => 'Model Town, Haroonabad',
		'about'              => 'Prime Vocational Institute, Haroonabad NAVTTC (Govt. of Pakistan) aur PSDA (Govt. of Punjab) se registered hai aur PBTE Lahore se affiliated hai. Hum IT, technical, skills aur medical/health ke shobon mein diploma aur certificate courses karwate hain, taake students hunar seekh kar rozgaar ya apna kaam shuru kar sakein.',
		'principal_message'  => 'Hamare institute mein khush aamdeed. Hamara maqsad har student ko practical hunar dena hai jo use mulk aur bairon-e-mulk rozgaar ke qabil banaye.',
		'stats'              => "Registered|NAVTTC\nRegistered|PSDA Punjab\nAffiliated|PBTE Lahore\nPartner|NAVTTC Institute",
		'facilities'         => "Computer Labs\nElectrical Workshop\nPlumbing Workshop\nStitching / Fashion Lab\nMedical Training Lab\nEvening Classes\nQualified Teachers\nJob Guidance",
		'navttc_eligibility' => 'Primary / Middle / Matric / Intermediate · Umar 18-45 saal · Male & Female',
		'navttc_apply_url'   => 'https://nsis.navttc.gov.pk',
		'navttc_documents'   => 'Shanakhti card (CNIC), 4 passport size tasaveer, taleemi asnaad aur result card ki copy',
		'posters'            => "admission-open.jpg|Admission Open — Boys & Girls\ndit-admission.jpg|DIT — Diploma in Information Technology\nnavttc-free-courses.jpg|NAVTTC & PM Youth Programme — Free Courses\nnavttc-partner.jpg|NAVTTC Partner Institute",
		'notify_email'       => '',
	);
}

/**
 * Read one institute setting (falls back to the default).
 *
 * @param string $key Setting key.
 * @return string
 */
function pvi_opt( $key ) {
	$opts = get_option( 'pvi_settings', array() );
	if ( is_array( $opts ) && array_key_exists( $key, $opts ) ) {
		return (string) $opts[ $key ];
	}
	$defaults = pvi_defaults();
	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Non-empty lines of a textarea setting.
 *
 * @param string $key Setting key.
 * @return string[]
 */
function pvi_lines( $key ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', pvi_opt( $key ) ) ) ) );
}

/**
 * "a|b" lines of a textarea setting as [a, b] pairs.
 *
 * @param string $key Setting key.
 * @return array[]
 */
function pvi_pairs( $key ) {
	$out = array();
	foreach ( pvi_lines( $key ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		$out[] = array( $parts[0], isset( $parts[1] ) ? $parts[1] : '' );
	}
	return $out;
}

/**
 * Theme image URL, or the URL itself when a full URL is given.
 *
 * @param string $src File name in assets/images or a full URL.
 * @return string
 */
function pvi_image_url( $src ) {
	if ( preg_match( '#^https?://#i', $src ) ) {
		return $src;
	}
	return PVI_URI . '/assets/images/' . ltrim( $src, '/' );
}

/**
 * ID of one of the theme's pages (created on activation).
 *
 * @param string $key home|courses|apply|notifications|results|contact.
 * @return int
 */
function pvi_page_id( $key ) {
	$pages = get_option( 'pvi_pages', array() );
	return isset( $pages[ $key ] ) ? (int) $pages[ $key ] : 0;
}

/**
 * URL of one of the theme's pages.
 *
 * @param string $key  Page key.
 * @param array  $args Optional query args.
 * @return string
 */
function pvi_page_url( $key, $args = array() ) {
	$id  = pvi_page_id( $key );
	$url = ( 'home' === $key ) ? home_url( '/' ) : ( ( $id && get_post_status( $id ) ) ? get_permalink( $id ) : home_url( '/' . $key . '/' ) );
	return $args ? add_query_arg( $args, $url ) : $url;
}

/**
 * Both phone numbers as tel: links.
 *
 * @param string $sep Separator.
 * @return string Safe HTML.
 */
function pvi_phone_links( $sep = ' / ' ) {
	$links = array();
	foreach ( array( pvi_opt( 'phone' ), pvi_opt( 'phone2' ) ) as $n ) {
		if ( '' !== trim( $n ) ) {
			$links[] = '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $n ) ) . '">' . esc_html( $n ) . '</a>';
		}
	}
	return implode( esc_html( $sep ), $links );
}

/**
 * Grade from percentage (same scale as the original site).
 *
 * @param float $p Percentage.
 * @return string
 */
function pvi_grade( $p ) {
	if ( $p >= 85 ) {
		return 'A+';
	}
	if ( $p >= 80 ) {
		return 'A';
	}
	if ( $p >= 70 ) {
		return 'B';
	}
	if ( $p >= 60 ) {
		return 'C';
	}
	if ( $p >= 50 ) {
		return 'D';
	}
	if ( $p >= 40 ) {
		return 'E';
	}
	return 'F';
}

/**
 * Course categories in display order (term meta "pvi_order").
 *
 * @return WP_Term[]
 */
function pvi_course_terms() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'pvi_course_cat',
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	usort(
		$terms,
		function ( $a, $b ) {
			$oa = (int) get_term_meta( $a->term_id, 'pvi_order', true );
			$ob = (int) get_term_meta( $b->term_id, 'pvi_order', true );
			$oa = $oa ? $oa : 999;
			$ob = $ob ? $ob : 999;
			return ( $oa === $ob ) ? strcasecmp( $a->name, $b->name ) : ( $oa < $ob ? -1 : 1 );
		}
	);
	return $terms;
}

/**
 * Course data as a plain array.
 *
 * @param WP_Post $post Course post.
 * @return array
 */
function pvi_course( $post ) {
	$get = function ( $k ) use ( $post ) {
		return (string) get_post_meta( $post->ID, '_pvi_' . $k, true );
	};
	return array(
		'id'          => $post->ID,
		'name'        => get_the_title( $post ),
		'code'        => $get( 'code' ),
		'duration'    => $get( 'duration' ),
		'note'        => $get( 'note' ),
		'seats'       => $get( 'seats' ),
		'fee'         => $get( 'fee' ),
		'eligibility' => $get( 'eligibility' ),
		'features'    => array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $get( 'features' ) ) ) ) ),
	);
}

/**
 * All published courses grouped by category, in display order.
 *
 * @return array[] Each: [ 'name' => string, 'navttc' => bool, 'courses' => array[] ].
 */
function pvi_courses_grouped() {
	$posts  = get_posts(
		array(
			'post_type'   => 'pvi_course',
			'post_status' => 'publish',
			'numberposts' => -1,
			'orderby'     => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
	$groups = array();
	foreach ( pvi_course_terms() as $term ) {
		$groups[ $term->term_id ] = array(
			'name'    => $term->name,
			'navttc'  => 0 === stripos( $term->name, 'NAVTTC' ),
			'courses' => array(),
		);
	}
	$groups['other'] = array(
		'name'    => 'Other Courses',
		'navttc'  => false,
		'courses' => array(),
	);
	foreach ( $posts as $p ) {
		$terms = get_the_terms( $p, 'pvi_course_cat' );
		$key   = ( $terms && ! is_wp_error( $terms ) && isset( $groups[ $terms[0]->term_id ] ) ) ? $terms[0]->term_id : 'other';
		$groups[ $key ]['courses'][] = pvi_course( $p );
	}
	return array_values(
		array_filter(
			$groups,
			function ( $g ) {
				return ! empty( $g['courses'] );
			}
		)
	);
}

/**
 * Course codes => names (for selects).
 *
 * @return array
 */
function pvi_course_options() {
	$out = array();
	foreach ( pvi_courses_grouped() as $g ) {
		foreach ( $g['courses'] as $c ) {
			if ( '' !== $c['code'] ) {
				$out[ $c['code'] ] = $c['name'];
			}
		}
	}
	return $out;
}

/**
 * Published notifications, newest first.
 *
 * @param int $limit Number to return (-1 = all).
 * @return WP_Post[]
 */
function pvi_notices( $limit = -1 ) {
	return get_posts(
		array(
			'post_type'   => 'pvi_notice',
			'post_status' => 'publish',
			'numberposts' => $limit,
			'orderby'     => 'date',
			'order'       => 'DESC',
		)
	);
}

/**
 * Whether a notification is marked important.
 *
 * @param int $id Post ID.
 * @return bool
 */
function pvi_is_important( $id ) {
	return '1' === (string) get_post_meta( $id, '_pvi_important', true );
}

/**
 * Number of important notifications (menu badge).
 *
 * @return int
 */
function pvi_important_count() {
	$q = new WP_Query(
		array(
			'post_type'      => 'pvi_notice',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_pvi_important', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	return (int) $q->found_posts;
}

/**
 * Render one notification block.
 *
 * @param WP_Post $n        Notice.
 * @param bool    $with_icon Prefix date with calendar icon.
 */
function pvi_render_notice( $n, $with_icon = false ) {
	$important = pvi_is_important( $n->ID );
	?>
	<div class="notice<?php echo $important ? ' important' : ''; ?>">
		<span class="date"><?php echo $with_icon ? '📅 ' : ''; ?><?php echo esc_html( get_the_date( 'j M Y', $n ) ); ?></span>
		<h3><?php echo esc_html( get_the_title( $n ) ); ?><?php echo $important ? '<span class="tag-new">Important</span>' : ''; ?></h3>
		<div class="entry-content"><?php echo wp_kses_post( wpautop( $n->post_content ) ); ?></div>
	</div>
	<?php
}

/**
 * Header/footer navigation items (Main menu if assigned, else the theme pages).
 *
 * @return array[]
 */
function pvi_nav_items() {
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations['primary'] ) ) {
		$menu_items = wp_get_nav_menu_items( $locations['primary'] );
		if ( $menu_items ) {
			$items = array();
			foreach ( $menu_items as $mi ) {
				if ( (int) $mi->menu_item_parent ) {
					continue;
				}
				$items[] = array(
					'url'   => $mi->url,
					'label' => $mi->title,
					'id'    => ( 'page' === $mi->object ) ? (int) $mi->object_id : 0,
				);
			}
			return $items;
		}
	}
	$items = array();
	$pages = array(
		'home'          => 'Home',
		'courses'       => 'Courses',
		'apply'         => 'Apply',
		'notifications' => 'Notifications',
		'results'       => 'Results',
		'contact'       => 'Contact',
	);
	foreach ( $pages as $key => $label ) {
		$items[] = array(
			'url'   => pvi_page_url( $key ),
			'label' => $label,
			'id'    => pvi_page_id( $key ),
		);
	}
	return $items;
}

/**
 * Whether a nav item points at the current page.
 *
 * @param array $item Nav item.
 * @return bool
 */
function pvi_nav_is_current( $item ) {
	if ( $item['id'] ) {
		return is_page( $item['id'] ) || ( is_front_page() && (int) get_option( 'page_on_front' ) === $item['id'] );
	}
	return is_front_page() && untrailingslashit( $item['url'] ) === untrailingslashit( home_url( '/' ) );
}

/**
 * Tell LiteSpeed Cache (Hostinger) to refresh pages after content changes.
 */
function pvi_purge_cache() {
	do_action( 'litespeed_purge_all' );
}
