<?php
/**
 * Camaloon connect
 *
 * @package Camaloon
 */

?>
<?php $status_woocommerce = camaloon_is_woocommerce_ready(); ?>
<?php $dir = esc_url( __DIR__ . '/camaloon-home.php' ); ?>

<?php if ( ! $status_woocommerce ) : ?>
	<div class="update-message notice notice-error text-red-500 notice-alt mx-0 mb-5">
		<div class="flex justify-between">
			<p><?php _e( 'The WooCommerce plugin is missing, we won’t be able to connect your Camaloon account.', 'camaloon' ); ?></p>
		</div>
	</div>
<?php else : ?>
	<?php if ( camaloon_check_list_failed_without_webhooks() || ( camaloon_is_store_installed() && camaloon_is_webhook_existed() !== STATUS_OK ) ) : ?>
		<div class="update-message notice notice-error text-red-500 notice-alt mx-0 mb-5">
			<div class="flex justify-between">
				<p><?php _e( 'There are errors with your store setup that may cause the Camaloon integration to not work as intended!', 'camaloon' ); ?></p>
				<a href=esc_attr("?page=/camaloon-print-on-demand/includes/templates/camaloon-home.php&tab=status") class="p-2 underline w-2/12 text-right"><?php _e( 'Check status', 'camaloon' ); ?></a>
			</div>
		</div>
	<?php endif; ?>	
<?php endif; ?>	
<?php $url_link = get_site_url(); ?>
<?php $url_link = preg_replace( '#^http(s)?://#', '', $url_link ); ?>
<?php if ( camaloon_is_store_installed() && $status_woocommerce ) : ?>
	<?php if ( isset( $_COOKIE['pluginInstalledAlert'] ) ) : ?>
		<div class="update-message notice notice-success text-green-500 notice-alt mx-0 mb-5">
			<div class="flex justify-between">
				<p><?php _e( 'Your Camaloon plugin has been successfully installed!', 'camaloon' ); ?></p>
			</div>
		</div>
	<?php endif; ?>
	<div class="bg-white text-center mb-4 py-10 border-gray-200 border-2 rounded px-8">
		<p class="text-base"><?php _e( 'Start selling! Create your first product!', 'camaloon' ); ?></p>
		<div class="flex justify-center items-center">
			<a href=<?php echo esc_url( $GLOBALS['camaloon_url'] ); ?>/print_on_demand/woo_commerce/bridge?domain=<?php echo esc_attr( $url_link ); ?> class="font-medium hover:bg-green-600 hover:border-green-600 text-white hover:text-white focus:text-white hover:no-underline focus:no-underline mt-6 whitespace-normal no-underline px-4 py-3 text-sm md:text-base leading-none rounded border-green-500 bg-green-500 inline-block text-center cursor-pointer" target="_blank"><?php _e( 'Go to Camaloon store', 'camaloon' ); ?></a>
			<form action=<?php echo "?page=".$dir."&tab=disconnect" ?> method="post" class="ml-5 mt-6">
					<input type="submit" name="someAction" value=<?php _e( 'Disconnect', 'camaloon' ); ?> class="font-medium bg-white text-blue-500 hover:text-blue-500 border-2 hover:no-underline px-4 py-2 border-blue-500 text-sm md:text-base rounded cursor-pointer" />
			</form>
		</div>
	</div>
	<div class="grid grid-cols-2 gap-4 items-center">
		<div class="h-full py-12 bg-white col-span-2 md:col-span-1 border-gray-200 border-2 rounded px-8 text-center">
			<p class="text-lg"><?php _e( 'Something wrong with your store?', 'camaloon' ); ?></p>
			<a href=<?php echo "?page=".$dir."&tab=status" ?> class="w-full font-medium text-blue-500 hover:text-blue-500 border-2 hover:no-underline focus:no-underline mt-6 whitespace-normal no-underline px-4 py-3 text-sm md:text-base leading-none rounded border-blue-500 inline-block text-center cursor-pointer"><?php _e( 'Check store status', 'camaloon' ); ?></a>
		</div>
		<div class="h-full py-12 bg-white col-span-2 md:col-span-1 border-gray-200 border-2 rounded px-8 text-center">
			<p class="text-lg"><?php _e( 'Need help with your WooCommerce setup?', 'camaloon' ); ?></p>
			<a href=<?php echo "?page=".$dir."&tab=faq" ?> class="w-full font-medium text-blue-500 hover:text-blue-500 border-2 hover:no-underline focus:no-underline mt-6 whitespace-normal no-underline px-4 py-3 text-sm md:text-base leading-none rounded border-blue-500 inline-block text-center cursor-pointer"><?php _e( 'Review our FAQs', 'camaloon' ); ?></a>
		</div>
	</div>
<?php else : ?>
	<div class="bg-white text-center py-8 border-gray-200 border-2 rounded px-8">
		<img src="<?php echo _e( camaloon_get_asset_url() . '/images/camaloon-homepage.svg' ); ?>" alt="Camaloon logo" class="border-none m-auto pb-6">
		<p class="text-base"><?php _e( 'Connect your WooCommerce to Camaloon!', 'camaloon' ); ?></p>
		<?php if ($status_woocommerce): ?>
      <a href=<?php echo esc_url( $GLOBALS['camaloon_url'] ); ?>/print_on_demand/woo_commerce/bridge?domain=<?php echo esc_attr( $url_link ); ?> class="font-medium  hover:no-underline focus:no-underline mt-6 whitespace-normal no-underline px-4 py-3 text-sm md:text-base leading-none rounded inline-block text-center cursor-pointer bg-green-500 border-green-500 hover:bg-green-600 hover:border-green-600 text-white hover:text-white focus:text-white" onClick="<?php echo esc_js( 'document.cookie = "pluginInstalledAlert=true"' ); ?>"><?php esc_attr_e( 'Connect your store', 'camaloon' ); ?></a>
    <?php else : ?>
      <a href="" class="font-medium  hover:no-underline focus:no-underline mt-6 whitespace-normal no-underline px-4 py-3 text-sm md:text-base leading-none rounded inline-block text-center cursor-not-allowed bg-gray-200 text-gray-400 hover:text-gray-400"><?php esc_attr_e( 'Connect your store', 'camaloon' ); ?></a>
    <?php endif; ?>
	</div>
<?php endif; ?>
