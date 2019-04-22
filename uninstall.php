<?php
/**
 * Uninstall UsersWP - Recaptcha
 *
 * Uninstalling UsersWP - Recaptcha deletes the plugin options.
 *
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = get_option('uwp_settings', array());
if ( 1 == $settings[ 'uninstall_recaptcha_data' ] ) {
    $settings = get_option('uwp_settings', array());
    
    $options = array(
        'recaptcha_api_key',
        'recaptcha_api_secret',
        'recaptcha_version',
        'enable_recaptcha_in_register_form',
        'enable_recaptcha_in_login_form',
        'enable_recaptcha_in_forgot_form',
        'enable_recaptcha_in_account_form',
        'enable_recaptcha_in_frontend_form',
        'enable_recaptcha_in_wp_login_form',
        'enable_recaptcha_in_wp_register_form',
        'disable_recaptcha_role_for',
        'recaptcha_title',
        'recaptcha_theme',
        'uninstall_recaptcha_data',
    );

    $options = apply_filters('uwp_recaptcha_uninstall_data', $options);
    
    if ( !empty( $options ) ) {
        foreach ( $options as $option ) {
            unset( $settings[$option] );
        }
    }

    update_option('uwp_settings', $settings);
}