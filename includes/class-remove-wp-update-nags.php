<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Remove_WP_Update_Nags {

	/**
	 * The single instance of Remove_WP_Update_Nags.
	 * @var 	object
	 * @access  private
	 * @since 	1.2.0
	 */
	private static $_instance = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.2.0
	 */
	public $version;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.2.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.2.0
	 */
	public $dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.2.0
	 */
	public $assets_url;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.5.0' ) {
		$this->_version = $version;
		$this->_token = 'Remove_WP_Update_Nags';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load admin CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Handle localisation
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 0 );

		/*** PLUGIN FUNCTIONS ***/

		add_action( 'admin_init', array( $this, 'remove_update_core' ) );

		add_action( 'admin_menu', array( $this, 'hide_wp_update_nag' ) );

	} // End __construct ()


	/**
	 * Load admin CSS
	 * @since   1.2.0
	 * @return  void
	 */
	public function admin_enqueue_styles( $hook ) {
        // Load only on main Dashboard page
        if ( $hook != 'index.php' ) {
            return;
        }
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	}


	/**
	 * Loads the translation file.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function i18n() {
		load_plugin_textdomain( 'remove-wp-update-nags', false, false );
	} // End i18n ()

	/**
	 * Remove Updates menu from Dashboard
	 *
	 * @since 1.0.0
	 */
	public function remove_update_core() {

	    remove_submenu_page( 'index.php', 'update-core.php' );

	}

	/**
	 * Hide core updates notification in the dashboard
	 *
	 * @link: //wordpress.stackexchange.com/a/77300/2015
	 *
	 * @since 1.0.0
	 */
	public function hide_wp_update_nag() {

	     //update notice at the top of the screen
	    if ( is_multisite() ) {
		    remove_action( 'network_admin_notices', 'update_nag', 3 );
	    } else {
	    	remove_action( 'admin_notices', 'update_nag', 3 );
	    }

	    add_filter( 'update_footer', array( $this, 'clean_update_footer' ), 9999);

	    remove_filter( 'update_footer', 'core_update_footer' );

	    add_filter( 'allow_dev_auto_core_updates', '__return_false' );           // Enable development updates
		add_filter( 'allow_minor_auto_core_updates', '__return_true' );         // Enable minor updates
		add_filter( 'allow_major_auto_core_updates', '__return_false' );         // Enable major updates

	}

	/**
	 * Remove the update nag in the dashboard footer, only show WP version
	 *
	 * @link: //developer.wordpress.org/reference/functions/core_update_footer/#comment-1865
	 *
	 * @todo: add calmpress condition (function_exists( 'calmpress_version' )), once we're able to test that
	 *
	 * @since 1.0.0
	 */
	function clean_update_footer() {

	    if ( ! current_user_can( 'update_core' ) )
	        return sprintf( __( 'Version %s', 'remove-wp-update-nags' ), get_bloginfo( 'version', 'display' ) );

		// Check for ClassicPress
		if ( function_exists( 'classicpress_version' ) ) {

			return sprintf( __( 'Version %s', 'remove-wp-update-nags' ), classicpress_version() );

		} else {

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
		        return sprintf( __( 'You are using a development version (%s).', 'remove-wp-update-nags' ), get_bloginfo( 'version', 'display' ) );

		    case 'upgrade' :
		        return sprintf( __( 'Version %s', 'remove-wp-update-nags' ), get_bloginfo( 'version', 'display' ) );

		    case 'latest' :
		    default :
		        return sprintf( __( 'Version %s', 'remove-wp-update-nags' ), get_bloginfo( 'version', 'display' ) );
		    }

		}
	}

	/**
	 * Main Remove_WP_Update_Nags Instance
	 *
	 * Ensures only one instance of Remove_WP_Update_Nags is loaded or can be loaded.
	 *
	 * @since 1.2.0
	 * @static
	 * @see Remove_WP_Update_Nags()
	 * @return Main Remove_WP_Update_Nags instance
	 */
	public static function instance ( $file = '', $version = '1.5.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.2.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Not allowed', 'remove-wp-update-nags' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.2.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Not allowed', 'remove-wp-update-nags' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.2.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}
