function uwp_recaptcha_onload() {
    if ( ! uwp_recaptcha.loaded ) {
        jQuery('.uwp-captcha-render').each(function() {
            var container = jQuery(this).attr('id');
            if (container) {
                try {
                    eval(container + '()');
                } catch(err) {
                    console.log(err);
                }
            }
        });
        uwp_recaptcha.loaded = true;
    }
}