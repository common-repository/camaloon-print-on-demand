<?php
/**
 * Camaloon faqs
 *
 * @package Camaloon
 */

?>
<?php $client = new Camaloon_Client( $GLOBALS['camaloon_url'] . '/' ); ?>
<?php $faqs = $client->get( '/api/print_on_demand/faqs', array( 'target' => 'woocommerce' ) ); ?>
<?php
	$allowed_html = array(
		'a'  => array(
			'href'  => array(),
			'title' => array(),
		),
		'ol' => array(),
		'li' => array(),
		'p'  => array(),
	);
	?>
<div class="container-fluid faq">
	<div class="grid grid-cols-2 gap-4">
		<div class="col-span-2 md:col-span-1 mb-5">
			<h3 class="text-lg font-semibold"><?php _e( 'Frequently Asked Questions', 'camaloon' ); ?></h3>
			<br>
			<p style="min-height: 40px;"><?php _e( 'Getting started made easy. Read the FAQs to jumpstart your business.', 'camaloon' ); ?></p>
			<br>
			<div class="bg-white border-gray-200 border-2 rounded p-8">
			<?php if ( ! empty( $faqs ) ) : ?>
				<?php foreach ( $faqs as &$faq ) : ?>
					<div class="border-b py-5">
						<h2 class="text-lg text-gray-800 font-semibold mb-2"><?php echo esc_html( $faq[ 'question_' . camaloon_get_locale_code_by_selected_language() ] ); ?></h2>
						<p><?php echo wp_kses( $faq[ 'answer_' . camaloon_get_locale_code_by_selected_language() ], $allowed_html ); ?></p>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
			</div>
		</div>
		<div class="col-span-2 md:col-span-1 mb-5">
			<h3 class="text-lg font-semibold"><?php _e( 'Need support? Contact us.', 'camaloon' ); ?></h3>
			<br>
			<p style="min-height: 40px;"><?php _e( 'Copy the box content below and add it to your support message Note: this status report may not include an error log. Contact your hosting provider if you need help with acquiring error logs.', 'camaloon' ); ?></p>
			<br>
			<div class="bg-white p-8 flex border-gray-200 border-2 rounded flex flex-col">
				<div>
				<div id="checklistClipboard" class="bg-gray-100 border-gray-200 border-2 rounded p-2 mx-auto">
				<p>##### <?php _e( 'Camaloon Checklist', 'camaloon' ); ?> #####</p>
						<?php $items = camaloon_get_checklist_items(); ?>
						<?php foreach ( $items as $item ) : ?>
								<p>
									<?php echo esc_html( $item['name'] ); ?> =>
									<?php $item['status'] = $item['method'](); ?>
									<?php
									if ( STATUS_OK === $item['status'] ) {
										echo esc_html__( 'OK', 'camaloon' );
									} elseif ( STATUS_NOT_CONNECTED === $item['status'] ) {
										echo esc_html__( 'NOT CONNECTED', 'camaloon' );
									} else {
										echo esc_html__( 'FAIL', 'camaloon' );
									}
									?>
						</p>
						<?php endforeach; ?>
					</div>
				</div>
				<div>
					<br>
					<p class="text-gray-500 text-xs"><?php _e( 'Note: this status report may not include an error log. Contact your hosting provider if you need help with acquiring error logs.','camaloon' ); ?></p> 
					<br>
					<button class="px-3 py-2 bg-white text-blue-500 border-blue-500 border rounded" onclick="copyChecklistClipboard()"><?php _e( 'Copy', 'camaloon' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	if( document.cookie.indexOf('pluginInstalledAlert=') != -1 ){
		document.cookie = "pluginInstalledAlert" + '=; Max-Age=0'
}
</script>
