<?php
/**
 * Site footer.
 *
 * @package pvi
 */

?>
<footer id="site-footer">
	<div class="footer-grid">
		<div>
			<img src="<?php echo esc_url( pvi_image_url( 'logo.png' ) ); ?>" alt="" style="width:64px;background:#f1f1f1;border-radius:10px;padding:4px">
			<h3><?php echo esc_html( pvi_opt( 'name' ) . ', ' . pvi_opt( 'city' ) ); ?></h3>
			<p><em><?php echo esc_html( pvi_opt( 'slogan' ) ); ?></em></p>
			<p><?php echo esc_html( pvi_opt( 'affiliation' ) ); ?></p>
			<?php if ( pvi_opt( 'established' ) ) : ?>
				<p>Established <?php echo esc_html( pvi_opt( 'established' ) ); ?></p>
			<?php endif; ?>
		</div>
		<div>
			<h3>Quick Links</h3>
			<?php foreach ( pvi_nav_items() as $pvi_item ) : ?>
				<a href="<?php echo esc_url( $pvi_item['url'] ); ?>"><?php echo esc_html( $pvi_item['label'] ); ?></a>
			<?php endforeach; ?>
		</div>
		<div>
			<h3>Rabta</h3>
			<p>📍 <?php echo esc_html( pvi_opt( 'address' ) ); ?></p>
			<p>📞 <?php echo pvi_phone_links(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?></p>
			<?php if ( pvi_opt( 'email' ) ) : ?>
				<p>✉️ <a href="mailto:<?php echo esc_attr( pvi_opt( 'email' ) ); ?>"><?php echo esc_html( pvi_opt( 'email' ) ); ?></a></p>
			<?php endif; ?>
			<?php if ( pvi_opt( 'timing' ) ) : ?>
				<p>🕗 <?php echo esc_html( pvi_opt( 'timing' ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<p class="copyright">© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( pvi_opt( 'name' ) ); ?>. All rights reserved. · <a href="<?php echo esc_url( admin_url() ); ?>" rel="nofollow">Staff Login</a></p>
</footer>
<?php wp_footer(); ?>
</body>
</html>
