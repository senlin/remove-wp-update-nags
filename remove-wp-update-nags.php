<?php
/*
 * Plugin Name: 		Remove WP Update Nags
 * Version:     		1.3.0
 * Plugin URI:  		https://so-wp.com/plugin/remove-wp-update-nags
 * Description:			Free WordPress plugin that removes all WP Update Nags, great for if you want to stay on WP 4.9.x and do not want to be constantly "reminded".
 * Network:     		true

 * Author:				SO WP
 * Author URI:  		https://so-wp.com

 * Requires at least:	4.9
 * Tested up to:		5.0.2

 * License:    			GPL-3.0+
 * License URI:			http://www.gnu.org/licenses/gpl-3.0.txt

 * Text Domain: 		remove-wp-update-nags

 * GitHub Plugin URI:	https://github.com/senlin/remove-wp-update-nags
 * GitHub Branch:		master

 * @package WordPress
 * @author SO WP
 * @since 1.2.0
 */

// don't load the plugin file directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-remove-wp-update-nags.php' );


/**
 * Returns the main instance of Remove_WP_Update_Nags to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Remove_WP_Update_Nags
 */
function Remove_WP_Update_Nags () {
	$instance = Remove_WP_Update_Nags::instance( __FILE__, '1.3.0' );

}

Remove_WP_Update_Nags();

if ( ! function_exists( 'remove_wp_update_nags_fs' ) ) {
    // Create a helper function for easy SDK access.
    function remove_wp_update_nags_fs() {
        global $remove_wp_update_nags_fs;

        if ( ! isset( $remove_wp_update_nags_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $remove_wp_update_nags_fs = fs_dynamic_init( array(
                'id'                  => '3074',
                'slug'                => 'remove-wp-update-nags',
                'type'                => 'plugin',
                'public_key'          => 'pk_31334f00adae8f86031bbe2a90ec1',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'first-path'     => 'plugins.php',
                    'account'        => false,
                    'contact'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $remove_wp_update_nags_fs;
    }

    // Init Freemius.
    remove_wp_update_nags_fs();
    // Signal that SDK was initiated.
    do_action( 'remove_wp_update_nags_fs_loaded' );
}
