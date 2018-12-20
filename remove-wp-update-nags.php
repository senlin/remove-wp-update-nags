<?php






add_action( 'admin_init', 'so_remove_update_core' );

add_action( 'admin_menu', 'so_hide_wp_update_nag' );

/**
 * Remove Updates menu from Dashboard
 */
function so_remove_update_core() {
    remove_submenu_page( 'index.php', 'update-core.php' );
}

//hide core updates notification in the dashboard - https://wordpress.stackexchange.com/a/77300/2015
function so_hide_wp_update_nag() {
    remove_action( 'admin_notices', 'update_nag', 3 ); //update notice at the top of the screen
    add_filter( 'update_footer', 'so_smarter_update_footer', 9999);
}

// remove the update nag in the dashboard footer, only show WP version
// @source: https://developer.wordpress.org/reference/functions/core_update_footer/#comment-1865
function so_smarter_update_footer() {

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


