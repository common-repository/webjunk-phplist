<?php
/*  wjphplist.php
 Copyright 2008,2009,2010 
 Support site: http://webjunk.com

 This file is part of WebJunk PHPList.

 WebJunk PHPList is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Webjunk PHPList is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this software; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
?>
<?php
/*
 Plugin Name: WebJunk PHPList
 Plugin URI: http://webjunk.com
 Description: This plugin provides easy to use mailing list functionality to your Wordpress site
 Author: Paul Newman
 Version: 1.2.0
 Author URI: http://webjunk.com/
 */
define("WJ_MAIL_VERSION","1.2.0");
define("WJ_MAIL_PREFIX","wjml_");
define("phplistdir","/home/webjunkd/public_html/lists");
define("phplisturl","/lists");
/* 
Depreciated - Do not Use Below line: 
*/
// define("WJ_PHPLIST_URL","http://webjunk.com/newsletter");
define("WJ_PHPLIST_URL",get_option('WJ_PHPLIST_URL1'));


// Pre-2.6 compatibility for wp-content folder location
if (!defined("WP_CONTENT_URL")) {
	define("WP_CONTENT_URL", get_option("siteurl") . "/wp-content");
}
if (!defined("WP_CONTENT_DIR")) {
	define("WP_CONTENT_DIR", ABSPATH . "wp-content");
}

if (!defined("WJ_MAIL_PLUGIN")) {

	$wj_mail_plugin=substr(dirname(__FILE__),strlen(WP_CONTENT_DIR)+9,strlen(dirname(__FILE__))-strlen(WP_CONTENT_DIR)-9);
	define("WJ_MAIL_PLUGIN", $wj_mail_plugin);
}

if (!defined("WJ_MAIL_SUB")) {
	if (get_option("siteurl") == get_option("home"))
	{
		define("WJ_MAIL_SUB", "wp-content/plugins/".WJ_MAIL_PLUGIN."/osticket/upload/");
	}
	else {
		define("WJ_MAIL_SUB", "wordpress/wp-content/plugins/".WJ_MAIL_PLUGIN."/osticket/upload/");
	}
}
if (!defined("WJ_MAIL_DIR")) {
	define("WJ_MAIL_DIR", WP_CONTENT_DIR . "/plugins/".WJ_MAIL_PLUGIN."/osticket/upload/");
}

if (!defined("WJ_MAIL_LOC")) {
	define("WJ_MAIL_LOC", WP_CONTENT_DIR . "/plugins/".WJ_MAIL_PLUGIN."/");
}

if (!defined("WJ_MAIL_URL")) {
	define("WJ_MAIL_URL", WP_CONTENT_URL . "/plugins/".WJ_MAIL_PLUGIN."/");
}
if (!defined("WJ_MAIL_LOGIN")) {
	define("WJ_MAIL_LOGIN", get_option("wj_mail_login"));
}



$wj_footers[]=array('http://www.phplist.com/','PHPlist');
$wj_mail_version=get_option("wj_mail_version");
if ($wj_mail_version) {
	add_action("init","wj_mail_init");
	add_filter('wp_footer','wj_mail_footer');
	add_filter('the_content', 'wj_mail_content', 10, 3);
	add_action('wp_head','wj_mail_header');
}

register_activation_hook(__FILE__,'wj_mail_activate');
register_deactivation_hook(__FILE__,'wj_mail_deactivate');
require_once(dirname(__FILE__) . '/includes/shared.inc.php');
require_once(dirname(__FILE__) . '/includes/http.class.php');
require_once(dirname(__FILE__) . '/includes/footer.inc.php');
require_once(dirname(__FILE__) . '/includes/integrator.inc.php');
require_once(dirname(__FILE__) . '/wjphplist_cp.php');

/**
 * Activation: creation of database tables & set up of pages
 * @return unknown_type
 */
function wj_mail_activate() {
	global $wpdb;
	global $current_user;
	global $wj_mail_options;

	$wpdb->show_errors();
	$prefix=$wpdb->prefix.WJ_MAIL_PREFIX;
	$wj_mail_version=get_option("wj_mail_version");
	if (!$wj_mail_version)
	{
		add_option("wj_mail_version",WJ_MAIL_VERSION);
	}
	else
	{
		update_option("wj_mail_version",WJ_MAIL_VERSION);
	}

	//create standard pages
	if ($wj_mail_version <= '1.0.0') {
		$pages=array();
		$pages[]=array("Mailing list","mailz","*",0);

		$ids="";
		foreach ($pages as $i =>$p)
		{
			$my_post = array();
			$my_post['post_title'] = $p['0'];
			$my_post['post_content'] = '';
			$my_post['post_status'] = 'publish';
			$my_post['post_author'] = 1;
			$my_post['post_type'] = 'page';
			$my_post['comment_status'] = 'closed';
			$my_post['menu_order'] = 100+$i;
			$id=wp_insert_post( $my_post );
			if (empty($ids)) { $ids.=$id; } else { $ids.=",".$id; }
			if (!empty($p[1])) add_post_meta($id,'wj_mail_page',$p[1]);
		}
		if (get_option("wj_mail_pages"))
		{
			update_option("wj_mail_pages",$ids);
		}
		else {
			add_option("wj_mail_pages",$ids);
		}
	}

}

/**
 * Deactivation
 * @return void
 */
function wj_mail_deactivate() {
	wp_clear_scheduled_hook('wj_mail_cron_hook');
}

/**
 * Uninstallation: removal of database tables
 * @return void
 */
function zing_mailz_uninstall() {
	global $wpdb;

	$prefix=$wpdb->prefix.ZING_MAILZ_PREFIX;
	$rows=$wpdb->get_results("show tables like '".$prefix."%'",ARRAY_N);
	if (count($rows) > 0) {
		foreach ($rows as $id => $row) {
			if (strpos($row[0],'_mybb_')===false && strstr($row[0],'_ost_')===false) {
				$query="drop table ".$row[0];
				$wpdb->query($query);
			}
		}
	}
	$ids=get_option("zing_mailz_pages");
	$ida=explode(",",$ids);
	foreach ($ida as $id) {
		wp_delete_post($id);
	}
	delete_option("zing_mailz_version",ZING_VERSION);
	delete_option("zing_mailz_pages",ZING_VERSION);
}

/**
 * Main function handling content
 * @param $process
 * @param $content
 * @return unknown_type
 */
function wj_mail_main($process,$content="") {
	global $wj_mail_content;

	if ($wj_mail_content) {
		$content='<div id="phplist">'.$wj_mail_content.'</div>';
	}
	return $content;
}

function wj_mail_output($process) {

	global $post;
	global $wpdb;
	global $cfg;
	global $thisuser;
	global $nav;
	global $wj_mail_loaded,$wj_mail_mode;

	$content="";

	switch ($process)
	{
		case "content":
			if (isset($_POST['zname'])) {
				$_POST['name']=$_POST['zname'];
				unset($_POST['zname']);
			}
			$cf=get_post_custom($post->ID);
			if (isset($_GET['zlist']))
			{
				if ($_GET['page']=='wjphplist_cp.php') $to_include='admin/index';
				elseif (isset($_GET['page'])) $to_include='admin/index';
				else $to_include=$_GET['zlist'];
				$wj_mail_mode="client";
			}
			elseif (isset($_GET['zscp']))
			{
				//$to_include="scp/".$_GET['zscp'];
				$to_include="index";

				$wj_mail_mode="admin";
			}
			elseif (isset($_GET['zsetup']))
			{
				$to_include="setup/".$_GET['zscp'];
				$wj_mail_mode="setup";
			}
			elseif (isset($cf['wj_mail_page']) && ($cf['wj_mail_page'][0]=='mailz'))
			{
				//$wj_mail_mode="client";
				$to_include="index";
			}
			elseif (isset($cf['wj_mail_page']) && ($cf['wj_mail_page'][0]=='admin'))
			{
				//$to_include="scp/".$_GET['zscp'];
				$to_include="index.php";
				$wj_mail_mode="admin";
			}
			else
			{
				return $content;
			}
			if (isset($cf['cat'])) {
				$_GET['cat']=$cf['cat'][0];
			}
			break;
		default:
			return $content;
			break;
	}
	//error_reporting(E_ALL & ~E_NOTICE);
	//ini_set('display_errors', '1');

	if (wj_mail_login()) {
		$http=wj_mail_http("phplist",$to_include.'.php');
		$news = new HTTPRequest($http);
		if ($news->live()) {
			$output=stripslashes($news->DownloadToString(true));
			if ($news->redirect) {
				$redirect=str_replace(WJ_PHPLIST_URL.'/admin/?page=',get_option('siteurl').'/wp-admin/'.'options-general.php?page=wjphplist_cp.php&zlist=index&zlistpage=',$output);
				header($redirect);
				die();
			}
			$content.=wj_mail_ob($output);
			return $content;
		}
	}
}

function wj_mail_mainpage() {
	$ids=get_option("wj_mail_pages");
	$ida=explode(",",$ids);
	return $ida[0];
}

function wj_mail_ob($buffer) {
	global $current_user,$wj_mail_mode,$wpdb;

	$prefix=$wpdb->prefix.WJ_MAIL_PREFIX;
	$query="select uniqid from ".$prefix."phplist_user where email='".$current_user->data->user_email."'";
	$uid=$wpdb->get_var($query);
	$home=get_option('home');
	$admin=get_option('siteurl').'/wp-admin/';
	$pid=wj_mail_mainpage();

	$buffer=str_replace('page=','zlistpage=',$buffer);
	if (is_admin()) {
		$buffer=str_replace('<span class="menulinkleft"><a href="./?zlistpage=logout">logout</a><br /></span>','',$buffer);
		// $buffer=str_replace('<a href="./?zlistpage=logout">logout</a>','',$buffer);
		$buffer=str_replace('./?','options-general.php?'.'page=wjphplist_cp.php&zlist=index&',$buffer);
		$buffer=str_replace('<form method=post >','<form method=post action="'.$admin.'options-general.php?page=wjphplist_cp.php&zlist=index&zlistpage='.$_GET['zlistpage'].'">',$buffer);
		$buffer=str_replace('name="page"','name="zlistpage"',$buffer);
		$buffer=str_replace('<form method=get>','<form method=get><input type="hidden" name="page" value="wjphplist_cp.php" /><input type="hidden" name="zlist" value="index" /><input type="hidden" name="zlistpage" value="'.$_GET['zlistpage'].'" />',$buffer);
		$buffer=str_replace('<form method="post" action="">','<form method=post action="'.$admin.'options-general.php?page=wjphplist_cp.php&zlist=index&zlistpage='.$_GET['zlistpage'].'">',$buffer);
		$buffer=str_replace(WJ_PHPLIST_URL.'/?',$admin.'options-general.php?page=wjphplist_cp.php&zlist=index&',$buffer);
		$buffer=str_replace('./FCKeditor',WJ_PHPLIST_URL.'/admin/FCKeditor',$buffer);
		$buffer=str_replace('src="images/','src="'.WJ_PHPLIST_URL.'/admin/images/',$buffer);
		$buffer=str_replace('src="js/jslib.js"','src="'.WJ_PHPLIST_URL.'/js/jslib.js"',$buffer);
	} else {
		$buffer=str_replace('/lists/admin',$admin.'options-general.php?page=wjphplist_cp.php&zlist=index&',$buffer); //go to admin page
		$buffer=str_replace('./?',$home.'/?page_id='.$pid.'&zlist=index&',$buffer);
		$buffer=str_replace(WJ_PHPLIST_URL.'/?',$home.'/?page_id='.$pid.'&zlist=index&',$buffer);
		$buffer=str_replace('src="images/','src="'.WJ_PHPLIST_URL.'/images/',$buffer);
		if ($_GET['p']=='subscribe' && isset($current_user->data->user_email)) {
			$buffer=str_replace('name=email value=""','name=email value="'.$current_user->data->user_email.'"',$buffer);
			$buffer=str_replace('name=emailconfirm value=""','name=emailconfirm value="'.$current_user->data->user_email.'"',$buffer);
		}
		if ($_GET['p']=='unsubscribe' && isset($current_user->data->user_email)) {
			$buffer=str_replace('name="unsubscribeemail" value=""','name="unsubscribeemail" value="'.$current_user->data->user_email.'"',$buffer);
			$buffer=str_replace('uid="','uid='.$uid.'"',$buffer);
		}
		if ($_GET['p']=='preferences' && isset($current_user->data->user_email)) {
			$buffer=str_replace('name=email value=""','name=email value="'.$current_user->data->user_email.'"',$buffer);
			$buffer=str_replace('name=emailconfirm value=""','name=emailconfirm value="'.$current_user->data->user_email.'"',$buffer);
		}
	}

	return '<!--buffer:start-->'.$buffer.'<!--buffer:end-->';
}

function wj_mail_http($module,$to_include="index",$get=array()) {
	$vars="";
	if (!$to_include || $to_include==".php") $to_include="index";
	$http=WJ_PHPLIST_URL.'/';
	$http.= $to_include;
	$and="";
	if (count($_GET) > 0) {
		foreach ($_GET as $n => $v) {
			if ($n!="zpage" && $n!="page_id" && $n!="zscp" && $n!="zlistpage" && $n!="page") {
				$vars.= $and.$n.'='.wj_urlencode($v);
				$and="&";
			} elseif ($n=="zlistpage") {
				$vars.= $and.'page'.'='.wj_urlencode($v);
				$and="&";
			}
		}
	}
	if (count($get) > 0) {
		foreach ($get as $n => $v) {
			$vars.= $and.$n.'='.wj_urlencode($v);
			$and="&";
		}
	}

	$vars.=$and.'wpabspath='.urlencode(ABSPATH);
	$vars.='&wppageid='.wj_mail_mainpage();
	$vars.='&wpsiteurl='.get_option('siteurl');
	if ($vars) $http.='?'.$vars;
	return $http;
}

/**
 * Page content filter
 * @param $content
 * @return unknown_type
 */
function wj_mail_content($content) {
	return wj_mail_main("content",$content);
}


/**
 * Header hook: loads FWS addons and css files
 * @return unknown_type
 */
function wj_mail_header()
{
	global $wj_mail_content;
	global $wj_mail_menu;
	global $wj_mail_post;

	if (isset($_POST) && isset($wj_mail_post)) {
		$_POST=array_merge($_POST,$wj_mail_post);
	}

	$output=wj_mail_output("content");

	$menu1=wj_integrator_cut($output,'<div class="menutableright">','</div>');
	if ($menu1) {
		$menu1=str_replace('<span','<li><span',$menu1);
		$menu1=str_replace('</span>','</span></li>',$menu1);
		$menu1='<ul>'.$menu1.'</ul>';
		$menu1=str_replace('menulinkleft','xmenulinkleft',$menu1);
		$menu1=str_replace('<hr>','',$menu1);
	}
	$wj_mail_menu=$menu1;

	$body=wj_integrator_cut($output,'<body','</body>',true);
	$body=strchr($body,'>');
	$wj_mail_content=trim(substr($body,1));
	if (is_admin()) {
		echo '<link rel="stylesheet" type="text/css" href="' . WJ_PHPLIST_URL . '/admin/styles/phplist.css" media="screen" />';
	} else {
		echo '<link rel="stylesheet" type="text/css" href="' . WJ_MAIL_URL . 'styles/phplist.css" media="screen" />';
	}
	echo '<link rel="stylesheet" type="text/css" href="' . WJ_MAIL_URL . 'wj-phplist.css" media="screen" />';
}

/**
 * Initialization of page, action & page_id arrays
 * @return unknown_type
 */
function wj_mail_init()
{
	ob_start();
	session_start();
}

function wj_mail_login() {
	global $current_user,$wpdb;

// Below was False but changed to fix wp-admin
	$loggedin=true;

	if (!current_user_can('edit_plugins') && isset($_SESSION['zing']['mailz']['loggedin'])) {
		wj_mail_logout();
	}
	if (!is_admin()) {
		$loggedin=true;
	}
	elseif (is_admin() && current_user_can('edit_plugins') && !isset($_SESSION['zing']['mailz']['loggedin'])) {
		$post['do']='scplogin';
		$post['login']='admin';//$current_user->data->user_login;
		$post['password']=get_option('wj_mail_password');
		$post['submit']='Enter';
		$http=wj_mail_http('osticket','admin/index.php');
		$news = new HTTPRequest($http);
		$news->post=$post;
		if ($news->live()) {
			$output=stripslashes($news->DownloadToString(true));
			if (strpos($output,"invalid password")===false && strpos($output,"Default login is admin")===false) {
				$loggedin=true;
				$_SESSION['zing']['mailz']['loggedin']=1;
			}
			else echo '<br /><strong style="color:red">---</strong><br />';
		}
	}
	elseif (isset($_SESSION['zing']['mailz']['loggedin'])) $loggedin=true;
	return $loggedin;

}

function wj_mail_logout() {
	if (isset($_SESSION['zing']['mailz']['loggedin'])) {

		$_GET['zlistpage']='logout';
		$http=wj_mail_http('osticket','admin/index.php');
		$news = new HTTPRequest($http);
		if ($news->live()) {
			$output=$news->DownloadToString(true);
			unset($_SESSION['zing']['mailz']['loggedin']);
		}

	}
}
/**
 * Display common WebJunk ML footer
 * @param $page_id
 * @return unknown_type
 */
function wj_mail_footer() {
	wj_footers();
}

/*
function wj_mail_more_reccurences() {
	return array(
'minute' => array('interval' => 60, 'display' => 'Every minute'),
'weekly' => array('interval' => 604800, 'display' => 'Once Weekly'),
'fortnightly' => array('interval' => 1209600, 'display' => 'Once Fortnightly'),
	);
}
add_filter('cron_schedules', 'wj_mail_more_reccurences');
*/

function wj_mail_cron() {

	$msg=time();
	
	$post['login']='admin';
	$post['password']=get_option('wj_mail_password');
	
	$http=wj_mail_http("phplist",'admin/index.php',array('page'=>'processqueue','user'=>'admin','password'=>get_option('wj_mail_password')));

	$news = new HTTPRequest($http);
	$news->post=$post;
	
	if ($news->live()) {
		$output=$news->DownloadToString(true);
		$msg.='ok';
	} else {
		$msg.='failed';
	}
	update_option('wj_mail_cron',$msg);
}
if (!wp_next_scheduled('wj_mail_cron_hook')) {
	wp_schedule_event( time(), 'hourly', 'wj_mail_cron_hook' );
}
add_action('wj_mail_cron_hook','wj_mail_cron');
?>