<?php /*
Plugin Name: Remove WP Update Nags
Plugin URI: https://so-wp.com/plugin/remove-wp-update-nags
Description: Free WordPress plugin that removes all WP Update Nags, great for if you want to stay on WP 4.9.x and do not want to be constantly "reminded".
Version: 1.1.0
Author: SO WP
Author URI: https://so-wp.com
Text Domain: remove-wp-update-nags
Domain Path: /languages
*/

/** Prevent direct access to files */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'admin_init', 'rwpun_remove_update_core' );

add_action( 'admin_menu', 'rwpun_hide_wp_update_nag' );

/**
 * Remove Updates menu from Dashboard
 */
function rwpun_remove_update_core() {
    remove_submenu_page( 'index.php', 'update-core.php' );
}

//hide core updates notification in the dashboard - //wordpress.stackexchange.com/a/77300/2015
function rwpun_hide_wp_update_nag() {
    remove_action( 'admin_notices', 'update_nag', 3 ); //update notice at the top of the screen
    add_filter( 'update_footer', 'rwpun_smarter_update_footer', 9999);
}

// remove the update nag in the dashboard footer, only show WP version
// @source: //developer.wordpress.org/reference/functions/core_update_footer/#comment-1865
function rwpun_smarter_update_footer() {

    if ( !current_user_can('update_core') )
        return sprintf( __( 'Version %s' ), get_bloginfo( 'version', 'display' ) );

    $cur = get_preferred_from_update_core();
    if ( ! is_object( $cur ) )
        $cur = new stdClass;

    if ( ! isset( $cur->current ) )
        $cur->current = '';

    if ( ! isset( $cur->url ) )
        $cur->url = '';

    if ( ! isset( $cur->response ) )
        $cur->response = '';

    switch ( $cur->response ) {
    case 'development' :
        return sprintf( __( 'You are using a development version (%s).' ), get_bloginfo( 'version', 'display' ) );

    case 'upgrade' :
        return sprintf( __( 'Version %s' ), get_bloginfo( 'version', 'display' ) );

    case 'latest' :
    default :
        return sprintf( __( 'Version %s' ), get_bloginfo( 'version', 'display' ) );
    }
}


