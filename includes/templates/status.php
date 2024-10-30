<?php
/**
 * Camaloon status
 *
 * @package Camaloon
 */

?>
<h3 class="text-lg font-semibold"><?php _e( 'System status', 'camaloon' ); ?></h3>
<br>
<p class="text-sm font-gray-700"><?php _e( 'Review the status of your connection. Errors in the connection of your store setup may cause the Camaloon integration to not work as intended.', 'camaloon' ); ?></p>
<br>
<div class="bg-white border-gray-200 border-2 rounded lg:px-8 px-2">
	<table class="w-full relative wp-list-table camaloon-status">
		<thead class="border-b-2 border-gray-300 font-semibold">
			<tr>
				<td class="w-1/3"><?php esc_html_e( 'Name', 'camaloon' ); ?></td>
				<td><?php esc_html_e( 'Description', 'camaloon' ); ?></td>
				<td class="w-2/12"><?php esc_html_e( 'Status', 'camaloon' ); ?></td>
			</tr>
		</thead>
		<tbody>
		<?php $items = camaloon_get_checklist_items(); ?>
		<?php foreach ( $items as $item ) : ?>
				<tr>
					<td><?php echo esc_html( $item['name'] ); ?></td>
					<td><?php echo esc_html( $item['description'] ); ?></td>
					<td>
						<?php $item['status'] = $item['method'](); ?>
						<?php
						if ( STATUS_OK === $item['status'] ) {
							echo '<span class="pass">' . esc_html__( 'OK', 'camaloon' ) . '</span>';
						} elseif ( STATUS_WARNING === $item['status'] ) {
							echo '<span class="warning">' . esc_html__( 'WARNING', 'camaloon' ) . '&#42;</span>';
						} elseif ( STATUS_NOT_CONNECTED === $item['status'] ) {
							echo '<span class="fail">' . esc_html__( 'NOT CONNECTED', 'camaloon' ) . '</span>';
						} else {
							echo '<span class="fail">' . esc_html__( 'FAIL', 'camaloon' ) . '</span>';
						}
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot class="border-t-2 border-gray-300 px-8 font-semibold">
			<tr>
				<td><?php esc_html_e( 'Name', 'camaloon' ); ?></td>
				<td><?php esc_html_e( 'Description', 'camaloon' ); ?></td>
				<td><?php esc_html_e( 'Status', 'camaloon' ); ?></td>
			</tr>
		</tfoot>
	</table>
</div>

<script>
	if( document.cookie.indexOf('pluginInstalledAlert=') != -1 ){
		document.cookie = "pluginInstalledAlert" + '=; Max-Age=0'
	}
</script>
