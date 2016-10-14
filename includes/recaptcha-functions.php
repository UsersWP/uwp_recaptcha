<?php
function uwp_recaptcha_init( $admin=false, $admin_ajax = false ) {

    global $uwp_options;

    $admin = is_admin();
    $admin_ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX );

    if ( uwp_recaptcha_check_role() ) { // disable captcha as per user role settings
        return;
    }

    $load_css = false;
    // registration form
    if ( !$admin && $uwp_options['enable_recaptcha_in_register_form'] == '1' ) {
        add_action( 'uwp_template_fields', 'uwp_recaptcha_form' );
        $load_css = true;
    }

    // login form
    if ( !$admin && $uwp_options['enable_recaptcha_in_login_form'] == '1' ) {
        add_action( 'uwp_template_fields', 'uwp_recaptcha_form' );
        $load_css = true;
    }

    // forgot form
    if ( !$admin && $uwp_options['enable_recaptcha_in_forgot_form'] == '1' ) {
        add_action( 'uwp_template_fields', 'uwp_recaptcha_form' );
        $load_css = true;
    }

    // account form
    if ( !$admin && $uwp_options['enable_recaptcha_in_account_form'] == '1' ) {
        add_action( 'uwp_template_fields', 'uwp_recaptcha_form' );
        $load_css = true;
    }


    if ( $load_css ) {
        //todo: fix this
        //wp_register_style( 'uwp-captcha-style', GEODIR_RECAPTCHA_PLUGIN_URL . '/css/uwp-captcha-style.css', array(), GEODIR_RECAPTCHA_VERSION);
        //wp_enqueue_style( 'uwp-captcha-style' );
    }

    add_action( 'wp_ajax_uwp_recaptcha_check', 'uwp_recaptcha_ajax_check' );
    add_action( 'wp_ajax_nopriv_uwp_recaptcha_check', 'uwp_recaptcha_ajax_check' );


    do_action( 'uwp_recaptcha_init' );
}

function uwp_recaptcha_check_role() {
    if ( !is_user_logged_in() ) { // visitors
        return false;
    }

    global $current_user;
    $role = !empty( $current_user ) && isset( $current_user->roles[0] ) ? $current_user->roles[0] : '';

    if ( $role != '' && (int)get_option( 'uwp_recaptcha_role_' . $role ) == 1 ) { // disable captcha
        return true;
    }
    else { // enable captcha
        return false;
    }
}


function uwp_recaptcha_ajax_check() {

    global $uwp_options;
    $uwp_form = isset( $_POST['uwp_form'] ) ? $_POST['uwp_form'] : '';

    $return = array();
    $return['success'] = false;
    $return['error'] = __( 'ERROR: You have entered an incorrect CAPTCHA value.', 'geodir-recaptcha' );
    if ( $uwp_form ) {
        switch( $uwp_form ) {
            case 'register':
            case 'login':
            case 'forgot':
            case 'account':
                $site_key = $uwp_options['recaptcha_api_key'];
                $secret_key = $uwp_options['recaptcha_api_secret'];

                if ( !( strlen( $site_key ) > 10 && strlen( $secret_key ) > 10 ) ) {
                    return;
                }

                $reCaptcha = new ReCaptcha( $secret_key );

                $recaptcha_value = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : '';
                $response = $reCaptcha->verifyResponse( $_SERVER['REMOTE_ADDR'], $recaptcha_value );

                $invalid_captcha = !empty( $response ) && isset( $response->success ) && $response->success ? false : true;

                if ( !$invalid_captcha ) {
                    $return['success'] = true;
                    $return['error'] = NULL;
                }
                break;
        }
    }
    echo json_encode( $return );
    exit;
}

function uwp_recaptcha_form() {
    $content = uwp_recaptcha_display( 'registration' );

    if ( $content ) {
        echo $content;
    }
}

function uwp_recaptcha_display( $form, $extra_class='' ) {

    global $uwp_options;
    $site_key = $uwp_options['recaptcha_api_key'];
    $secret_key = $uwp_options['recaptcha_api_secret'];

    if ( strlen( $site_key ) > 10 && strlen( $secret_key ) > 10 ) {
        $captcha_title = get_option( 'uwp_recaptcha_title' );

        $language = uwp_recaptcha_language();
        $captcha_theme = uwp_recaptcha_theme();


        $captcha_title = apply_filters( 'uwp_captcha_title', $captcha_title );

        $ajax = ( defined( 'DOING_AJAX' ) && DOING_AJAX );
        $div_id = 'uwp_captcha_' . $form;
        ?>
        <div class="uwp-captcha uwp-captcha-<?php echo $form;?> <?php echo $extra_class;?>" style="margin:7px 0">
            <?php if ( trim( $captcha_title ) != '' ) { ?><label class="gd-captcha-title"><?php _e( $captcha_title ) ;?></label><?php } ?>
            <?php if ( $form == 'bp_registration' ) { global $bp; if ( !empty( $bp->signup->errors['gd_recaptcha_field'] ) ) { ?>
                <div class="error"><?php echo $bp->signup->errors['gd_recaptcha_field'];?></div>
            <?php } } ?>
            <div id="<?php echo $div_id;?>" class="gd-captcha-render"></div>
            <?php
            if ( $ajax ) {
                ?>
                <script type="text/javascript">
                    try {
                        var <?php echo $div_id;?> = function() {
                        }
                        jQuery(function() {
                            jQuery.getScript( 'https://www.google.com/recaptcha/api.js?onload=<?php echo $div_id;?>&hl=<?php echo $language;?>&render=explicit' ).
                                done(function( script, textStatus ) {
                                    if (typeof grecaptcha == 'undefined') {
                                        var to;
                                        clearInterval(to);
                                        to = setInterval(function(){
                                            if ( typeof grecaptcha != 'undefined' ) {
                                                clearInterval(to);
                                                if ( !jQuery('#<?php echo $div_id;?>').html() ) {
                                                    grecaptcha.render('<?php echo $div_id;?>', { 'sitekey' : '<?php echo $site_key;?>', 'theme' : '<?php echo $captcha_theme;?>', 'callback' : gdcaptcha_callback_<?php echo $div_id;?> });
                                                }
                                            }
                                        }, 50);
                                    } else {
                                        if ( !jQuery('#<?php echo $div_id;?>').html() ) {
                                            grecaptcha.render('<?php echo $div_id;?>', { 'sitekey' : '<?php echo $site_key;?>', 'theme' : '<?php echo $captcha_theme;?>', 'callback' : gdcaptcha_callback_<?php echo $div_id;?> });
                                        }
                                    }
                                })
                                .fail(function( jqxhr, settings, exception ) { console.log( exception ); });
                        });
                    } catch(err) {
                        console.log(err);
                    }

                    function gdcaptcha_callback_<?php echo $div_id;?>(res) {
                        if (typeof res != 'undefined' && res) {
                            jQuery('#<?php echo $div_id;?> .g-recaptcha-response').val(res);
                        }
                    }
                </script>
            <?php } else { ?>
                <script type="text/javascript">
                    try {
                        var <?php echo $div_id;?> = function() {
                            if ( ( typeof jQuery != 'undefined' && !jQuery('#<?php echo $div_id;?>').html() ) || '<?php echo $form;?>'=='registration' ) {
                                grecaptcha.render('<?php echo $div_id;?>', { 'sitekey' : '<?php echo $site_key;?>', 'theme' : '<?php echo $captcha_theme;?>' });
                            }
                        }
                    } catch(err) {
                        console.log(err);
                    }
                    if ( typeof grecaptcha != 'undefined' && grecaptcha ) {
                        <?php echo $div_id;?>();
                    }
                </script>
                <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?onload=<?php echo $div_id;?>&hl=<?php echo $language;?>&render=explicit" async defer></script>
                <?php
            }
            ?>
        </div>
        <?php
    } else {
        $plugin_settings_link = admin_url( '/admin.php?page=geodirectory&tab=uwp_recaptcha&subtab=gdcaptcha_settings' );
        ?>
        <div class="gd-captcha gd-captcha-<?php echo $form; ?>">
            <div class="gd-captcha-err"><?php echo sprintf( __( 'To use reCAPTCHA you must get an API key from  <a target="_blank" href="https://www.google.com/recaptcha/admin">here</a> and enter keys in the plugin settings page at <a target="_blank" href="%s">here</a>' ), $plugin_settings_link ); ?></div>
        </div>
        <?php
    }
}