<?php
/**
 * Custom post types, course categories, capabilities and the Institute Staff role.
 *
 * @package pvi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Capabilities that control all institute content.
 *
 * @return string[]
 */
function pvi_capabilities() {
	return array(
		'edit_pvi_items',
		'edit_others_pvi_items',
		'edit_published_pvi_items',
		'edit_private_pvi_items',
		'publish_pvi_items',
		'read_private_pvi_items',
		'delete_pvi_items',
		'delete_others_pvi_items',
		'delete_published_pvi_items',
		'delete_private_pvi_items',
	);
}

/**
 * Labels helper.
 *
 * @param string $one  Singular.
 * @param string $many Plural.
 * @return array
 */
function pvi_labels( $one, $many ) {
	return array(
		'name'               => $many,
		'singular_name'      => $one,
		'menu_name'          => $many,
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New ' . $one,
		'edit_item'          => 'Edit ' . $one,
		'new_item'           => 'New ' . $one,
		'view_item'          => 'View ' . $one,
		'search_items'       => 'Search ' . $many,
		'not_found'          => 'No ' . strtolower( $many ) . ' found',
		'not_found_in_trash' => 'No ' . strtolower( $many ) . ' in Trash',
		'all_items'          => 'All ' . $many,
	);
}

add_action(
	'init',
	function () {
		$common = array(
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'capability_type'     => array( 'pvi_item', 'pvi_items' ),
			'map_meta_cap'        => true,
		);

		register_post_type(
			'pvi_application',
			array_merge(
				$common,
				array(
					'labels'        => pvi_labels( 'Application', 'Applications' ),
					'description'   => 'Online admission applications submitted from the website.',
					'menu_icon'     => 'dashicons-id-alt',
					'menu_position'     => 25,
					'supports'          => false,
					'show_in_admin_bar' => false,
				)
			)
		);

		register_post_type(
			'pvi_notice',
			array_merge(
				$common,
				array(
					'labels'        => pvi_labels( 'Notification', 'Notifications' ),
					'menu_icon'     => 'dashicons-megaphone',
					'menu_position' => 26,
					'supports'      => array( 'title', 'editor' ),
				)
			)
		);

		register_post_type(
			'pvi_result',
			array_merge(
				$common,
				array(
					'labels'        => pvi_labels( 'Result', 'Results' ),
					'menu_icon'     => 'dashicons-chart-bar',
					'menu_position' => 27,
					'supports'      => false,
				)
			)
		);

		register_post_type(
			'pvi_course',
			array_merge(
				$common,
				array(
					'labels'        => pvi_labels( 'Course', 'Courses' ),
					'menu_icon'     => 'dashicons-welcome-learn-more',
					'menu_position' => 28,
					'supports'      => array( 'title', 'page-attributes' ),
				)
			)
		);

		register_taxonomy(
			'pvi_course_cat',
			'pvi_course',
			array(
				'labels'            => array(
					'name'          => 'Course Categories',
					'singular_name' => 'Course Category',
					'menu_name'     => 'Categories',
					'add_new_item'  => 'Add New Category',
					'edit_item'     => 'Edit Category',
					'all_items'     => 'All Categories',
				),
				'public'            => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => false,
				'show_in_nav_menus' => false,
				'hierarchical'      => true,
				'rewrite'           => false,
				'query_var'         => false,
				'capabilities'      => array(
					'manage_terms' => 'edit_pvi_items',
					'edit_terms'   => 'edit_pvi_items',
					'delete_terms' => 'edit_pvi_items',
					'assign_terms' => 'edit_pvi_items',
				),
			)
		);
	}
);

/*
 * Applications only come from the website form, so hide "Add New" for them.
 * (Not done with 'create_posts' => 'do_not_allow' because a WordPress core bug then
 * locks non-admin roles out of the list screen.)
 */
add_action(
	'admin_menu',
	function () {
		remove_submenu_page( 'edit.php?post_type=pvi_application', 'post-new.php?post_type=pvi_application' );
	},
	999
);
add_action(
	'load-post-new.php',
	function () {
		if ( isset( $_GET['post_type'] ) && 'pvi_application' === $_GET['post_type'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			wp_safe_redirect( admin_url( 'edit.php?post_type=pvi_application' ) );
			exit;
		}
	}
);
add_action(
	'admin_head',
	function () {
		echo '<style>.post-type-pvi_application .page-title-action{display:none}</style>';
	}
);

/**
 * Give Administrators the institute capabilities and create the "Institute Staff" role
 * (manages applications, notifications, results and courses — nothing else).
 */
function pvi_setup_roles() {
	$caps  = pvi_capabilities();
	$admin = get_role( 'administrator' );
	if ( $admin ) {
		foreach ( $caps as $cap ) {
			$admin->add_cap( $cap );
		}
	}
	$staff = array(
		'read'         => true,
		'upload_files' => true,
	);
	foreach ( $caps as $cap ) {
		$staff[ $cap ] = true;
	}
	remove_role( 'pvi_staff' );
	add_role( 'pvi_staff', 'Institute Staff', $staff );
	update_option( 'pvi_roles_version', PVI_VERSION );
}

add_action(
	'init',
	function () {
		if ( get_option( 'pvi_roles_version' ) !== PVI_VERSION ) {
			pvi_setup_roles();
		}
	},
	5
);

// Display order field for course categories.
add_action(
	'pvi_course_cat_add_form_fields',
	function () {
		?>
		<div class="form-field">
			<label for="pvi_order">Display order</label>
			<input type="number" name="pvi_order" id="pvi_order" value="">
			<p>Courses page par categories is number ki tarteeb se dikhti hain (1 sab se pehle).</p>
		</div>
		<?php
	}
);
add_action(
	'pvi_course_cat_edit_form_fields',
	function ( $term ) {
		?>
		<tr class="form-field">
			<th scope="row"><label for="pvi_order">Display order</label></th>
			<td>
				<input type="number" name="pvi_order" id="pvi_order" value="<?php echo esc_attr( get_term_meta( $term->term_id, 'pvi_order', true ) ); ?>">
				<p class="description">Courses page par categories is number ki tarteeb se dikhti hain (1 sab se pehle).</p>
			</td>
		</tr>
		<?php
	}
);
$pvi_save_term_order = function ( $term_id ) {
	// Core verifies the term form nonce before these hooks fire.
	if ( isset( $_POST['pvi_order'] ) && current_user_can( 'edit_pvi_items' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		update_term_meta( $term_id, 'pvi_order', absint( $_POST['pvi_order'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	}
	pvi_purge_cache();
};
add_action( 'created_pvi_course_cat', $pvi_save_term_order );
add_action( 'edited_pvi_course_cat', $pvi_save_term_order );

// Refresh cached pages whenever institute content changes.
add_action(
	'save_post',
	function ( $post_id, $post ) {
		if ( in_array( $post->post_type, array( 'pvi_notice', 'pvi_result', 'pvi_course' ), true ) && ! wp_is_post_revision( $post_id ) ) {
			pvi_purge_cache();
		}
	},
	20,
	2
);
add_action(
	'trashed_post',
	function ( $post_id ) {
		if ( in_array( get_post_type( $post_id ), array( 'pvi_notice', 'pvi_result', 'pvi_course' ), true ) ) {
			pvi_purge_cache();
		}
	}
);
