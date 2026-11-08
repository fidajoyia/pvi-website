<?php
/**
 * wp-admin > Institute Settings: name, contact details, home page text, NAVTTC info, posters.
 *
 * @package pvi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field definitions: key => [ label, type, help ].
 *
 * @return array
 */
function pvi_setting_fields() {
	return array(
		'Institute'                => array(
			'name'            => array( 'Institute name', 'text', '' ),
			'city'            => array( 'City', 'text', '' ),
			'tagline'         => array( 'Tagline', 'text', 'Comma ke baad wala hissa home page par golden rang mein dikhta hai.' ),
			'sub_tagline'     => array( 'Sub tagline', 'text', '' ),
			'admission_badge' => array( 'Admission badge', 'text', 'Top bar aur home page par dikhta hai.' ),
			'affiliation'     => array( 'Affiliation line', 'text', '' ),
			'slogan'          => array( 'Slogan (footer)', 'text', '' ),
			'established'     => array( 'Established (year)', 'text', 'Khali ho to nahi dikhta.' ),
		),
		'Contact'                  => array(
			'address'      => array( 'Address', 'text', '' ),
			'phone'        => array( 'Phone 1', 'text', '' ),
			'phone2'       => array( 'Phone 2', 'text', '' ),
			'email'        => array( 'Public email', 'email', 'Khali ho to nahi dikhta.' ),
			'timing'       => array( 'Timing', 'text', 'Maslan "Mon - Sat, 8:00 AM - 4:00 PM". Khali ho to nahi dikhta.' ),
			'map_query'    => array( 'Google Maps search', 'text', 'Jo Google Maps mein search karte hain (institute ka naam + shehar).' ),
			'notify_email' => array( 'Send new applications to', 'email', 'Nayi application aane par is email par khabar jati hai. Khali ho to site ke admin email par.' ),
		),
		'Home page'                => array(
			'about'             => array( 'College ke baare mein', 'textarea', '' ),
			'principal_message' => array( 'Principal ka paigham', 'textarea', '' ),
			'stats'             => array( 'Stats boxes', 'textarea', 'Har line: Value|Label — maslan: Registered|NAVTTC' ),
			'facilities'        => array( 'Facilities', 'textarea', 'Har line par ek facility.' ),
			'posters'           => array( 'Posters', 'textarea', 'Har line: tasveer|title. Nayi tasveer Media > Add New mein upload karein aur uska URL yahan likhein.' ),
		),
		'NAVTTC (PM Youth Programme)' => array(
			'navttc_eligibility' => array( 'Eligibility', 'text', '' ),
			'navttc_documents'   => array( 'Documents to bring', 'text', '' ),
			'navttc_apply_url'   => array( 'Online apply link', 'url', '' ),
		),
	);
}

add_action(
	'admin_menu',
	function () {
		add_menu_page( 'Institute Settings', 'Institute Settings', 'manage_options', 'pvi-settings', 'pvi_render_settings', 'dashicons-building', 24 );
	}
);

add_action(
	'admin_init',
	function () {
		register_setting(
			'pvi_settings_group',
			'pvi_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'pvi_sanitize_settings',
			)
		);
	}
);

/**
 * Sanitize settings.
 *
 * @param mixed $input Raw input.
 * @return array
 */
function pvi_sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	$out   = array();
	foreach ( pvi_setting_fields() as $fields ) {
		foreach ( $fields as $key => $f ) {
			$val = isset( $input[ $key ] ) ? (string) $input[ $key ] : '';
			switch ( $f[1] ) {
				case 'textarea':
					$out[ $key ] = sanitize_textarea_field( $val );
					break;
				case 'email':
					$out[ $key ] = sanitize_email( $val );
					break;
				case 'url':
					$out[ $key ] = esc_url_raw( $val );
					break;
				default:
					$out[ $key ] = sanitize_text_field( $val );
			}
		}
	}
	pvi_purge_cache();
	return $out;
}

/**
 * Settings page.
 */
function pvi_render_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1>Institute Settings</h1>
		<p>Yeh maloomat website ke har page par istemal hoti hai.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'pvi_settings_group' ); ?>
			<?php foreach ( pvi_setting_fields() as $section => $fields ) : ?>
				<h2 class="title"><?php echo esc_html( $section ); ?></h2>
				<table class="form-table" role="presentation">
					<?php foreach ( $fields as $key => $f ) : ?>
						<tr>
							<th scope="row"><label for="pvi_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $f[0] ); ?></label></th>
							<td>
								<?php if ( 'textarea' === $f[1] ) : ?>
									<textarea class="large-text" rows="<?php echo in_array( $key, array( 'stats', 'facilities', 'posters' ), true ) ? 6 : 4; ?>" id="pvi_<?php echo esc_attr( $key ); ?>" name="pvi_settings[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( pvi_opt( $key ) ); ?></textarea>
								<?php else : ?>
									<input class="regular-text" type="<?php echo esc_attr( $f[1] ); ?>" id="pvi_<?php echo esc_attr( $key ); ?>" name="pvi_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( pvi_opt( $key ) ); ?>">
								<?php endif; ?>
								<?php if ( $f[2] ) : ?>
									<p class="description"><?php echo esc_html( $f[2] ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
