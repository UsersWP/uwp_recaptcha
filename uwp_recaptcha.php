<?php
/*
Plugin Name: UsersWP - ReCaptcha
Plugin URI: https://userswp.io
Description: ReCaptcha add-on for UsersWP.
Version: 1.0.4
Author: AyeCode Ltd
Author URI: https://userswp.io
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: uwp-recaptcha
Domain Path: /languages
Update URL: https://github.com/UsersWP/uwp_recaptcha/
Update ID: 323
*/
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'UWP_RECAPTCHA_VERSION', '1.0.4' );

define( 'UWP_RECAPTCHA_PATH', plugin_dir_path( __FILE__ ) );

define( 'UWP_RECAPTCHA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( is_admin() ) {

    if ( !function_exists( 'deactivate_plugins' ) ) {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }

    // Check UsersWP class exists or not.
    if ( !class_exists( 'UsersWP' ) ) {

        deactivate_plugins( plugin_basename( __FILE__ ) );
        function uwp_recaptcha_requires_userswp_plugin() {
            echo '<div class="notice notice-warning is-dismissible"><p><strong>' . sprintf( __( '%s requires %sUsersWP%s plugin to be installed and active.', 'uwp-recaptcha' ), 'UsersWP - Recaptcha', '<a href="https://wordpress.org/plugins/userswp/" target="_blank">', '</a>' ) . '</strong></p></div>';
        }
        add_action( 'admin_notices', 'uwp_recaptcha_requires_userswp_plugin' );
        return;

    }
}

require plugin_dir_path(__FILE__) . 'includes/class-uwp-recaptcha.php';

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

            require_once('includes/activator.php');
            UWP_ReCaptcha_Activator::activate();

            // Restore original blog.
            restore_current_blog();
        } else {
            require_once('includes/activator.php');
            UWP_ReCaptcha_Activator::activate();
        }
    } else {
        require_once('includes/activator.php');
        UWP_ReCaptcha_Activator::activate();
    }
}
register_activation_hook( __FILE__, 'activate_uwp_recaptcha' );


function init_uwp_recaptcha() {

    UsersWP_Recaptcha::get_instance();
}
add_action( 'plugins_loaded', 'init_uwp_recaptcha', apply_filters( 'uwp_recaptcha_action_priority', 10 ) );