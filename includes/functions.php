<?php
add_action( 'init', 'uwp_recaptcha_init', 0 );
function uwp_recaptcha_init() {

    if ( uwp_recaptcha_check_role() ) { // disable captcha as per user role settings
        return;
    }

    $enable_register_form = uwp_get_option('enable_recaptcha_in_register_form', false);
    $enable_login_form = uwp_get_option('enable_recaptcha_in_login_form', false);
    $enable_forgot_form = uwp_get_option('enable_recaptcha_in_forgot_form', false);
    $enable_account_form = uwp_get_option('enable_recaptcha_in_account_form', false);

    // registration form
    if ( $enable_register_form == '1' ) {
        add_action( 'uwp_template_fields', 'uwp_recaptcha_form_register' );
    }

    // login form
    if ( $enable_login_form == '1' ) {
        add_action( 'uwp_template_fields', 'uwp_recaptcha_form_login' );
    }

    // forgot form
    if ( $enable_forgot_form == '1' ) {
        add_action( 'uwp_template_fields', 'uwp_recaptcha_form_forgot' );
    }

    // account form
    if ( $enable_account_form == '1' ) {
        add_action( 'uwp_template_fields', 'uwp_recaptcha_form_account' );
    }

    do_action( 'uwp_recaptcha_init' );
}

function uwp_recaptcha_check_role() {
    if ( !is_user_logged_in() ) { // visitors
        return false;
    }

    global $current_user;
    $role = !empty( $current_user ) && isset( $current_user->roles[0] ) ? $current_user->roles[0] : '';

    if ( $role != '' && (int)uwp_get_option('uwp_recaptcha_role_' . $role, 0) == 1 ) { // disable captcha
        return true;
    }
    else { // enable captcha
        return false;
    }
}

function uwp_recaptcha_check_enabled( $type = '' ) {
    if ( ! empty( $type ) ) {
        $forms = array( $type );
    } else {
        $forms = apply_filters( 'uwp_recaptcha_available_in_forms', array( 
            'enable_recaptcha_in_wp_register_form',
            'enable_recaptcha_in_wp_login_form',
            'enable_recaptcha_in_wp_reset_pwd_form',
            'enable_recaptcha_in_register_form',
            'enable_recaptcha_in_login_form',
            'enable_recaptcha_in_forgot_form',
            'enable_recaptcha_in_account_form',
        ) );
    }

    $enabled = false;

    if ( !empty( $forms ) ) {
        foreach ( $forms as $form ) {
            if ( ! empty( $form ) && uwp_get_option( $form, false ) ) {
                $enabled = true;
                break;
            }
        }
    }

    return apply_filters( 'uwp_recaptcha_check_enabled', $enabled, $forms, $type );
}

add_filter('uwp_validate_result', 'uwp_recaptcha_validate', 10, 2);
function uwp_recaptcha_validate($result, $type) {

    $errors = new WP_Error();

    if ($type == 'register') {
        $enable_register_form = uwp_get_option('enable_recaptcha_in_register_form', false);
        if ( $enable_register_form != '1' ) {
            return $result;
        }
    } elseif ($type == 'login') {
        $enable_login_form = uwp_get_option('enable_recaptcha_in_login_form', false);
        if ( $enable_login_form != '1' ) {
            return $result;
        }
    } elseif ($type == 'forgot') {
        $enable_forgot_form = uwp_get_option('enable_recaptcha_in_forgot_form', false);
        if ( $enable_forgot_form != '1' ) {
            return $result;
        }
    } elseif ($type == 'account') {
        $enable_account_form = uwp_get_option('enable_recaptcha_in_account_form', false);
        if ( $enable_account_form != '1' ) {
            return $result;
        }
    } elseif ($type == 'frontend') {
        $enable_account_form = uwp_get_option('enable_recaptcha_in_frontend_form', false);
        if ( $enable_account_form != '1' ) {
            return $result;
        }
    } else {
        return $result;
    }

    if (is_wp_error($result)) {
        return $result;
    }

    $site_key = uwp_get_option('recaptcha_api_key', '');
    $secret_key = uwp_get_option('recaptcha_api_secret', '');
    $captcha_version = uwp_get_option( 'recaptcha_version', 'default' );

    if ( !( strlen( $site_key ) > 10 && strlen( $secret_key ) > 10 ) ) {
        return $result;
    }

    if ( $type ) {
        switch( $type ) {
            case 'register':
            case 'login':
            case 'forgot':
            case 'account':
            case 'frontend':
                $site_key = uwp_get_option('recaptcha_api_key', '');
                $secret_key = uwp_get_option('recaptcha_api_secret', '');
                $captcha_version = uwp_get_option( 'recaptcha_version', 'default' );

                if ( !( strlen( $site_key ) > 10 && strlen( $secret_key ) > 10 ) ) {
                    if (current_user_can('manage_options')) {
                        $plugin_settings_link = admin_url( '/admin.php?page=uwp_recaptcha' );
                        $err_msg = sprintf( __( 'To use reCAPTCHA you must get an API key from  <a target="_blank" href="https://www.google.com/recaptcha/admin">here</a> and enter keys in the plugin settings page at <a target="_blank" href="%s">here</a>' ), $plugin_settings_link );
                    } else {
                        $err_msg = __('<strong>Error</strong>: Something went wrong. Please contact site admin.', 'uwp-recaptcha');
                    }

                    if (is_wp_error($result)) {
                        $result->add('invalid_captcha', $err_msg);
                    } else {
                        $errors->add('invalid_captcha', $err_msg);
                        $result = $errors;
                    }
                    break;
                }

                $reCaptcha = new ReCaptcha( $secret_key );

                $recaptcha_value = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : '';
                $response = $reCaptcha->verifyResponse( $_SERVER['REMOTE_ADDR'], $recaptcha_value );

                $invalid_captcha = !empty( $response ) && isset( $response->success ) && $response->success ? false : true;

                if ( $invalid_captcha ) {
                    $err_msg = __('<strong>Error</strong>: You have entered an incorrect CAPTCHA value.', 'uwp-recaptcha');
                    if (is_wp_error($result)) {
                        $result->add('invalid_captcha', $err_msg);
                    } else {
                        $errors->add('invalid_captcha', $err_msg);
                        $result = $errors;
                    }
                } else {
                    //do nothing
                }
                break;
        }
    }

    return $result;
}

function uwp_recaptcha_form_register($type) {
    if ($type == 'register') {
        uwp_recaptcha_display( 'register' );
    }
}

function uwp_recaptcha_form_login($type) {
    if ($type == 'login') {
        uwp_recaptcha_display( 'login' );
    }
}

function uwp_recaptcha_form_forgot($type) {
    if ($type == 'forgot') {
        uwp_recaptcha_display( 'forgot' );
    }
}

function uwp_recaptcha_form_account($type) {
    if ($type == 'account') {
        uwp_recaptcha_display( 'account' );
    }
}

function uwp_recaptcha_display( $form ) {

    $site_key = uwp_get_option('recaptcha_api_key', '');
    $secret_key = uwp_get_option('recaptcha_api_secret', '');
    $captcha_version = uwp_get_option( 'recaptcha_version', 'default' );

    if ( strlen( $site_key ) > 10 && strlen( $secret_key ) > 10 ) {

        $captcha_theme = uwp_get_option('recaptcha_theme', '');
        $captcha_title = uwp_get_option('recaptcha_title', '');

        $captcha_title = apply_filters( 'uwp_captcha_title', $captcha_title );

        $div_id = 'uwp_captcha_' . $form;
        ?>
        <div class="uwp-captcha uwp-captcha-<?php echo $form;?>" style="margin: 7px 0;clear: both;margin-bottom: 15px;">
            <?php if ( trim( $captcha_title ) != '' ) { ?>
                <label class="uwp-captcha-title"><?php _e( $captcha_title ) ;?></label>
            <?php } ?>

            <div id="<?php echo $div_id;?>" class="uwp-captcha-render"></div>

            <?php if ( $captcha_version != 'invisible' ) { ?>
                <script type="text/javascript">
                    try {
                        var <?php echo $div_id;?> = function() {
                            if ( ( typeof jQuery != 'undefined' && !jQuery('#<?php echo $div_id;?>').html() ) ) {
                                grecaptcha.render('<?php echo $div_id;?>', { 'sitekey' : '<?php echo $site_key;?>', 'theme' : '<?php echo $captcha_theme;?>' });
                            }
                        }
                    } catch(err) {
                        console.log(err);
                    }
                </script>
            <?php } else { ?>
                <script type="text/javascript">
                     try {
                        var <?php echo $div_id;?> = function() {
                            if (typeof grecaptcha == 'undefined') {
                                var to;
                                clearInterval(to);
                                to = setInterval(function(){
                                    if ( typeof grecaptcha != 'undefined' ) {
                                        clearInterval(to);
                                        for (var i = 0; i < document.forms.length; ++i) {
                                            var form = document.forms[i];
                                            var holder = form.querySelector('.uwp-captcha-render');
                                            if (null === holder) {
                                                continue;
                                            }
                                            (function(frm) {
                                                jQuery(holder).html('');
                                                if ( !jQuery(holder).html() ) {
                                                    var holderId = grecaptcha.render(holder, {
                                                        'sitekey': '<?php echo $site_key;?>',
                                                        'size': 'invisible',
                                                        'badge': 'bottomright', // possible values: bottomright, bottomleft, inline
                                                        'callback': function (recaptchaToken) {
                                                            HTMLFormElement.prototype.submit.call(frm);
                                                        }
                                                    });
                                                    frm.onsubmit = function (evt) {
                                                        evt.preventDefault();
                                                        grecaptcha.execute(holderId);
                                                    };
                                                }
                                            })(form);
                                        }
                                    }
                                }, 50);
                            } else {
                                for (var i = 0; i < document.forms.length; ++i) {
                                    var form = document.forms[i];
                                    var holder = form.querySelector('.uwp-captcha-render');
                                    if (null === holder) {
                                        continue;
                                    }
                                    (function(frm) {
                                        if ( !jQuery(holder).html() ) {
                                            var holderId = grecaptcha.render(holder, {
                                                'sitekey': '<?php echo $site_key;?>',
                                                'size': 'invisible',
                                                'badge': 'bottomright', // possible values: bottomright, bottomleft, inline
                                                'callback': function (recaptchaToken) {
                                                    HTMLFormElement.prototype.submit.call(frm);
                                                }
                                            });
                                            frm.onsubmit = function (evt) {
                                                evt.preventDefault();
                                                grecaptcha.execute(holderId);
                                            };
                                        }
                                    })(form);
                                }
                            }
                        }
                    } catch(err) {
                        console.log(err);
                    }
                </script>
            <?php } ?>
            <?php
            ?>
        </div>
        <?php
    }
}

function uwp_recaptcha_language( $default = 'en' ) {
    $current_lang = get_locale();

    $current_lang = $current_lang != '' ? $current_lang : $default;

    $special_lang = array( 'zh-HK', 'zh-CN', 'zh-TW', 'en-GB', 'fr-CA', 'de-AT', 'de-CH', 'pt-BR', 'pt-PT', 'es-419' );
    if ( !in_array( $current_lang, $special_lang ) ) {
        $current_lang = substr( $current_lang, 0, 2 );
    }

    $language = apply_filters( 'uwp_recaptcha_api_language', $current_lang );

    return $language;
}

function uwp_recaptcha_key_notices() {

    $site_key = uwp_get_option('recaptcha_api_key', false);
    $secret_key = uwp_get_option('recaptcha_api_secret', false);

    if (empty($site_key) && empty($secret_key)) {
        echo '<div class="notice-error notice is-dismissible"><p><strong>' . sprintf(__('UsersWP ReCaptcha addon: API Key and API Secret not set. %sclick here%s to set one.', 'uwp-recaptcha'), '<a href=\'' . admin_url('admin.php?page=uwp_recaptcha') . '\'>', '</a>') . '</strong></p></div>';
    } elseif (empty($site_key)) {
        echo '<div class="notice-error notice is-dismissible"><p><strong>' . sprintf(__('UsersWP ReCaptcha addon: API Key not set. %sclick here%s to set one.', 'uwp-recaptcha'), '<a href=\'' . admin_url('admin.php?page=uwp_recaptcha') . '\'>', '</a>') . '</strong></p></div>';
    } elseif (empty($secret_key)) {
        echo '<div class="notice-error notice is-dismissible"><p><strong>' . sprintf(__('UsersWP ReCaptcha addon: API Secret not set. %sclick here%s to set one.', 'uwp-recaptcha'), '<a href=\'' . admin_url('admin.php?page=uwp_recaptcha') . '\'>', '</a>') . '</strong></p></div>';
    }

}
add_action( 'admin_notices', 'uwp_recaptcha_key_notices' );