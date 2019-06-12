<?php
function uwp_recaptcha_check_role() {
    if ( !is_user_logged_in() ) { // visitors
        return false;
    }

    global $current_user;
    $role = !empty( $current_user ) && isset( $current_user->roles[0] ) ? $current_user->roles[0] : '';

    if ( $role != '' && ((int)uwp_get_option('disable_recaptcha_role_' . $role, 0) == 1 || in_array($role, uwp_get_option('disable_recaptcha_role_for', array())) )) { // disable captcha
        return true;
    }
    else { // enable captcha
        return false;
    }
}

function uwp_recaptcha_display( $form ) {

    if ( uwp_recaptcha_check_role() ) { // disable captcha as per user role settings
        return;
    }

    $site_key = uwp_get_option('recaptcha_api_key', '');
    $secret_key = uwp_get_option('recaptcha_api_secret', '');
    $captcha_version = uwp_get_option( 'recaptcha_version', 'default' );

    if(!$form){
        $form = 'general';
    }

    if ( strlen( $site_key ) > 10 && strlen( $secret_key ) > 10 ) {

        $captcha_theme = uwp_get_option('recaptcha_theme', '');
        $captcha_title = uwp_get_option('recaptcha_title', '');
        $captcha_title = apply_filters( 'uwp_captcha_title', $captcha_title );
        $captcha_size = 'normal';

        if(wp_is_mobile()){
            $captcha_size = 'compact';
        }

        $captcha_size = apply_filters( 'uwp_captcha_size', $captcha_size );

        $div_id = 'uwp_captcha_' . $form;
        if(($form == 'wp_login' || $form == 'wp_register') && !wp_is_mobile()){
            if ( 'default' == uwp_get_option('recaptcha_version', 'default') ) {
                $from_width = 302;
            } else {
                $from_width = 320;
            }
            ?>
            <style type="text/css" media="screen">
                .login-action-login #loginform,
                .login-action-lostpassword #lostpasswordform,
                .login-action-register #registerform {
                    width: <?php echo $from_width; ?>px !important;
                }
                #login_error,
                .message {
                    width: <?php echo $from_width + 20; ?>px !important;
                }
                .login-action-login #loginform .gglcptch,
                .login-action-lostpassword #lostpasswordform .gglcptch,
                .login-action-register #registerform .gglcptch {
                    margin-bottom: 10px;
                }
            </style>
            <?php
        }
        ?>
        <div class="uwp-captcha uwp-captcha-<?php echo $form;?>" style="margin: 7px 0;clear: both;margin-bottom: 15px;">
            <?php if ( trim( $captcha_title ) != '' ) { ?>
                <label class="uwp-captcha-title"><?php _e( $captcha_title ) ;?></label>
            <?php } ?>

            <?php if ( $captcha_version == 'default' ) { ?>
                <div id="<?php echo $div_id;?>" class="uwp-captcha-render"></div>
                <script type="text/javascript">
                    try {
                        var <?php echo $div_id;?> = function() {
                            if ( ( typeof jQuery != 'undefined' && !jQuery('#<?php echo $div_id;?>').html() ) ) {
                                grecaptcha.render('<?php echo $div_id;?>', { 'sitekey' : '<?php echo $site_key;?>', 'theme' : '<?php echo $captcha_theme;?>', 'size' : '<?php echo $captcha_size;?>' });
                            }
                        }
                    } catch(err) {
                        console.log(err);
                    }
                </script>
            <?php } else if ( $captcha_version == 'v3' ) {
                    $api_url = sprintf( 'https://www.google.com/recaptcha/api.js?render=%s', $site_key );
                    echo '<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">';
                    echo '<script src="' . $api_url . '"></script>
                            <script>
                            if (typeof grecaptcha != \'undefined\') {
                              grecaptcha.ready(function() {
                                  grecaptcha.execute(\''. $site_key .'\', {action: \'uwp_captcha\'}).then(function(token) {
                                     document.getElementById(\'g-recaptcha-response\').value=token;
                                  });
                              });
                              }
                             </script>';
                ?>
            <?php } else { ?>
                <div id="<?php echo $div_id;?>" class="uwp-captcha-render"></div>
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

function uwp_recaptcha_check( $form ) {

    $secret_key = uwp_get_option('recaptcha_api_secret', '');
    $remote_addr = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP );
    $response = uwp_recaptcha_get_response( $secret_key, $remote_addr );
    $captcha_version = uwp_get_option( 'recaptcha_version', 'default' );
    $captcha_score = uwp_get_option( 'recaptcha_score', 0.5 );
    $result = '';
    $err_msg = __('<strong>ERROR</strong>: reCAPTCHA verification failed. Please try again.', 'uwp-recaptcha');

    $invalid_captcha = true;
    if ( isset( $response['success'] ) && $response['success'] ) {
        if('v3' ==  $captcha_version && $response['score'] <  $captcha_score && 'uwp_captcha' == $response['action']){
            $invalid_captcha = true;
        } else {
            $invalid_captcha = false;
        }
    }

    if ( $invalid_captcha ) {
        remove_action('authenticate', 'wp_authenticate_username_password', 20);
        if(isset($response['error-codes']) && !empty($response['error-codes'])){
            switch ($response['error-codes']){
                case 'missing-input-secret':
                case 'invalid-input-secret':
                    $err_msg = __('<strong>reCAPTCHA ERROR</strong>: The secret parameter is missing or invalid. Please try again.', 'uwp-recaptcha');
                    break;
                case 'missing-input-response':
                case 'invalid-input-response':
                    $err_msg = __('<strong>reCAPTCHA ERROR</strong>: The response parameter is missing or invalid. Please try again.', 'uwp-recaptcha');
                    break;
                case 'bad-request':
                    $err_msg = __('<strong>reCAPTCHA ERROR</strong>: The request is invalid. Please try again.', 'uwp-recaptcha');
                    break;
                case 'timeout-or-duplicate':
                    $err_msg = __('<strong>reCAPTCHA ERROR</strong>: The response is no longer valid: either is too old or has been used previously. Please try again.', 'uwp-recaptcha');
                    break;
            }
        }
        $error = new WP_Error();
        $error->add('invalid_captcha', $err_msg);
        $result = $error;
    }

    $result = apply_filters( 'uwp_recaptcha_check', $result, $form );

    return $result;
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

function uwp_recaptcha_enabled(){
    $site_key = uwp_get_option('recaptcha_api_key', '');
    $secret_key = uwp_get_option('recaptcha_api_secret', '');

    if ( !( strlen( $site_key ) > 10 && strlen( $secret_key ) > 10 ) ) {
        return false;
    }

    return true;
}

function uwp_recaptcha_get_response( $privatekey, $remote_ip ) {
    $args = array(
        'body' => array(
            'secret'   => $privatekey,
            'response' => stripslashes( esc_html( $_POST["g-recaptcha-response"] ) ),
            'remoteip' => $remote_ip,
        ),
        'sslverify' => false
    );
    $resp = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', $args );
    return json_decode( wp_remote_retrieve_body( $resp ), true );
}