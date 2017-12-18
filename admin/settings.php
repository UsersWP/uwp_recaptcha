<?php
/**
 * Modifies the settings form title.
 *
 * @since       1.0.0
 * @package     userswp
 *
 * @param       string      $title         Original title.
 * @param       string      $page          admin.php?page=uwp_xxx.
 * @param       string      $active_tab    active tab in that settings page.
 *
 * @return      string      Form title.
 */
function uwp_recaptcha_display_form_title($title, $page, $active_tab) {
    if ($page == 'uwp_recaptcha' && $active_tab == 'main') {
        $title = __('ReCaptcha Settings', 'uwp-recaptcha');
    }
    return $title;
}
add_filter('uwp_display_form_title', 'uwp_recaptcha_display_form_title', 10, 3);

add_action('uwp_recaptcha_settings_main_tab_content', 'uwp_recaptcha_main_tab_content', 10, 1);
function uwp_recaptcha_main_tab_content($form) {
    echo $form;
}

add_action('uwp_admin_sub_menus', 'uwp_add_admin_recaptcha_sub_menu', 10, 1);
/**
 * Adds the current userswp addon settings page menu as submenu.
 *
 * @since       1.0.0
 * @package     userswp
 *
 * @param       callable   $settings_page    The function to be called to output the content for this page.
 *
 * @return      void
 */
function uwp_add_admin_recaptcha_sub_menu($settings_page) {

    add_submenu_page(
        "userswp",
        __( 'ReCaptcha', 'uwp-recaptcha' ),
        __( 'ReCaptcha', 'uwp-recaptcha' ),
        'manage_options',
        'uwp_recaptcha',
        $settings_page
    );

}

add_filter('uwp_settings_tabs', 'uwp_add_recaptcha_tab');
/**
 * Adds settings tabs for the current userswp addon.
 *
 * @since       1.0.0
 * @package     userswp
 *
 * @param       array     $tabs    Existing tabs array.
 *
 * @return      array     Tabs array.
 */
function uwp_add_recaptcha_tab($tabs) {
    $tabs['uwp_recaptcha'] = array(
        'main' => __( 'ReCaptcha', 'uwp-recaptcha' ),
    );
    return $tabs;
}

add_filter('uwp_registered_settings', 'uwp_add_recaptcha_settings');
/**
 * Registers form fields for the current userswp addon settings page.
 *
 * @since       1.0.0
 * @package     userswp
 *
 * @param       array     $uwp_settings    Existing settings array.
 *
 * @return      array     Settings array.
 */
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
        'recaptcha_version' => array(
            'id' => 'recaptcha_version',
            'name' => __( 'ReCaptcha version', 'userswp' ),
            'desc' => __( 'Select the ReCaptcha version. <b style="color: red;">Heads Up! V2 keys will not work with invisible recaptcha, you will have to create new ones.</b>', 'userswp' ),
            'type' => 'select',
            'options' => uwp_recpatcha_version_options(),
            'chosen' => true,
            'placeholder' => __( 'Select Option', 'userswp' ),
            'class' => 'uwp_label_block',
        ),
        /*'enable_recaptcha_in_wp_register_form' => array(
            'id'   => 'enable_recaptcha_in_wp_register_form',
            'name' => __( 'Enable ReCaptcha in', 'uwp-recaptcha' ),
            'desc' => __( 'WordPress Register Form', 'uwp-recaptcha' ),
            'type' => 'checkbox',
            'std'  => '1',
            'class' => 'uwp_label_inline',
        ),
        'enable_recaptcha_in_wp_login_form' => array(
            'id'   => 'enable_recaptcha_in_wp_login_form',
            'name' => '',
            'desc' => __( 'WordPress Login Form', 'uwp-recaptcha' ),
            'type' => 'checkbox',
            'std'  => '1',
            'class' => 'uwp_label_inline',
        ),
        'enable_recaptcha_in_wp_reset_pwd_form' => array(
            'id'   => 'enable_recaptcha_in_wp_reset_pwd_form',
            'name' => '',
            'desc' => __( 'WordPress Reset Password Form', 'uwp-recaptcha' ),
            'type' => 'checkbox',
            'std'  => '1',
            'class' => 'uwp_label_inline',
        ),*/
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

    $options = apply_filters('uwp_settings_recaptcha_enable_for', $options);

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


function uwp_recpatcha_version_options() {
    $recaptcha_version_options = array(
            'default' =>  __('ReCaptcha V2', 'uwp-recaptcha'),
            'invisible' =>  __('Invisible ReCaptcha', 'uwp-recaptcha'),
        );

    $recaptcha_version_options = apply_filters('uwp_recaptcha_version_options', $recaptcha_version_options);

    return $recaptcha_version_options;
}