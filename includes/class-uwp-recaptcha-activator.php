<?php
/**
 * Fired during plugin activation
 *
 * @link       http://wpgeodirectory.com
 * @since      1.0.0
 *
 * @package    Users_WP
 * @subpackage Users_WP/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Users_WP
 * @subpackage Users_WP/includes
 * @author     GeoDirectory Team <info@wpgeodirectory.com>
 */
class UWP_ReCaptcha_Activator {

    /**
     * @since    1.0.0
     */
    public static function activate() {
        self::add_default_options();
    }

    public static function add_default_options() {

        $settings = get_option( 'uwp_settings', array());

        $settings['recaptcha_api_key'] = "";
        $settings['recaptcha_api_secret'] = "";

        $settings['enable_recaptcha_in_register_form'] = "1";
        $settings['enable_recaptcha_in_login_form'] = "1";
        $settings['enable_recaptcha_in_forgot_form'] = "1";
        $settings['enable_recaptcha_in_account_form'] = "1";


        $settings['recaptcha_title'] = "";
        $settings['recaptcha_theme'] = "light";

        update_option( 'uwp_settings', $settings );

    }

}