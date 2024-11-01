<?php

if (!function_exists('wj_footers')) {
	function wj_footers() {
		global $wj_footer,$wj_footers;

		$bail_out = ( ( defined( 'WP_ADMIN' ) && WP_ADMIN == true ) || ( strpos( $_SERVER[ 'PHP_SELF' ], 'wp-admin' ) !== false ) );
		if ( $bail_out ) return $footer;

		//Please contact us if you wish to remove the WebJunk logo in the footer
		if (!$wj_footer) {
			echo '<center style="margin-top:0px;font-size:x-small">';
			foreach ($wj_footers as $foot) {
			echo ', <a href="'.$foot[0].'">'.$foot[1].'</a>';
			}
			echo '</center>';
			$wj_footer=true;
		}

	}
}
?>