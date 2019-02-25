<?php
add_filter( 'uwp_get_sections_uwp-addons','uwp_recaptcha_addons_get_sections' );

function uwp_recaptcha_addons_get_sections( $sections ) {

    $sections['uwp_recaptcha'] =  __('ReCaptcha', 'uwp-recaptcha');

    return $sections;
}

add_filter( 'uwp_get_settings_uwp-addons','uwp_recaptcha_addons_get_settings',10,2 );

function uwp_recaptcha_addons_get_settings( $settings, $current_section ) {

    if( !empty( $current_section ) && 'uwp_recaptcha' === $current_section ) {

        $settings = apply_filters( 'uwp_addon_recaptcha_options', array(
            array(
                'title' => __('ReCaptcha Settings', 'uwp-friends'),
                'type' => 'title',
                'desc' => '',
                'id' => 'addons_recaptcha_settings_options',
                'desc_tip' => false,
            ),
            array(
                'id' => 'recaptcha_api_key',
                'name' => __( 'Google ReCaptcha API Key', 'uwp-recaptcha' ),
                'desc' => __( 'Enter Re-Captcha site key that you get after site registration at <a target="_blank" href="https://www.google.com/recaptcha/admin#list">here</a>.', 'uwp-recaptcha' ),
                'type' => 'text',
                'size' => 'regular',
                'placeholder' => __( 'Enter Google ReCaptcha API Key', 'uwp-recaptcha' ),
                'desc_tip' => true,
            ),
            array(
                'id' => 'recaptcha_api_secret',
                'name' => __( 'Google ReCaptcha API Secret', 'uwp-recaptcha' ),
                'desc' => __( 'Enter Re-Captcha secret key that you get after site registration at <a target="_blank" href="https://www.google.com/recaptcha/admin#list">here</a>', 'uwp-recaptcha' ),
                'type' => 'text',
                'size' => 'regular',
                'placeholder' => __( 'Enter Google ReCaptcha API Secret', 'uwp-recaptcha' ),
                'desc_tip' => true,
            ),
            array(
                'id' => 'recaptcha_version',
                'name' => __( 'ReCaptcha version', 'uwp-recaptcha' ),
                'desc' => __( 'Select the ReCaptcha version. <b style="color: red;">Heads Up! V2 keys will not work with invisible recaptcha, you will have to create new ones.</b>', 'uwp-recaptcha' ),
                'type' => 'select',
                'options' => uwp_recpatcha_version_options(),
                'chosen' => true,
                'placeholder' => __( 'Select Option', 'uwp-recaptcha' ),
                'class' => 'uwp_label_block',
                'desc_tip' => true,
            ),
            array(
                'id'   => 'enable_recaptcha_in_register_form',
                'name' => __( 'Enable ReCaptcha in', 'uwp-recaptcha' ),
                'desc' => __( 'UsersWP Register Form.', 'uwp-recaptcha' ),
                'type' => 'checkbox',
                'default'  => '1',
                'class' => 'uwp_label_inline',
            ),
            array(
                'id'   => 'enable_recaptcha_in_login_form',
                'name' => '',
                'desc' => __( 'UsersWP Login Form.', 'uwp-recaptcha' ),
                'type' => 'checkbox',
                'default'  => '1',
                'class' => 'uwp_label_inline',
            ),
            array(
                'id'   => 'enable_recaptcha_in_forgot_form',
                'name' => '',
                'desc' => __( 'UsersWP Forgot Form.', 'uwp-recaptcha' ),
                'type' => 'checkbox',
                'default'  => '1',
                'class' => 'uwp_label_inline',
            ),
            array(
                'id'   => 'enable_recaptcha_in_account_form',
                'name' => '',
                'desc' => __( 'UsersWP Account Form.', 'uwp-recaptcha' ),
                'type' => 'checkbox',
                'default'  => '1',
                'class' => 'uwp_label_inline',
            ),

        ));

        $count = 0;

        $roles = get_editable_roles();

        foreach ( $roles as $role => $data ) {
            $count++;
            $settings['disable_recaptcha_role_' . $role] = array(
                'id' => 'disable_recaptcha_role_' . $role,
                'name' => ( $count == 1 ? __( 'Disable Google reCAPTCHA for', 'uwp-recaptcha' ) : '' ),
                'desc' => __( $data['name'], 'uwp-recaptcha' ),
                'default' => '0',
                'type' => 'checkbox',
            );
        }

        $settings['recaptcha_title' ] = array(
            'id' => 'recaptcha_title',
            'name' => __( 'Captcha Title', 'uwp-recaptcha' ),
            'desc' => __( 'Captcha title to be displayed above captcha code, leave blank to hide.', 'uwp-recaptcha' ),
            'type' => 'text',
            'size' => 'regular',
            'desc_tip' => true,
        );

        $settings['recaptcha_theme']  = array(
            'id' => 'recaptcha_theme',
            'name' => __( 'ReCaptcha Theme', 'uwp-recaptcha' ),
            'desc' => sprintf(__( 'Select color theme of captcha widget. %sLearn more%s', 'uwp-recaptcha' ), '<a target="_blank" href="https://developers.google.com/recaptcha/docs/display#render_param">', '</a>'),
            'type' => 'select',
            'default' => 'light',
            'options' => array(
                'light' => __( 'Light', 'uwp-recaptcha' ),
                'dark' => __( 'Dark', 'uwp-recaptcha' ),
            ),
            'desc_tip' => true,
        );

        $settings[] = array('type' => 'sectionend', 'id' => 'addons_recaptcha_settings_options');

    }

    return $settings;
}

add_filter( 'uwp_get_settings_uninstall','uwp_recaptcha_settings_uninstall' );

function uwp_recaptcha_settings_uninstall( $settings ) {

    $settings[] = array(
        'name'     => __( 'UsersWP - Recaptcha', 'uwp-recaptcha' ),
        'desc'     => __( 'Remove all data when deleted?', 'uwp-recaptcha' ),
        'id'       => 'uninstall_recaptcha_data',
        'type'     => 'checkbox',
    );

    return $settings;
}

function uwp_recpatcha_version_options() {

    $recaptcha_version_options = array(
        'default' =>  __('ReCaptcha V2', 'uwp-recaptcha'),
        'invisible' =>  __('Invisible ReCaptcha', 'uwp-recaptcha'),
    );

    $recaptcha_version_options = apply_filters('uwp_recaptcha_version_options', $recaptcha_version_options);

    return $recaptcha_version_options;
}