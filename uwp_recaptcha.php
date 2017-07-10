<?php
/*
Plugin Name: UsersWP - ReCaptcha
Plugin URI: https://wpgeodirectory.com
Description: ReCaptcha add-on for UsersWP.
Version: 1.0.0-beta
Author: GeoDirectory team
Author URI: https://wpgeodirectory.com
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: uwp-recaptcha
Domain Path: /languages
Requires at least: 3.1
Tested up to: 4.6
*/
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UWP_RECAPTCHA_VERSION', '1.0.0-beta' );

define( 'UWP_RECAPTCHA_PATH', plugin_dir_path( __FILE__ ) );

define( 'UWP_RECAPTCHA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

class Users_WP_Recaptcha {

    private static $instance;

    /**
     * Plugin Version
     */
    private $version = '1.0.0-beta';

    private $file;

    private $plugin_dir;

    private $plugin_url;

    private $includes_dir;

    private $includes_url;

    /**
     * Plugin Title
     */
    public $title = 'UsersWP - ReCaptcha';


    public static function get_instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Users_WP_Recaptcha ) ) {
            self::$instance = new Users_WP_Recaptcha;
            self::$instance->setup_globals();
            self::$instance->includes();
            self::$instance->setup_actions();
            self::$instance->load_textdomain();
        }

        return self::$instance;
    }

    private function __construct() {
        self::$instance = $this;
    }

    private function setup_globals() {

        // paths
        $this->file         = __FILE__;
        $this->basename     = apply_filters( 'uwp_recaptcha_plugin_basenname', plugin_basename( $this->file ) );
        $this->plugin_dir   = apply_filters( 'uwp_recaptcha_plugin_dir_path',  plugin_dir_path( $this->file ) );
        $this->plugin_url   = apply_filters( 'uwp_recaptcha_plugin_dir_url',   plugin_dir_url ( $this->file ) );

        // includes
        $this->includes_dir = apply_filters( 'uwp_recaptcha_includes_dir', trailingslashit( $this->plugin_dir . 'includes'  ) );
        $this->includes_url = apply_filters( 'uwp_recaptcha_includes_url', trailingslashit( $this->plugin_url . 'includes'  ) );

    }

    private function setup_actions() {

        do_action( 'uwp_recaptcha_setup_actions' );
    }

    public function load_textdomain() {

        // Set filter for plugin's languages directory
        $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
        $lang_dir = apply_filters( 'uwp_recaptcha_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale        = apply_filters( 'plugin_locale',  get_locale(), 'uwp-recaptcha' );
        $mofile        = sprintf( '%1$s-%2$s.mo', 'uwp-recaptcha', $locale );

        // Setup paths to current locale file
        $mofile_local  = $lang_dir . $mofile;
        $mofile_global = WP_LANG_DIR . '/uwp-recaptcha/' . $mofile;

        if ( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/uwp-recaptcha/ folder
            load_textdomain( 'uwp-recaptcha', $mofile_global );
        } elseif ( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/uwp-recaptcha/languages/ folder
            load_textdomain( 'uwp-recaptcha', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'uwp-recaptcha', false, $lang_dir );
        }
    }

    private function includes() {

        if ( !class_exists( 'ReCaptcha' ) ) {
            require_once dirname( __FILE__ ) . '/includes/class-recaptcha.php';
        }

        if (class_exists( 'Users_WP' )) {
            require_once dirname( __FILE__ ) . '/includes/recaptcha-functions.php';
        }

        do_action( 'uwp_recaptcha_include_files' );

        if ( ! is_admin() )
            return;

        require_once dirname( __FILE__ ) . '/includes/admin-settings.php';
        do_action( 'uwp_recaptcha_include_admin_files' );

    }


}

function activate_uwp_recaptcha($network_wide) {
    if (is_multisite()) {
        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }

        // Network active.
        if ( is_plugin_active_for_network( 'userswp/userswp.php' ) ) {
            $network_wide = true;
        }
        if ($network_wide) {
            $main_blog_id = (int) get_network()->site_id;
            // Switch to the new blog.
            switch_to_blog( $main_blog_id );

            require_once('includes/class-uwp-recaptcha-activator.php');
            UWP_ReCaptcha_Activator::activate();

            // Restore original blog.
            restore_current_blog();
        } else {
            require_once('includes/class-uwp-recaptcha-activator.php');
            UWP_ReCaptcha_Activator::activate();
        }
    } else {
        require_once('includes/class-uwp-recaptcha-activator.php');
        UWP_ReCaptcha_Activator::activate();
    }
}
register_activation_hook( __FILE__, 'activate_uwp_recaptcha' );


function init_uwp_recaptcha() {

    if ( ! class_exists( 'Users_WP' ) ) {
        if ( !class_exists( 'Users_WP_Extension_Activation' ) ) {
            require_once dirname( __FILE__ ) . '/includes/class-ext-activation.php';
        }
        $activation = new Users_WP_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation->run();
        return Users_WP_Recaptcha::get_instance();

    } else {
        return Users_WP_Recaptcha::get_instance();
    }
}
add_action( 'plugins_loaded', 'init_uwp_recaptcha', apply_filters( 'uwp_recaptcha_action_priority', 10 ) );