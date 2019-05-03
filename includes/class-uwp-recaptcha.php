<?php

if(!class_exists('UsersWP_Recaptcha')) {

    class UsersWP_Recaptcha
    {

        private static $instance;

        /**
         * Plugin Version
         */
        private $version = UWP_RECAPTCHA_VERSION;


        public static function get_instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof UsersWP_Recaptcha)) {
                self::$instance = new UsersWP_Recaptcha;
                self::$instance->setup_globals();
                self::$instance->includes();
                self::$instance->setup_actions();
            }

            return self::$instance;
        }

        private function __construct()
        {
            self::$instance = $this;
        }

        private function setup_globals()
        {

        }

        private function setup_actions()
        {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('login_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            add_action('wp_authenticate_user', array($this, 'login_authenticate'));
            add_action('registration_errors', array($this, 'registration_errors'));
            add_action('init', array($this, 'load_textdomain'));

            do_action('uwp_recaptcha_setup_actions');

            if (is_admin()) {
                add_action('admin_init', array($this, 'activation_redirect'));
            }
        }

        /**
         * Load the textdomain.
         */
        public function load_textdomain()
        {
            load_plugin_textdomain('uwp-recaptcha', false, basename(UWP_RECAPTCHA_PATH) . '/languages');
        }

        private function includes()
        {

            if (!class_exists('ReCaptcha')) {
                require_once UWP_RECAPTCHA_PATH . '/includes/recaptcha.php';
            }

            if (class_exists('UsersWP')) {
                require_once UWP_RECAPTCHA_PATH . '/includes/functions.php';
            }

            do_action('uwp_recaptcha_include_files');

            if (!is_admin())
                return;

            require_once UWP_RECAPTCHA_PATH . '/admin/settings.php';
            do_action('uwp_recaptcha_include_admin_files');

        }

        /**
         * Redirect to the registration settings page on activation.
         *
         * @since 1.0.0
         */
        public function activation_redirect()
        {
            // Bail if no activation redirect
            if (!get_transient('_uwp_recaptcha_activation_redirect')) {
                return;
            }

            // Delete the redirect transient
            delete_transient('_uwp_recaptcha_activation_redirect');

            // Bail if activating from network, or bulk
            if (is_network_admin() || isset($_GET['activate-multi'])) {
                return;
            }

            wp_safe_redirect(admin_url('admin.php?page=userswp&tab=uwp-addons&section=uwp_recaptcha'));
            exit;
        }

        public function admin_enqueue_scripts(){
            if(1 == uwp_get_option('enable_recaptcha_in_wp_login_form') || 1 == uwp_get_option('enable_recaptcha_in_wp_register_form')){
                $this->enqueue_scripts();
                add_action('login_footer', array($this, 'add_scripts'));
            }
        }

        public function enqueue_scripts()
        {
            if (!wp_script_is('uwp_recaptcha_js_api', 'registered')) {
                if ($GLOBALS['pagenow'] === 'wp-login.php' || is_uwp_page() || is_uwp_page('frontend_post_page')) {
                    $language = uwp_recaptcha_language();

                    wp_register_script('uwp_recaptcha_js_api', 'https://www.google.com/recaptcha/api.js?onload=uwp_recaptcha_onload&hl=' . $language . '&render=explicit', array('jquery'), $this->version, true);

                    add_action('wp_footer', array($this, 'add_scripts'));
                }
            }
        }

        public function add_scripts()
        {
            wp_enqueue_script('uwp_recaptcha_script', UWP_RECAPTCHA_PLUGIN_URL . 'assets/js/script.js', array('jquery', 'uwp_recaptcha_js_api'), $this->version, true);

            $localize_vars = apply_filters('uwp_recaptcha_localize_vars', array());

            wp_localize_script('uwp_recaptcha_script', 'uwp_recaptcha', $localize_vars);
        }

        public function login_authenticate($user){

            if(isset( $_POST['uwp_login_nonce'] )){  // ignore UWP login form submission
                return $user;
            }

            if(1 != uwp_get_option('enable_recaptcha_in_wp_login_form') || !uwp_recaptcha_enabled()){
                return $user;
            }

            if ( is_wp_error( $user ) && isset( $user->errors["empty_username"] ) && isset( $user->errors["empty_password"] ) ){
                return $user;
            }

            $secret_key = uwp_get_option('recaptcha_api_secret', '');

            $reCaptcha = new ReCaptcha( $secret_key );

            $recaptcha_value = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : '';
            $response = $reCaptcha->verifyResponse( $_SERVER['REMOTE_ADDR'], $recaptcha_value );

            $invalid_captcha = !empty( $response ) && isset( $response->success ) && $response->success ? false : true;

            if ( $invalid_captcha ) {
                remove_action('authenticate', 'wp_authenticate_username_password', 20);
                $error = new WP_Error();
                $err_msg = __('<strong>ERROR</strong>: reCAPTCHA verification failed. Try again.', 'uwp-recaptcha');
                $error->add('invalid_captcha', $err_msg);
                return $error;
            }

            return $user;
        }

        public function registration_errors($errors){

            if(1 != uwp_get_option('enable_recaptcha_in_wp_register_form') || !uwp_recaptcha_enabled()){
                return $errors;
            }

            $secret_key = uwp_get_option('recaptcha_api_secret', '');

            $reCaptcha = new ReCaptcha( $secret_key );

            $recaptcha_value = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : '';
            $response = $reCaptcha->verifyResponse( $_SERVER['REMOTE_ADDR'], $recaptcha_value );

            $invalid_captcha = !empty( $response ) && isset( $response->success ) && $response->success ? false : true;

            if ( $invalid_captcha ) {
                $err_msg = __('<strong>ERROR</strong>: reCAPTCHA verification failed. Try again.', 'uwp-recaptcha');
                $errors->add('invalid_captcha', $err_msg);
                return $errors;
            }

            return $errors;
        }
    }
}