<?php
/*
Plugin Name:	IP Allowed List
Plugin URI:		http://www.fergusweb.net/software/ip-allowed-list/
Description:	Limits access to the site to people on an allowed list of IP addresses.  If you're not on the list, you only get to see a customisable "Coming Soon" style of page.  To remove protection, simply disable this plugin.
Version:		1.1
Author:			Anthony Ferguson
Author URI:		http://www.fergusweb.net
*/



// Definitions
define('WLIST_PLUGIN_URL', WP_PLUGIN_URL.'/ip-allowed-list');
define('WLIST_PLUGIN_DIR', WP_PLUGIN_DIR.'/ip-allowed-list');
define('WLIST_CONTENTFILE', WLIST_PLUGIN_DIR.'/blocked-content.htm');
define('WLIST_OPTKEY', 'ip-allowlist');

// Default Options
$wlist_DefaultOptions = array(
	'tokenAddList' => 'letmein',
	'tokenRemList' => 'takemeoff',
	'contentFrom' => 'file',		// Will change to (int) PostID if we're using that
);
add_option(WLIST_OPTKEY, $wlist_DefaultOptions);
add_option(WLIST_OPTKEY.'_list', array());

// Include required files
require_once(WLIST_PLUGIN_DIR.'/allowed-admin.php');

// Time to hijack the page if visitor not on allowed list
add_action('init', 'wlist_hijack_page');
function wlist_hijack_page() {
	wlist_token_handler();
	if (is_admin()) { return; }
	if (wlist_is_login_page()) { return; }
	// Public page, so lets greet jack.  (hijack page)
	$opts = get_option(WLIST_OPTKEY);
	$list = get_option(WLIST_OPTKEY.'_list');
	
	if ( !wlist_is_on_list($_SERVER['REMOTE_ADDR']) ) {
		// Not on the allowed list - time to take over
		$fileContent = file_get_contents(WLIST_CONTENTFILE);
		echo $fileContent;
		echo "\n\n".'<!-- This IP: '.$_SERVER['REMOTE_ADDR'].' -->'."\n";
		die;
	}
}


// Determine if this is a login page, or similar
// Since is_admin() doesn't already cover that, and I can't find an equivalent function.  dot dot dot....
function wlist_is_login_page() {
	if (stristr($_SERVER['SCRIPT_FILENAME'], 'wp-login.php')) { return true; }
	return false;
}

// Return TRUE/FALSE if $IP is on the allowed list
function wlist_is_on_list($IP=false) {
	if ($IP == false) { return false; }
	$list = get_option(WLIST_OPTKEY.'_list');
	if (!in_array($IP, $list) ) { return false; }
	return true;
}

// Handles any tokens, as set per options.  (eg,  ?letmein)
function wlist_token_handler() {
	$opts = get_option(WLIST_OPTKEY);
	$list = get_option(WLIST_OPTKEY.'_list');
	$redir = false;
	if (isset($_GET[$opts['tokenAddList']]) ) {
		$list[] = $_SERVER['REMOTE_ADDR'];
		$list = array_unique($list);
		update_option(WLIST_OPTKEY.'_list', $list );
		$redir = str_replace('?'.$opts['tokenAddList'], '', $_SERVER['REQUEST_URI']);
	} else 
	if (isset($_GET[$opts['tokenRemList']]) ) {
		$thisIP = $_SERVER['REMOTE_ADDR'];
		if (in_array($thisIP, $list)) {
			unset($list[array_search($thisIP, $list)]);
		}
		update_option(WLIST_OPTKEY.'_list', $list );
		$redir = str_replace('?'.$opts['tokenRemList'], '', $_SERVER['REQUEST_URI']);
	}
	// Redirect
	if ($redir !== false) {
		header('Location: '.$redir);
		die;
	}
}



?>