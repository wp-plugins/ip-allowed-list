<?php


// Add "settings" link to plugin page
add_filter('plugin_action_links', 'wlist_PluginActions', 10, 2);
function wlist_PluginActions($links, $file) {
	$this_plugin = 'allowed-list/allowed-list.php';
	if ( $file == $this_plugin ){
		$settings_link = '<a href="tools.php?page=wlist-options">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
}

// Add menu links
add_action('admin_menu', 'wlist_Admin_Menu');
function wlist_Admin_Menu() {
	$hook = add_submenu_page('tools.php', 'Configure IP Address Allowed List', 'IP Allowed List', 8, 'wlist-options', 'wlist_Admin_SettingsPage');
	add_action("load-$hook", 'wlist_Admin_JS_CSS');
}

// Load JS + CSS
function wlist_Admin_JS_CSS() {
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_style('ip-allow-list', WLIST_PLUGIN_URL.'/admin.css');
	if (function_exists('use_codepress')) wp_enqueue_script('codepress');
	if (use_codepress()) add_action('admin_print_footer_scripts', 'codepress_footer_js');
}

// Show the Options page
function wlist_Admin_SettingsPage() {
	$opts = get_option(WLIST_OPTKEY);
	$list = get_option(WLIST_OPTKEY.'_list');
	echo '<div class="wrap">'."\n";
	echo '<h2>IP Address Allowed List</h2>'."\n";
	
	if (isset($_POST['AddIP_x']) && isset($_POST['AddIP_y'])) {
		if (!wp_verify_nonce($_POST['_wpnonce'], 'wlistSettings')) { echo '<p class="alert">Invalid Security</p></div>'."\n"; return;	}
		// Add this IP to allowed list
		$list[] = $_SERVER['REMOTE_ADDR'];
		$list = array_unique($list);
		update_option(WLIST_OPTKEY.'_list', $list );
		echo '<div id="message" class="updated fade"><p><strong>Your IP has been added to the list</strong></p></div>';		
	} else
	if (isset($_POST['RemSel_x']) && isset($_POST['RemSel_y'])) {
		if (!wp_verify_nonce($_POST['_wpnonce'], 'wlistSettings')) { echo '<p class="alert">Invalid Security</p></div>'."\n"; return;	}
		// Remove seleted IPs from allowed list
		if (is_array($_POST['delete'])) {
			foreach ($_POST['delete'] as $delID) {
				unset($list[$delID]);
			}
			update_option(WLIST_OPTKEY.'_list', $list );
			echo '<div id="message" class="updated fade"><p><strong>Selected IPs have been removed from the list</strong></p></div>';
		}
	} else
	if (isset($_POST['SaveListContent'])) {
		if (!wp_verify_nonce($_POST['_wpnonce'], 'wlistContent')) { echo '<p class="alert">Invalid Security</p></div>'."\n"; return;	}
		// Save HTML content
		$content = stripslashes($_POST['newcontent']);
		file_put_contents(WLIST_CONTENTFILE, $content);
		//echo WLIST_CONTENTFILE.'<br /><br />'.$content;
		//echo '<pre>'; print_r($_POST); echo '</pre>';
		echo '<div id="message" class="updated fade"><p><strong>Changes to Blocked Content file saved.</strong></p></div>';
	}
	
	?>
<div id="tabContainer">
  <ul id="tabMenu">
	<li><a href="#tabSettings"><span>List Settings</span></a></li>
	<li><a href="#tabContent"><span>Blocked Content</span></a></li>
  </ul><!-- tcTabMenu -->
  
  <div id="tabSettings">
<form id="wlistSettings" class="wlist" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
  <?php wp_nonce_field('wlistSettings') ?>
  <fieldset id="curList">
    <legend>Current List of IPs</legend>
	<p class="buttons">
        <label class="add"><input type="image" name="AddIP" id="AddIP" src="<?php echo WLIST_PLUGIN_URL; ?>/img/add.png"> Add this IP</label>
        <label class="rem"><input type="image" name="RemSel" id="RemSel" src="<?php echo WLIST_PLUGIN_URL; ?>/img/delete.png"> Remove Selected</label>
    </p>
    <ul>
<?php
if (!is_array($list) || count($list) < 1) {	echo '<li>No IP addresses are currently on the list!</li>';	}
foreach ($list as $key=>$val) {
	echo "\t\t".'<li><input type="checkbox" class="tick" name="delete[]" id="ip_'.$key.'" value="'.$key.'" /><label for="ip_'.$key.'">'.$val.'</label></li>'."\n";
}
?>
    </ul>
  </fieldset>
  <fieldset id="curSettings">
	<legend>Settings</legend>
	<p class="privacy"><a href="options-privacy.php">Blog Privacy</a></p>
	<p><label for="token_add">Add Token</label>
    	?<input type="text" name="token_add" id="token_add" value="<?php echo $opts['tokenAddList']; ?>" /></p>
    <p><label for="token_remove">Remove Token</label>
    	?<input type="text" name="token_remove" id="token_remove" value="<?php echo $opts['tokenRemList']; ?>" /></p>
    <div class="notes">
    <p>Tokens allow you to add or remove your IP address to the allowed list.</p>
    <p>This provides a means to share your site with someone, by allowing them to add themselves to the list.</p>
    <p>If you visit <code>yoursite.com/?add_token</code> you will be added to the list.</p>
    </div>
	<div class="save"><input type="submit" name="SaveListSettings" value="Save Changes" id="Save" class="button-primary" /></div>
  </fieldset>
  <div class="cb"></div>
</form>
  </div><!-- tab -->
  
  
  <div id="tabContent">
<form id="template" class="wlist wlistContent" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>#tabContent">
  <?php wp_nonce_field('wlistContent') ?>
<?php
if (!is_writable(WLIST_CONTENTFILE)) {
	echo '<div class="warning"><p><strong>Warning: <code>blocked-content.htm</code> is not writable.<br />
									It MUST be writable before you can make any changes to your content file!</strong></p></div>';
	echo '</form></div></div></div><!-- tab container wrap -->';
	return;
}
$fileContent = file_get_contents(WLIST_CONTENTFILE);
?>
  <textarea name="newcontent" id="newcontent" class="large-text codepress html" rows="25" cols="50"><?php echo $fileContent; ?></textarea>
  <div class="save"><input type="submit" name="SaveListContent" value="Save Changes" id="Save" class="button-primary" /></div>
  
</form>


        
  </div><!-- tab -->
</div><!-- container -->
<script type="text/javascript"><!--
jQuery(document).ready(function($){
	$('#tabContainer').tabs();
});
--></script>
    <?php
	echo '</div><!-- wrap -->'."\n";
}




?>