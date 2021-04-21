<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://me.io
 * @since             1.0.0
 * @package           Plugin_Scheduler
 *
 * @wordpress-plugin
 * Plugin Name:       Product Scheduler
 * Plugin URI:        http://plugin-scheduler.io
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Allan Casilum
 * Author URI:        http://me.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       plugin-scheduler
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_SCHEDULER_VERSION', '1.0.0' );

/**
 * For autoloading classes
 * */
spl_autoload_register('ps_directory_autoload_class');
function ps_directory_autoload_class($class_name) {
		if ( false !== strpos( $class_name, 'PS' ) ) {
	 $include_classes_dir = realpath( get_template_directory( __FILE__ ) ) . DIRECTORY_SEPARATOR;
	 $admin_classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
	 $class_file = str_replace( '_', DIRECTORY_SEPARATOR, $class_name ) . '.php';
	 if( file_exists($include_classes_dir . $class_file) ){
		 require_once $include_classes_dir . $class_file;
	 }
	 if( file_exists($admin_classes_dir . $class_file) ){
		 require_once $admin_classes_dir . $class_file;
	 }
 }
}
function ps_get_plugin_details() {
 // Check if get_plugins() function exists. This is required on the front end of the
 // site, since it is in a file that is normally only loaded in the admin.
 if ( ! function_exists( 'get_plugins' ) ) {
	 require_once ABSPATH . 'wp-admin/includes/plugin.php';
 }
 $ret = get_plugins();
 return $ret['product-scheduler/product-scheduler.php'];
}

/**
* get the text domain of the plugin.
**/
function ps_get_text_domain() {
 $ret = ps_get_plugin_details();
 return $ret['TextDomain'];
}

/**
* get the plugin directory path.
**/
function ps_get_plugin_dir() {
 return plugin_dir_path( __FILE__ );
}

/**
* get the plugin url path.
**/
function ps_get_plugin_dir_url() {
 return plugin_dir_url( __FILE__ );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-scheduler-activator.php
 */
function activate_plugin_scheduler() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-scheduler-activator.php';
	Plugin_Scheduler_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-scheduler-deactivator.php
 */
function deactivate_plugin_scheduler() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-scheduler-deactivator.php';
	Plugin_Scheduler_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_scheduler' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_scheduler' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-plugin-scheduler.php';

require plugin_dir_path( __FILE__ ) . 'functions/helper.php';
require plugin_dir_path( __FILE__ ) . 'functions/wp.php';

require_once __DIR__.'/vendor/autoload.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plugin_scheduler() {
	/**
	* Check if WooCommerce is active
	**/
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	  // Put your plugin code here
		$plugin = new Plugin_Scheduler();
		$plugin->run();

		\Carbon_Fields\Carbon_Fields::boot();
		PS_Options::get_instance()->init();
		PS_WPMenu::get_instance();
		PS_WOO_ProductDataTab::get_instance();

		PS_WOO_SelectSchedule::get_instance();
		PS_SelectTimeRange::get_instance()->initAjaxGetTimeRange();

		PS_Calendar_Ajax::get_instance();
	}

	//PS_CarbonDate::get_instance()->test();
}
//run_plugin_scheduler();
add_action('plugins_loaded', 'run_plugin_scheduler');
