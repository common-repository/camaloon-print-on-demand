<?php
/**
 * Camaloon disconnect
 *
 * @package Camaloon
 */

?>
<?php $client = new Camaloon_Client( $GLOBALS['camaloon_url'] . '/' ); ?>
<?php disconnect_camaloon( $client ); ?>

<script>
	window.location.href = '?page=camaloon-print-on-demand%2Fincludes%2Ftemplates%2Fcamaloon-home.php'
</script>
