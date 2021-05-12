<?php
if (!defined('WFWAF_LOG_PATH')) {
	define('WFWAF_LOG_PATH', WP_CONTENT_DIR . '/wflogs/');
}

require_once 'wfaWAFAutoPrependUninstaller.php';

class wordfenceAssistant {
	public static function installPlugin(){
	}
	public static function uninstallPlugin(){
	}
	public static function install_actions(){
		if(is_admin()){
			add_action('admin_init', 'wordfenceAssistant::admin_init');
			if(is_multisite()){
				if(self::isAdminPageMU()){
					add_action('network_admin_menu', 'wordfenceAssistant::admin_menus');
				} //else don't show menu
			} else {
				add_action('admin_menu', 'wordfenceAssistant::admin_menus');
			}

		}
	}
	public static function admin_init(){
		if(! self::isAdmin()){ return; }
		add_action('wp_ajax_wordfenceAssistant_do', 'wordfenceAssistant::ajax_do_callback');
		wp_enqueue_script('wordfenceAstjs', self::getBaseURL() . 'js/admin.js', array('jquery'), WORDFENCE_ASSISTANT_VERSION);
		wp_enqueue_style('wordfenceast-main-style', self::getBaseURL() . 'css/main.css', '', WORDFENCE_ASSISTANT_VERSION);
		wp_localize_script('wordfenceAstjs', 'WordfenceAstVars', array(
			'ajaxURL' => admin_url('admin-ajax.php'),
			'firstNonce' => wp_create_nonce('wp-ajax')
			));
	}
	public static function admin_menus(){
		if(! self::isAdmin()){ return; }
		add_submenu_page("WFAssistant", "WF Assistant", "WF Assistant", "activate_plugins", "WFAssistant", 'wordfenceAssistant::mainMenu');
		add_menu_page('WF Assistant', 'WF Assistant', 'activate_plugins', 'WFAssistant', 'wordfenceAssistant::mainMenu', self::getBaseURL() . 'images/wordfence-logo.svg'); 
	}
	public static function mainMenu(){
		require 'mainMenu.php';
	}
	public static function ajax_do_callback(){
		if(! self::isAdmin()){
			die(json_encode(array('errorMsg' => "You appear to have logged out or you are not an admin. Please sign-out and sign-in again.")));
		}
		$func = $_POST['func'];
		$nonce = $_POST['nonce'];
		if(! wp_verify_nonce($nonce, 'wp-ajax')){ 
			die(json_encode(array('errorMsg' => "Your browser sent an invalid security token to Wordfence. Please try reloading this page or signing out and in again.")));
		}
		if($func == 'delAll'){
			return self::delAll();
		} else if($func == 'clearLocks'){
			return self::clearLocks();
		} else if ($func == 'disableAutoUpdate') {
			return self::disableAutoUpdate();
		} else if($func == 'disableFirewall'){
			return self::disableFirewall();
		} else if ($func == 'finalizeDisableFirewall') {
			return self::finalizeDisableFirewall();
		} else if ($func == 'disableIPBlacklist') {
			return self::disableIPBlacklist();
		} else if($func == 'clearLiveTraffic'){
			return self::clearLiveTraffic();
		} else {
			die(json_encode(array('errorMsg' => "An invalid operation was requested.")));
		}

	}
	public static function clearLiveTraffic(){
		global $wpdb;
		$errors = $wpdb->suppress_errors();
		$wpdb->query("truncate table " . $wpdb->base_prefix . "wfHits");
		$wpdb->query("delete from " . $wpdb->base_prefix . "wfHits");
		$wpdb->query("truncate table " . $wpdb->base_prefix . "wfhits");
		$wpdb->query("delete from " . $wpdb->base_prefix . "wfhits");
		$wpdb->suppress_errors($errors);
		die(json_encode(array('msg' => "All Wordfence live traffic data deleted.")));
	}
	public static function disableAutoUpdate() {
		global $wpdb;
		$errors = $wpdb->suppress_errors();
		$wpdb->query("update " . $wpdb->base_prefix . "wfConfig set val=0 where name='autoUpdate'");
		$wpdb->query("update " . $wpdb->base_prefix . "wfconfig set val=0 where name='autoUpdate'");
		$wpdb->suppress_errors($errors);
	}
	public static function disableFirewall() {
		$wafUninstaller = new wfaWAFAutoPrependUninstaller();
		
		$removeBootstrap = null;
		if (!$wafUninstaller->bootstrapFileIsActive()) {
			$removeBootstrap = true;
		}
		
		self::_disableFirewall($removeBootstrap);
		if ($removeBootstrap !== true && $wafUninstaller->usesUserIni()) { //Using a .user.ini where there's a delay before taking effect
			$iniTTL = intval(ini_get('user_ini.cache_ttl'));
			if ($iniTTL == 0) {
				$iniTTL = 300; //The PHP default
			}
			$timeout = max(30000, ($iniTTL + 1) * 1000);
			
			if ($timeout < 60000) { $timeoutString = floor($timeout / 1000) . ' second' . ($timeout == 1000 ? '' : 's'); }
			else { $timeoutString = floor($timeout / 60000) . ' minute' . (floor($timeout / 60000) == 1 ? '' : 's'); }
			
			$content = "<p class='wordfence-assistant-waiting'><img src='" . self::getBaseURL() . "images/loading_large.gif' alt='Loading indicator'>&nbsp;&nbsp;<span>Waiting for it to take effect. This may take up to {$timeoutString} due to caching of the .user.ini file. Stay on this page to ensure that the last step is finished, and a message will appear when this is complete.</span></p>";
			$content .= "<script>
setTimeout(function() { WFAST.finalizeDisableFirewall(); }, {$timeout});
</script>";
			die(json_encode(array('html' => $content)));
		}
		
		die(json_encode(array('msg' => "Wordfence firewall has been disabled.")));
	}
	public static function finalizeDisableFirewall() {
		$wafUninstaller = new wfaWAFAutoPrependUninstaller();
		if ($wafUninstaller->bootstrapFileIsActive()) {
			die(json_encode(array('msg' => "The Wordfence Web Application Firewall has not been fully removed. This may be because auto_prepend_file is configured somewhere else or the value is still cached by PHP.")));
		}
		
		self::_disableFirewall(true);
		die(json_encode(array('msg' => "Wordfence firewall has been disabled.")));
	}
	private static function _disableFirewall($removeBootstrap = null) {
		global $wpdb;

		//Old Firewall
		$errors = $wpdb->suppress_errors();
		$wpdb->query("update " . $wpdb->base_prefix . "wfConfig set val=0 where name='firewallEnabled'");
		$wpdb->query("update " . $wpdb->base_prefix . "wfconfig set val=0 where name='firewallEnabled'");
		$wpdb->suppress_errors($errors);

		//WAF
		self::_recursivelyRemoveWflogs('');

		$wafUninstaller = new wfaWAFAutoPrependUninstaller();
		$wafUninstaller->uninstall($removeBootstrap);
	}
	public static function disableIPBlacklist() {
		//Disable the setting
		$configContents = @file_get_contents(WFWAF_LOG_PATH . '/config.php');
		$separator = '******************************************************************';
		$firstSeparator = @strpos($configContents, $separator);
		if ($firstSeparator === false) { die(json_encode(array('msg' => "Error reading WAF config while attempting to disable blacklist."))); }
		$secondSeparator = @strpos($configContents, $separator, $firstSeparator + strlen($separator));
		if ($secondSeparator === false) { die(json_encode(array('msg' => "Error reading WAF config while attempting to disable blacklist."))); }
		$serializedConfig = @substr($configContents, $secondSeparator + strlen($separator) + 1 /* \n */);
		$config = @unserialize($serializedConfig);
		if (!is_array($config)) { die(json_encode(array('msg' => "Unexpected value found while attempting to disable blacklist."))); }
		$config['disableWAFBlacklistBlocking'] = 1;
		$serializedConfig = serialize($config);
		$configContents = substr($configContents, 0, $secondSeparator + strlen($separator) + 1 /* \n */) . $serializedConfig;
		if (file_put_contents(WFWAF_LOG_PATH . '/config.php', $configContents) === false) {
			die(json_encode(array('msg' => "Failed saving the WAF config while attempting to disable blacklist.")));
		}
		
		//Remove the blocked IP cache
		self::_recursivelyRemoveWflogs('ips.php');
		
		die(json_encode(array('msg' => "The Wordfence IP Blacklist has been disabled.")));
	}
	
	/**
	 * Removes a path within wflogs, recursing as necessary.
	 *
	 * @param string $file
	 * @param array $processedDirs
	 * @return array The list of removed files/folders.
	 */
	private static function _recursivelyRemoveWflogs($file, $processedDirs = array()) {
		if (preg_match('~(?:^|/|\\\\)\.\.(?:/|\\\\|$)~', $file)) {
			return array();
		}
		
		if (stripos(WFWAF_LOG_PATH, 'wflogs') === false) { //Sanity check -- if not in a wflogs folder, user will have to do removal manually
			return array();
		}
		
		$path = rtrim(WFWAF_LOG_PATH, '/') . '/' . $file;
		if (is_link($path)) {
			if (@unlink($path)) {
				return array($file);
			}
			return array();
		}
		
		if (is_dir($path)) {
			$real = realpath($file);
			if (in_array($real, $processedDirs)) {
				return array();
			}
			$processedDirs[] = $real;
			
			$dir = opendir($path);
			if ($dir) {
				$contents = array();
				while ($sub = readdir($dir)) {
					if ($sub == '.' || $sub == '..') { continue; }
					$contents[] = $sub;
				}
				closedir($dir);
				
				$filesRemoved = array();
				foreach ($contents as $f) {
					$removed = self::_recursivelyRemoveWflogs($file . '/' . $f, $processedDirs);
					$filesRemoved = array($filesRemoved, $removed);
				}
			}
			
			if (@rmdir($path)) {
				$filesRemoved[] = $file;
			}
			return $filesRemoved;
		}
		
		if (@unlink($path)) {
			return array($file);
		}
		return array();
	}
	
	public static function clearLocks(){
		global $wpdb;
		$tables = array('wfBlocks', 'wfBlocks7', 'wfBlocksAdv', 'wfLockedOut', 'wfScanners', 'wfLeechers');
		foreach($tables as $t){
			$wpdb->query("truncate table " . $wpdb->base_prefix . "$t"); 
			$wpdb->query("delete from " . $wpdb->base_prefix . "$t"); //Compensates for the lack of permission to truncate
			
			$wpdb->query("truncate table " . $wpdb->base_prefix . strtolower($t));  
			$wpdb->query("delete from " . $wpdb->base_prefix . strtolower($t)); //Compensates for the lack of permission to truncate
		}
		
		//WAF
		if (class_exists('wfWAF')) {
			try {
				wfWAF::getInstance()->getStorageEngine()->setConfig('patternBlocks', '', 'synced');
				wfWAF::getInstance()->getStorageEngine()->setConfig('countryBlocks', '', 'synced');
				wfWAF::getInstance()->getStorageEngine()->setConfig('otherBlocks', '', 'synced');
				wfWAF::getInstance()->getStorageEngine()->setConfig('lockouts', '', 'synced');
				
				if (method_exists(wfWAF::getInstance()->getStorageEngine(), 'purgeIPBlocks')) {
					wfWAF::getInstance()->getStorageEngine()->purgeIPBlocks(wfWAFStorageInterface::IP_BLOCKS_BLACKLIST);
				}
			}
			catch (Exception $e) {
				// Do nothing
			}
		}
		else { //Not loaded, maybe plugin is disabled?
			@unlink(rtrim(WFWAF_LOG_PATH, '/') . '/config-synced.php'); //Synced back on next hit
		}
		
		die(json_encode(array('msg' => "All locked IPs, locked out users and advanced blocks cleared.")));
	}
	public static function delAll() {
		if (defined('WORDFENCE_VERSION')) {
			die(json_encode(array('errorMsg' => __('Please deactivate the Wordfence plugin before you delete all of its data.', 'wordfence-assistant'))));
		}
		if (defined('WORDFENCE_LS_VERSION')) {
			die(json_encode(array('errorMsg' => __('Please deactivate the Wordfence Login Security plugin before you delete all of its data.', 'wordfence-assistant'))));
		}
		
		global $wpdb;
		self::_disableFirewall();
		$tables = array('wfBlocks', 'wfBlocksAdv', 'wfLockedOut', 'wfThrottleLog', 'wfNet404s', 'wfBlockedCommentLog', 'wfVulnScanners', 'wfBadLeechers', 'wfLeechers', 'wfScanners', 'wfBlocks7',  'wfConfig',  'wfCrawlers',  'wfFileChanges',  'wfHits',  'wfIssues',  'wfPendingIssues',  'wfTrafficRates',  'wfLocs',  'wfLogins',  'wfReverseCache',  'wfStatus',  'wfHoover',  'wfFileMods',  'wfBlockedIPLog',  'wfSNIPCache',  'wfKnownFileList',  'wfNotifications',  'wfLiveTrafficHuman',  'wfPerfLog', 'wfls_settings', 'wfls_2fa_secrets');
		foreach ($tables as $t) {
			$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->base_prefix . "$t");
			$wpdb->query("DROP TABLE IF EXISTS " . $wpdb->base_prefix . strtolower($t));
		}
		
		update_option('wordfenceActivated', 0);
		wp_clear_scheduled_hook('wordfence_daily_cron');
		wp_clear_scheduled_hook('wordfence_hourly_cron');
		//Remove old legacy cron job if it exists
		wp_clear_scheduled_hook('wordfence_scheduled_scan');
		wp_clear_scheduled_hook('wordfence_start_scheduled_scan'); //Unschedule legacy scans without args
		wp_clear_scheduled_hook('wordfence_ls_ntp_cron');
		//Any additional scans will fail and won't be rescheduled. 
		
		foreach (array('wordfence_version', 'wordfenceActivated', 'wf_plugin_act_error', 'wordfence_case', 'wordfence_lastSyncAttackData', 'wordfence_syncAttackDataAttempts', 'wordfence_syncingAttackData', 'wordfence_ls_version') as $opt) {
			delete_option($opt);
		}
		
		$caps = array('wf2fa_activate_2fa_self', 'wf2fa_activate_2fa_others', 'wf2fa_manage_settings');
		$roles = new WP_Roles();
		foreach ($roles->role_objects as $name => $r) {
			if (is_multisite()) {
				self::_remove_cap_multisite($name, $caps);
			}
			else {
				self::_remove_cap($name, $caps);
			}
		}

		die(json_encode(array('msg' => __('All Wordfence tables and data were removed.', 'wordfence-assistant'))));
	}
	public static function isAdmin(){
		if(is_multisite()){
			if(current_user_can('manage_network')){
				return true;
			}
		} else {
			if(current_user_can('manage_options')){
				return true;
			}
		}
		return false;
	}
	public static function isAdminPageMU(){
		if(preg_match('/^[\/a-zA-Z0-9\-\_\s\+\~\!\^\.]*\/wp-admin\/network\//', $_SERVER['REQUEST_URI'])){ 
			return true; 
		}
		return false;
	}
	public static function getBaseURL(){
		return plugins_url('', WORDFENCE_ASSISTANT_FCPATH) . '/';
	}
	
	private static function _wp_roles($site_id = null) {
		require(ABSPATH . 'wp-includes/version.php'); /** @var string $wp_version */
		if (version_compare($wp_version, '4.9', '>=')) {
			return new WP_Roles($site_id);
		}
		
		//\WP_Roles in WP < 4.9 initializes based on the current blog ID
		$current_site = get_current_blog_id();
		switch_to_blog($site_id);
		$wp_roles = new WP_Roles();
		switch_to_blog($current_site);
		return $wp_roles;
	}
	
	private static function _remove_cap_multisite($role_name, $cap) {
		global $wpdb;
		$blogs = $wpdb->get_col("SELECT `blog_id` FROM `{$wpdb->blogs}` WHERE `deleted` = 0");
		foreach ($blogs as $id) {
			$wp_roles = self::_wp_roles($id);
			$current_site = get_current_blog_id();
			switch_to_blog($id); //We have to set the blog ID for $wpdb because \WP_Roles does not set it prior to saving a multisite role
			self::_remove_cap($role_name, $cap, $wp_roles);
			switch_to_blog($current_site);
		}
	}
	
	private static function _remove_cap($role_name, $cap, $wp_roles = null) {
		if ($wp_roles === null) { $wp_roles = self::_wp_roles(); }
		$role = $wp_roles->get_role($role_name);
		if ($role === null) {
			return false;
		}
		
		if (!is_array($cap)) { $cap = array($cap); }
		foreach ($cap as $c) {
			$wp_roles->remove_cap($role_name, $c);
		}
		
		return true;
	}
}
?>
