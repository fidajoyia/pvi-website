<?php
/**
 * Template Name: PVI – Notifications
 *
 * @package pvi
 */

get_header();
$pvi_notices = pvi_notices();
?>
<div class="page-title">
	<h1>Notifications &amp; Announcements</h1>
	<p>College ki taaza tareen khabrein</p>
</div>

<main class="container" style="max-width:850px">
	<section>
		<?php
		if ( $pvi_notices ) {
			foreach ( $pvi_notices as $pvi_n ) {
				pvi_render_notice( $pvi_n, true );
			}
		} else {
			echo '<p>Abhi koi notification nahi hai.</p>';
		}
		?>
	</section>
</main>
<?php
get_footer();
