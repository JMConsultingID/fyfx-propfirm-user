<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://fundyourfx.com
 * @since             1.0.0
 * @package           Fyfx_Propfirm_User
 *
 * @wordpress-plugin
 * Plugin Name:       A - FYFX Propfirm User
 * Plugin URI:        https://fundyourfx.com
 * Description:       This Plugin to Create User or Account to FYFX Dashboard Your Propfirm
 * Version:           1.0.0
 * Author:            Ardi JM Consulting
 * Author URI:        https://fundyourfx.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fyfx-propfirm-user
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
define( 'FYFX_PROPFIRM_USER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fyfx-propfirm-user-activator.php
 */
function activate_fyfx_propfirm_user() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fyfx-propfirm-user-activator.php';
	Fyfx_Propfirm_User_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fyfx-propfirm-user-deactivator.php
 */
function deactivate_fyfx_propfirm_user() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fyfx-propfirm-user-deactivator.php';
	Fyfx_Propfirm_User_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_fyfx_propfirm_user' );
register_deactivation_hook( __FILE__, 'deactivate_fyfx_propfirm_user' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fyfx-propfirm-user.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fyfx_propfirm_user() {

	$plugin = new Fyfx_Propfirm_User();
	$plugin->run();

}
run_fyfx_propfirm_user();
