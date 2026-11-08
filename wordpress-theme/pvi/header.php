<?php
/**
 * Site header (top bar + navigation).
 *
 * @package pvi
 */

$pvi_name_words = preg_split( '/\s+/', trim( pvi_opt( 'name' ) ), 2 );
$pvi_important  = pvi_important_count();
$pvi_notices_id = pvi_page_id( 'notifications' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header id="site-header">
	<div class="topbar">
		<span>🎓 <?php echo esc_html( pvi_opt( 'admission_badge' ) ); ?></span>
		<span>📞 <?php echo pvi_phone_links(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?></span>
		<?php if ( pvi_opt( 'email' ) ) : ?>
			<span>✉️ <a href="mailto:<?php echo esc_attr( pvi_opt( 'email' ) ); ?>"><?php echo esc_html( pvi_opt( 'email' ) ); ?></a></span>
		<?php endif; ?>
	</div>
	<nav class="navbar" aria-label="Main">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand">
			<img src="<?php echo esc_url( pvi_image_url( 'logo.png' ) ); ?>" alt="" class="logo">
			<span class="brand-text">
				<strong><?php echo esc_html( strtoupper( $pvi_name_words[0] ) ); ?></strong>
				<small><?php echo esc_html( ( isset( $pvi_name_words[1] ) ? $pvi_name_words[1] . ' · ' : '' ) . pvi_opt( 'city' ) ); ?></small>
			</span>
		</a>
		<button class="menu-btn" type="button" aria-label="Menu" aria-expanded="false">☰</button>
		<ul class="nav-links">
			<?php foreach ( pvi_nav_items() as $pvi_item ) : ?>
				<li><a href="<?php echo esc_url( $pvi_item['url'] ); ?>" class="<?php echo pvi_nav_is_current( $pvi_item ) ? 'active' : ''; ?>">
					<?php echo esc_html( $pvi_item['label'] ); ?>
					<?php if ( $pvi_important && $pvi_notices_id && $pvi_item['id'] === $pvi_notices_id ) : ?>
						<span class="badge"><?php echo (int) $pvi_important; ?></span>
					<?php endif; ?>
				</a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
</header>
