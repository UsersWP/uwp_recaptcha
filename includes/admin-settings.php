<?php
add_filter('uwp_display_form_title', 'uwp_recaptcha_display_form_title', 10, 3);
function uwp_recaptcha_display_form_title($title, $page, $active_tab) {
    if ($page == 'uwp_recaptcha' && $active_tab == 'main') {
        $title = __('ReCaptcha Settings', 'uwp-recaptcha');
    }
    return $title;
}

add_action('uwp_recaptcha_settings_main_tab_content', 'uwp_recaptcha_main_tab_content', 10, 1);
function uwp_recaptcha_main_tab_content($form) {
    echo $form;
}

add_action('uwp_admin_sub_menus', 'uwp_add_admin_recaptcha_sub_menu', 10, 1);
function uwp_add_admin_recaptcha_sub_menu($settings_page) {

    add_submenu_page(
        "userswp",
        "ReCaptcha",
        "ReCaptcha",
        'manage_options',
        'uwp_recaptcha',
        $settings_page
    );

}

add_filter('uwp_settings_tabs', 'uwp_add_recaptcha_tab');
function uwp_add_recaptcha_tab($tabs) {
    $tabs['uwp_recaptcha'] = array(
        'main' => __( 'ReCaptcha', 'uwp-recaptcha' ),
    );
    return $tabs;
}

add_filter('uwp_registered_settings', 'uwp_add_recaptcha_settings');
function uwp_add_recaptcha_settings($uwp_settings) {

    $options = array(
        'recaptcha_api_key' => array(
            'id' => 'recaptcha_api_key',
            'name' => __( 'Google ReCaptcha API Key', 'uwp-recaptcha' ),
            'desc' => __( 'Enter Re-Captcha site key that you get after site registration at <a target="_blank" href="https://www.google.com/recaptcha/admin#list">here</a>.', 'uwp-recaptcha' ),
            'type' => 'text',
            'size' => 'regular',
            'placeholder' => __( 'Enter Google ReCaptcha API Key', 'uwp-recaptcha' )
        ),
        'recaptcha_api_secret' => array(
            'id' => 'recaptcha_api_secret',
            'name' => __( 'Google ReCaptcha API Secret', 'uwp-recaptcha' ),
            'desc' => __( 'Enter Re-Captcha secret key that you get after site registration at <a target="_blank" href="https://www.google.com/recaptcha/admin#list">here</a>', 'uwp-recaptcha' ),
            'type' => 'text',
            'size' => 'regular',
            'placeholder' => __( 'Enter Google ReCaptcha API Secret', 'uwp-recaptcha' )
        ),
        'enable_recaptcha_in_register_form' => array(
            'id'   => 'enable_recaptcha_in_register_form',
            'name' => __( 'Enable ReCaptcha in', 'uwp-recaptcha' ),
            'desc' => __( 'UsersWP Register Form.', 'uwp-recaptcha' ),
            'type' => 'checkbox',
            'std'  => '1',
            'class' => 'uwp_label_inline',
        ),
        'enable_recaptcha_in_login_form' => array(
            'id'   => 'enable_recaptcha_in_login_form',
            'name' => '',
            'desc' => __( 'UsersWP Login Form.', 'uwp-recaptcha' ),
            'type' => 'checkbox',
            'std'  => '1',
            'class' => 'uwp_label_inline',
        ),
        'enable_recaptcha_in_forgot_form' => array(
            'id'   => 'enable_recaptcha_in_forgot_form',
            'name' => '',
            'desc' => __( 'UsersWP Forgot Form.', 'uwp-recaptcha' ),
            'type' => 'checkbox',
            'std'  => '1',
            'class' => 'uwp_label_inline',
        ),
        'enable_recaptcha_in_account_form' => array(
            'id'   => 'enable_recaptcha_in_account_form',
            'name' => '',
            'desc' => __( 'UsersWP Account Form.', 'uwp-recaptcha' ),
            'type' => 'checkbox',
            'std'  => '1',
            'class' => 'uwp_label_inline',
        ),
    );

    $count = 0;
    $roles = get_editable_roles();
    foreach ( $roles as $role => $data ) {
        $count++;
        $options['disable_recaptcha_role_' . $role] = array(
            'id' => 'disable_recaptcha_role_' . $role,
            'name' => ( $count == 1 ? __( 'Disable Google reCAPTCHA for', 'uwp-recaptcha' ) : '' ),
            'desc' => __( $data['name'], 'uwp-recaptcha' ),
            'std' => '0',
            'class' => 'uwp_label_inline',
            'type' => 'checkbox',
        );
    }

    $options['recaptcha_title'] = array(
        'id' => 'recaptcha_title',
        'name' => __( 'Captcha Title', 'uwp-recaptcha' ),
        'type' => 'text',
        'std' => '',
        'desc' 	=> __( 'Captcha title to be displayed above captcha code, leave blank to hide.', 'uwp-recaptcha' ),
    );
    $options['recaptcha_theme'] = array(
        'id' 		=> 'recaptcha_theme',
        'name' => __( 'Captcha Theme', 'uwp-recaptcha' ),
        'desc' 		=> __( 'Select color theme of captcha widget. <a target="_blank" href="https://developers.google.com/recaptcha/docs/display#render_param">Learn more</a>', 'uwp-recaptcha' ),
        'type' 		=> 'select',
        'options' => array(
            'light' => __( 'Light', 'uwp-recaptcha' ),
            'dark' => __( 'Dark', 'uwp-recaptcha' ),
        )
    );

    $uwp_settings['uwp_recaptcha'] = array(
        'main' => apply_filters( 'uwp_settings_recaptcha', $options),
    );

    return $uwp_settings;
}