<?php
/**
 * Fired during plugin activation
 *
 * @link       http://wpgeodirectory.com
 * @since      1.0.0
 *
 * @package    uwp_recaptcha
 * @subpackage uwp_recaptcha/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    uwp_recaptcha
 * @subpackage uwp_recaptcha/includes
 * @author     GeoDirectory Team <info@wpgeodirectory.com>
 */
class UWP_ReCaptcha_Activator {

    /**
     * @since    1.0.0
     */
    public static function activate() {
        self::add_default_options();

        // Set activation redirect flag
        set_transient( '_uwp_recaptcha_activation_redirect', true, 30 );
    }

    public static function add_default_options() {

        $settings = get_option( 'uwp_settings', array());

	    if (!isset($settings['recaptcha_api_key'])) {
		    $settings['recaptcha_api_key'] = "";
	    }
	    if (!isset($settings['recaptcha_api_secret'])) {
		    $settings['recaptcha_api_secret'] = "";
	    }
	    if (!isset($settings['enable_recaptcha_in_register_form'])) {
		    $settings['enable_recaptcha_in_register_form'] = "1";
	    }
	    if (!isset($settings['enable_recaptcha_in_login_form'])) {
		    $settings['enable_recaptcha_in_login_form'] = "1";
	    }
	    if (!isset($settings['enable_recaptcha_in_forgot_form'])) {
		    $settings['enable_recaptcha_in_forgot_form'] = "1";
	    }
	    if (!isset($settings['enable_recaptcha_in_account_form'])) {
		    $settings['enable_recaptcha_in_account_form'] = "1";
	    }
	    if (!isset($settings['enable_recaptcha_in_wp_login_form'])) {
		    $settings['enable_recaptcha_in_wp_login_form'] = "1";
	    }
	    if (!isset($settings['enable_recaptcha_in_wp_register_form'])) {
		    $settings['enable_recaptcha_in_wp_register_form'] = "1";
	    }
	    if (!isset($settings['recaptcha_title'])) {
		    $settings['recaptcha_title'] = "";
	    }
	    if (!isset($settings['recaptcha_theme'])) {
		    $settings['recaptcha_theme'] = "light";
	    }

        update_option( 'uwp_settings', $settings );

    }

}