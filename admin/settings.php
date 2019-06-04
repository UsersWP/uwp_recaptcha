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
                'id' => 'recaptcha_version',
                'name' => __( 'ReCaptcha version', 'uwp-recaptcha' ),
                'desc' => __( 'Select the ReCaptcha version. <b style="color: red;">Heads Up! V2, V3 and invisible recaptcha has different keys. So use keys based on version you select here.</b>', 'uwp-recaptcha' ),
                'type' => 'select',
                'options' => uwp_recpatcha_version_options(),
                'chosen' => true,
                'placeholder' => __( 'Select Option', 'uwp-recaptcha' ),
                'class' => 'uwp_label_block',
                'desc_tip' => true,
            ),
            array(
                'id' => 'recaptcha_api_key',
                'name' => __( 'Google ReCaptcha API Key', 'uwp-recaptcha' ),
                'desc' => __( 'Enter Re-Captcha site key that you get after site registration at <a target="_blank" href="https://www.google.com/recaptcha/admin#list">here</a>. Recaptcha V2 and V3 has different API key.', 'uwp-recaptcha' ),
                'type' => 'text',
                'size' => 'regular',
                'placeholder' => __( 'Enter Google ReCaptcha API Key', 'uwp-recaptcha' ),
                'desc_tip' => true,
            ),
            array(
                'id' => 'recaptcha_api_secret',
                'name' => __( 'Google ReCaptcha API Secret', 'uwp-recaptcha' ),
                'desc' => __( 'Enter Re-Captcha secret key that you get after site registration at <a target="_blank" href="https://www.google.com/recaptcha/admin#list">here</a>. Recaptcha V2 and V3 has different API secret', 'uwp-recaptcha' ),
                'type' => 'text',
                'size' => 'regular',
                'placeholder' => __( 'Enter Google ReCaptcha API Secret', 'uwp-recaptcha' ),
                'desc_tip' => true,
            ),
            array(
                'id' => 'recaptcha_score',
                'name' => __( 'Minimum verification score', 'uwp-recaptcha' ),
                'desc' => __( 'Set the minimum verification score from 0 to 1 (for Recaptcha V3 only). See more <a target="_blank" href="https://developers.google.com/recaptcha/docs/v3#score">here</a>', 'uwp-recaptcha' ),
                'type' => 'number',
                'size' => 'regular',
                'default' => 0.5,
                'desc_tip' => true,
                'custom_attributes' => array(
                    'max' => 1,
                    'min' => 0,
                    'step' => 0.1,
                ),
            ),
            array(
                'id'   => 'enable_recaptcha_in_register_form',
                'name' => __( 'Enable ReCaptcha in', 'uwp-recaptcha' ),
                'desc' => __( 'UsersWP Register Form', 'uwp-recaptcha' ),
                'type' => 'checkbox',
                'default'  => '1',
                'class' => 'uwp_label_inline',
            ),
            array(
                'id'   => 'enable_recaptcha_in_login_form',
                'name' => '',
                'desc' => __( 'UsersWP Login Form', 'uwp-recaptcha' ),
                'type' => 'checkbox',
                'default'  => '1',
                'class' => 'uwp_label_inline',
            ),
            array(
                'id'   => 'enable_recaptcha_in_forgot_form',
                'name' => '',
                'desc' => __( 'UsersWP Forgot Form', 'uwp-recaptcha' ),
                'type' => 'checkbox',
                'default'  => '1',
                'class' => 'uwp_label_inline',
            ),
            array(
                'id'   => 'enable_recaptcha_in_account_form',
                'name' => '',
                'desc' => __( 'UsersWP Account Form', 'uwp-recaptcha' ),
                'type' => 'checkbox',
                'default'  => '1',
                'class' => 'uwp_label_inline',
            ),
            array(
                'id'   => 'enable_recaptcha_in_wp_login_form',
                'name' => '',
                'desc' => __( 'WordPress Login Form', 'uwp-recaptcha' ),
                'type' => 'checkbox',
                'default'  => '1',
                'class' => 'uwp_label_inline',
            ),
            array(
                'id'   => 'enable_recaptcha_in_wp_register_form',
                'name' => '',
                'desc' => __( 'WordPress Registeration Form', 'uwp-recaptcha' ),
                'type' => 'checkbox',
                'default'  => '1',
                'class' => 'uwp_label_inline',
            ),

        ));

        $count = 0;

        $roles = get_editable_roles();

        if(count($roles) > 0){
            $role_options = array();
            foreach ( $roles as $role => $data ) {
                $count++;
                $role_options[$role] = $data['name'];
            }

            $settings['disable_recaptcha_role_for'] = array(
                'id' => 'disable_recaptcha_role_for',
                'name' => __( 'Disable reCAPTCHA for', 'uwp-recaptcha' ),
                'desc' => __( 'Select the roles to disable ReCaptcha for.', 'uwp-recaptcha' ),
                'type' => 'multiselect',
                'options' => $role_options,
                'chosen' => true,
                'placeholder' => __( 'Select Roles', 'uwp-recaptcha' ),
                'class' => 'uwp_label_block',
                'desc_tip' => true,
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
        'v3' =>  __('ReCaptcha V3', 'uwp-recaptcha'),
        'default' =>  __('ReCaptcha V2', 'uwp-recaptcha'),
        'invisible' =>  __('Invisible ReCaptcha', 'uwp-recaptcha'),
    );

    $recaptcha_version_options = apply_filters('uwp_recaptcha_version_options', $recaptcha_version_options);

    return $recaptcha_version_options;
}