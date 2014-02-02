<?php
class WPKlaviyoAnalytics {

    function __construct() {
        add_action('wp_footer', array(&$this, 'insert_analytics'));
    }

    function insert_analytics() {
        global $current_user;
        wp_reset_query();

        get_currentuserinfo();
        $klaviyo_settings = get_option('klaviyo_settings');

        if ($klaviyo_settings['public_api_key'] == '') {
          return;
        }

        echo "\n" . '<!-- Start Klaviyo // Plugin Version: ' . KLAVIYO_PLUGIN_VERSION .' -->' . "\n";
        echo '<script type="text/javascript">' . "\n";
        echo 'var _learnq = _learnq || [];' . "\n";

        echo '_learnq.push(["account", "' . $klaviyo_settings['public_api_key'] . '"]);' . "\n";

        if ($current_user->user_email) {
            echo '_learnq.push(["identify", {' . "\n";
            echo '  $email : "' . $current_user->user_email . '"' . "\n";
            echo '}]);' . "\n";
        } else {
            // See if current user is a commenter
            $commenter = wp_get_current_commenter();
            if ($commenter['comment_author_email']) {
                echo '_learnq.push(["identify", {' . "\n";
                echo '  $email : "' . $commenter['comment_author_email'] . '"' . "\n";
                echo '}]);' . "\n";
            }
        } 

        echo '(function() {' . "\n";
        echo '   var pa = document.createElement("script"); pa.type = "text/javascript"; pa.async = true;' . "\n";
        echo '   pa.src = "//a.klaviyo.com/media/js/analytics/analytics.js";' . "\n";
        echo '   var s = document.getElementsByTagName("script")[0];' . "\n";
        echo '   s.parentNode.insertBefore(pa, s);' . "\n";
        echo '})();' . "\n";
        echo '</script>' . "\n";
        echo '<!-- end: Klaviyo Code. -->' . "\n";
    }
}

?>