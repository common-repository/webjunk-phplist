<?php
$wj_mail_name = "WebJunk PHPList";
$wj_mail_shortname = "wj_mail";
$wj_mail_options=array();

function wj_mail_add_admin() {

	global $wj_mail_name, $wj_mail_shortname, $wj_mail_options;

	//echo 'mc='.get_magic_quotes_gpc().'/'.get_magic_quotes_runtime();
	
	if ( $_GET['page'] == basename(__FILE__) ) {

		if ( 'update' == $_REQUEST['action'] ) {
			wj_mail_activate();
			foreach ($wj_mail_options as $value) {
				if( isset( $_REQUEST[ $value['id'] ] ) ) {
					update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
				} else { delete_option( $value['id'] );
				}
			}
			header("Location: options-general.php?page=wjphplist_cp.php");
			die;
		}

		if ( 'install' == $_REQUEST['action'] ) {
			wj_mail_activate();
			foreach ($wj_mail_options as $value) {
				if( isset( $_REQUEST[ $value['id'] ] ) ) {
					update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
				} else { delete_option( $value['id'] );
				}
			}
			header("Location: options-general.php?page=wjphplist_cp.php&installed=true");
			die;
		}

		if( 'uninstall' == $_REQUEST['action'] ) {
			wj_mail_uninstall();
			foreach ($wj_mail_options as $value) {
				delete_option( $value['id'] );
				update_option( $value['id'], $value['std'] );
			}
			header("Location: options-general.php?page=wjphplist_cp.php&uninstalled=true");
			die;
		}
	}

	add_options_page($wj_mail_name." Options", "$wj_mail_name", 8, basename(__FILE__), 'wj_mail_admin');

 }


function wj_phplist_form() {
$WJ_PHPLIST_URL = get_option('WJ_PHPLIST_URL1');
?>
<form method="post">
  <label for="WJ_PHPLIST_URL1">URL to PHPList:
   <input type="text" size="60" name="WJ_PHPLIST_URL1" value="<?=$WJ_PHPLIST_URL?>" />
  </label>
<input type="submit" name="submit" value="Update URL" />
</form> <?php
}

function update_wj_phplisturl() {
$ok = false;
if ($_REQUEST['WJ_PHPLIST_URL1']) {
 update_option('WJ_PHPLIST_URL1',$_REQUEST['WJ_PHPLIST_URL1']);
 $ok = true;
}
if ($ok) {
 ?><p>Options Saved</p><?php
}
else {
?>
<p>Failed to save URL</p>
<?php
}
}





function wj_mail_admin() {

	global $wj_mail_name, $wj_mail_shortname, $wj_mail_options, $wpdb;

	if ( $_REQUEST['installed'] ) echo '<div id="message" class="updated fade"><p><strong>'.$wj_mail_name.' installed.</strong></p></div>';
	if ( $_REQUEST['uninstalled'] ) echo '<div id="message" class="updated fade"><p><strong>'.$wj_mail_name.' uninstalled.</strong></p></div>';

	$wj_mail_version=get_option("wj_mail_version");
	?>
<div class="wrap">
<h2 class="wj-left"><b><?php echo $wj_mail_name; ?></b></h2>


<div style="clear:both"></div>

<hr />
<?php if ($wj_mail_version) {

if ($_REQUEST['submit']) {
 update_wj_phplisturl();
}
print wj_phplist_form();
?> <br /> <?php

		wj_mail_cp();
}
	?>
	<br />


<hr />

<p>For more info and support, contact us at <a href="http://webjunk.com/">WebJunk.com</a></p>
<hr />
	<?php
}

function wj_mail_cp() {
	global $wj_mail_content;
	global $wj_mail_menu;
//	global $wj_mail_post;
	
	if (empty($_GET['zlist'])) $_GET['zlist']='admin/index';
	
	wj_mail_header();
//echo '<div width="100%">';
	echo '<div id="wjmail-mailz-cp-content">';
	if ($_GET['zlistpage']=='admin') {
		echo 'Please use the <a href="users.php">Wordpress Users menu</a> to change <strong>admin</strong> user details';
	} else {
		echo '<div id="phplist">'.$wj_mail_content.'</div>';
	}
	echo '</div>';
	echo '<div id="wj-mail-cp-menu">';
	echo $wj_mail_menu;
	echo '</div>';
//echo '</div>';
	
}

add_action('admin_menu', 'wj_mail_add_admin'); ?>