<?php
/*
 * Plugin Name: Klaviyo
 * Version: 1.2
 * Plugin URI: http://wordpress.org/extend/plugins/klaviyo-wordpress/
 * Description: Integrate <a href="https://www.klaviyo.com/?utm_source=wordpress">Klaviyo</a> into your Wordpress site.
 * Author: Klaviyo
 * Author URI: https://www.klaviyo.com/
 */

// Support local development (symlinks)
// Info at: http://alexking.org/blog/2011/12/15/wordpress-plugins-and-symlinks
$my_plugin_file = __FILE__;

if (isset($plugin)) {
    $my_plugin_file = $plugin;
}
else if (isset($mu_plugin)) {
    $my_plugin_file = $mu_plugin;
}
else if (isset($network_plugin)) {
    $my_plugin_file = $network_plugin;
}


//
// CONSTANTS
// ------------------------------------------
if (!defined('KLAVIYO_URL')) {
    define('KLAVIYO_URL', plugin_dir_url($my_plugin_file));
}
if (!defined('KLAVIYO_PATH')) {
    define('KLAVIYO_PATH', WP_PLUGIN_DIR . '/' . basename(dirname($my_plugin_file)) . '/');
}
if (!defined('KLAVIYO_BASENAME')) {
    define('KLAVIYO_BASENAME', plugin_basename($my_plugin_file));
}
if (!defined('KLAVIYO_ADMIN')) {
    define('KLAVIYO_ADMIN', admin_url());
}
if (!defined('KLAVIYO_PLUGIN_VERSION' ) ) {
    define('KLAVIYO_PLUGIN_VERSION', '1.2.0');
}



//
// INCLUDES
// ------------------------------------------
require_once(KLAVIYO_PATH . 'inc/kla-analytics.php');
require_once(KLAVIYO_PATH . 'inc/kla-admin.php');
require_once(KLAVIYO_PATH . 'inc/kla-widgets.php');
require_once(KLAVIYO_PATH . 'inc/kla-notice.php');
require_once(KLAVIYO_PATH . 'inc/kla-logging.php');



//
// HELPER CLASS - WPKlaviyo
// ------------------------------------------

class WPKlaviyo {

    function __construct() {
        global $klaviyowp_admin, $klaviyowp_notice, $klaviyowp_analytics, $klaviyowp_tracking;
        global $post;

        $klaviyowp_admin = new WPKlaviyoAdmin();

        $klaviyowp_analytics = new WPKlaviyoAnalytics();
        //$klaviyowp_tracking = new WPKlaviyoTracking();

        // Display config message.
        $klaviyowp_message = new WPKlaviyoNotification();
        add_action('admin_notices', array(&$klaviyowp_message, 'config_warning'));

        add_action('widgets_init', create_function('', 'return register_widget("Klaviyo_EmailSignUp_Widget");'));
    }

    function add_defaults() {
        $klaviyo_settings = get_option('klaviyo_settings');

        if (($klaviyo_settings['installed'] != 'true') || !is_array($klaviyo_settings)) {
            $klaviyo_settings = array(
                'installed' => 'true',
                'public_api_key' => '',
                'private_api_key' => '',
                'admin_settings_message' => ''
            );
            update_option('klaviyo_settings', $klaviyo_settings);
        }
    }

    function is_connected($public_api_key='') {
        if (trim($public_api_key) != '') {
            return true;
        } else {
            $klaviyo_settings = get_option('klaviyo_settings');
            if (trim($klaviyo_settings['public_api_key']) != '') {
                return true;
            } else {
                return false;
            }
        }
    }

    function format_text($content, $br=true) {
        return $content;
    }
}



//
// INIT
// ------------------------------------------

global $klaviyowp;
$klaviyowp = new WPKlaviyo();
// RegisterDefault settings
register_activation_hook(__FILE__, array( $klaviyowp, 'add_defaults'));

?>