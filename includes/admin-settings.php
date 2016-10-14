<?php
apply_filters('uwp_registered_settings', 'uwp_add_recaptcha_settings');
function uwp_add_recaptcha_settings($uwp_settings) {
    $uwp_settings['recaptcha'] = apply_filters( 'uwp_settings_recaptcha',
        array(
            'recaptcha_api_key' => array(
                'id' => 'recaptcha_api_key',
                'name' => __( 'Google ReCaptcha API Key', 'users-wp' ),
                'desc' => __( 'Enter Re-Captcha site key that you get after site registration at <a target="_blank" href="https://www.google.com/recaptcha/admin#list">here</a>.', 'users-wp' ),
                'type' => 'text',
                'size' => 'regular',
                'placeholder' => __( 'Enter Google ReCaptcha API Key', 'users-wp' )
            ),
            'recaptcha_api_secret' => array(
                'id' => 'recaptcha_api_secret',
                'name' => __( 'Google ReCaptcha API Secret', 'users-wp' ),
                'desc' => __( 'Enter Re-Captcha secret key that you get after site registration at <a target="_blank" href="https://www.google.com/recaptcha/admin#list">here</a>', 'users-wp' ),
                'type' => 'text',
                'size' => 'regular',
                'placeholder' => __( 'Enter Google ReCaptcha API Secret', 'users-wp' )
            ),
            'enable_recaptcha_in_register_form' => array(
                'id'   => 'enable_recaptcha_in_register_form',
                'name' => __( 'Enable ReCaptcha in Register Form', 'users-wp' ),
                'desc' => __( 'Enable ReCaptcha in Register Form.', 'users-wp' ),
                'type' => 'checkbox',
                'std'  => '1'
            ),
            'enable_recaptcha_in_login_form' => array(
                'id'   => 'enable_recaptcha_in_login_form',
                'name' => __( 'Enable ReCaptcha in Login Form', 'users-wp' ),
                'desc' => __( 'Enable ReCaptcha in Login Form.', 'users-wp' ),
                'type' => 'checkbox',
                'std'  => '1'
            ),
            'enable_recaptcha_in_forgot_form' => array(
                'id'   => 'enable_recaptcha_in_forgot_form',
                'name' => __( 'Enable ReCaptcha in Forgot Form', 'users-wp' ),
                'desc' => __( 'Enable ReCaptcha in Forgot Form.', 'users-wp' ),
                'type' => 'checkbox',
                'std'  => '1'
            ),
            'enable_recaptcha_in_account_form' => array(
                'id'   => 'enable_recaptcha_in_account_form',
                'name' => __( 'Enable ReCaptcha in Account Form', 'users-wp' ),
                'desc' => __( 'Enable ReCaptcha in Account Form.', 'users-wp' ),
                'type' => 'checkbox',
                'std'  => '1'
            ),
        )
    );

    return $uwp_settings;
}

apply_filters('uwp_settings_tabs', 'uwp_add_recaptcha_tab');
function uwp_add_recaptcha_tab($tabs) {
    $tabs['recaptcha']   = __( 'ReCaptcha', 'users-wp' );
    return $tabs;
}