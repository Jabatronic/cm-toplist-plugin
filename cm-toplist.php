<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              jr@iamjabulani.tech
 * @since             1.0.0
 * @package           Cm_Toplist
 *
 * @wordpress-plugin
 * Plugin Name:       Catena Media Toplist
 * Plugin URI:        www.iamjabulani.tech
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Jabulani Robbins
 * Author URI:        jr@iamjabulani.tech
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cm-toplist
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
define( 'CM_TOPLIST_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cm-toplist-activator.php
 */
function activate_cm_toplist() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cm-toplist-activator.php';
	Cm_Toplist_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cm-toplist-deactivator.php
 */
function deactivate_cm_toplist() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cm-toplist-deactivator.php';
	Cm_Toplist_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cm_toplist' );
register_deactivation_hook( __FILE__, 'deactivate_cm_toplist' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cm-toplist.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cm_toplist() {

	$plugin = new Cm_Toplist();
	$plugin->run();

}
run_cm_toplist();
