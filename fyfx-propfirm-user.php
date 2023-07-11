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
 * Plugin Name:       A - FYFX Propfirm User with Hypestacks
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

if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . '/wp-admin/includes/plugin.php');
}

/**
* Check for the existence of WooCommerce and any other requirements
*/
function fyfx_propfirm_user_check_requirements() {
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        return true;
    } else {
        add_action( 'admin_notices', 'fyfx_propfirm_user_missing_wc_notice' );
        return false;
    }
}

/**
* Display a message advising WooCommerce is required
*/
function fyfx_propfirm_user_missing_wc_notice() { 
    $class = 'notice notice-error';
    $message = __( 'FYFX Propfirm User requires WooCommerce to be installed and active.', 'fyfx-propfirm-user' );
 
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}

add_action( 'plugins_loaded', 'fyfx_propfirm_user_check_requirements' );

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
require plugin_dir_path( __FILE__ ) . 'includes/class-fyfx-propfirm-user-functions.php';

function filter_action_fyfx_propfirm_links( $links ) {
     $links['settings'] = '<a href="https://fundyourfx.com">' . __( 'Settings', 'fyfx-propfirm-user' ) . '</a>';
     $links['support'] = '<a href="https://hypestacks.stoplight.io/docs/yourpropfirm-client-api/91741256b2999-create-new-user">' . __( 'Doc', 'fyfx-propfirm-user' ) . '</a>';
     // if( class_exists( 'Fyfx_Payment' ) ) {
     //  $links['upgrade'] = '<a href="https://fundyourfx.com">' . __( 'Upgrade', 'fyfx-propfirm-user' ) . '</a>';
     // }
     return $links;
}
add_filter( 'plugin_action_links_fyfx-propfirm-user/fyfx-propfirm-user.php', 'filter_action_fyfx_propfirm_links', 10, 1 );

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
