var holderId;
jQuery(document).ready(function($) {
    //uwp_init_recaptcha();
});

function uwp_init_recaptcha() {
    if ( jQuery('.uwp-captcha-render').length) {

        jQuery('.uwp-captcha-render').each(function() {
            if(jQuery(this).html()==''){
                var container = jQuery(this).attr('id');
                if (container) {
                    try {
                        //uwp_display_captcha(container);
                    } catch(err) {
                        console.log(err);
                    }
                }
            }
        });
    }
}

function uwp_display_captcha(element){
    try {
        if(uwp_recaptcha_data.captcha_version == 'v3'){
            grecaptcha.ready(
                function() {
                    holderId = grecaptcha.execute(uwp_recaptcha_data.site_key, {action: 'uwp_captcha'}).then(function (token) {
                        document.getElementById(element).value = token;
                    });
                }
            );
        } else if(uwp_recaptcha_data.captcha_version == 'invisible'){
            grecaptcha.ready(
                function() {
                    holderId = grecaptcha.render(element, {
                        'sitekey': uwp_recaptcha_data.site_key,
                        'size': 'invisible',
                        'badge': 'bottomright',
                        'callback': function (token) {
                            uwp_maybe_check_recaptcha(element);
                        }
                    });
                }
            );
        } else {
            holderId = grecaptcha.render(element , { 'sitekey' : uwp_recaptcha_data.site_key, 'theme' : uwp_recaptcha_data.captcha_theme, 'size' : uwp_recaptcha_data.captcha_size });
        }
    } catch(err) {
        console.log(err);
    }
}

function uwp_reset_captcha(element){
    if(uwp_recaptcha_data.captcha_version == 'v3') {
        if (typeof grecaptcha != 'undefined') {
            holderId = grecaptcha.execute(uwp_recaptcha_data.site_key, {action: 'uwp_captcha'}).then(function (token) {
                document.getElementById(element).value = token;
            });
        }

    } else {
        if (typeof grecaptcha != 'undefined') {
            grecaptcha.reset(holderId);
        }
    }
}

function uwp_maybe_execute_captcha(element){
    if(uwp_recaptcha_data.captcha_version == 'invisible') {
        if (typeof grecaptcha != 'undefined') {
            grecaptcha.execute(holderId);
        }

    }
}