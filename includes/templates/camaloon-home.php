<?php
/**
 * Camaloon home page
 *
 * @package Camaloon
 */

?>
<?php if ( ! isset( $_GET['tab'] ) ) : ?>
	<?php $tab = "connect"; ?>
<?php else : ?>
	<?php $tab = esc_attr( camaloon_pod_tabs( sanitize_text_field( $_GET['tab'] ) ) ); ?>
<?php endif; ?>

<?php $client = new Camaloon_Client( $GLOBALS['camaloon_url'] . '/' ); ?>
<?php camaloon_mark_as_inactive_if_needed( $client ); ?>
<?php $dir = esc_url( __DIR__ . '/camaloon-home.php' ); ?>


<div class="lg:mx-24 mx-4">
	<div class="h-20 p-5 bg-white my-8 border-gray-200 border-2 rounded px-8">
		<ul class="navibar-menus flex items-center list-none h-16">
			<li class="mb-4">
				<img src="<?php echo esc_url( camaloon_get_asset_url() ) . '/images/camaloon-icon.svg'; ?>" alt="Camaloon logo" class="border-none w-8">
			</li>
			<li class="mb-4">
				<a class="h-16 px-6 w-full flex items-center justify-center cursor-pointer text-gray-800 border-b-4 <?php echo 'connect' === $tab ? esc_attr( 'border-green-400 font-semibold' ) : esc_attr( 'border-white' ); ?>" href=<?php echo '?page=' . $dir ?>><?php _e('Home', 'camaloon'); ?></a>
			</li>
			<li class="mb-4">
				<a class="h-16 px-6 w-full flex items-center justify-center cursor-pointer text-gray-800 border-b-4 <?php echo 'status' === $tab ? esc_attr( 'border-green-400 font-semibold' ) : esc_attr( 'border-white' ); ?>" href=<?php echo '?page=' . $dir . '&tab=status' ?>><?php _e('Status', 'camaloon'); ?></a>
			</li>
			<li class="mb-4">
				<a class="h-16 px-6 w-full flex items-center justify-center cursor-pointer text-gray-800 border-b-4 <?php echo 'faq' === $tab ? esc_attr( 'border-green-400 font-semibold' ) : esc_attr( 'border-white' ); ?>" href=<?php echo '?page=' . $dir . '&tab=faq' ?>><?php _e('Support', 'camaloon'); ?></a>
			</li>
		</ul>
	</div>
	<?php
	if ( 'connect' === $tab ) {
		echo camaloon_render_php( 'connect' );
	} else {
		if ( 'status' === $tab ) {
			echo camaloon_render_php( 'status' );
		} else {
			if ( 'faq' === $tab ) {
				echo camaloon_render_php( 'faq' );
			} else {
				echo camaloon_render_php( 'disconnect' );
			}
		}
	}
	?>
</div>
