<?php
class WPKlaviyoNotification {

    public $admin_message_text = '';
    public $default_message_text = '';
        
    function __construct($default_message_text = '') {
        $this->admin_message_text = '';
        $this->default_message_text = $default_message_text;
    }
    
    function config_warning() {
        $klaviyo_settings = get_option('klaviyo_settings');

        if (!WPKlaviyo::is_connected($klaviyo_settings['public_api_key'])) {
            if (!$klaviyo_settings['admin_settings_message']) {
                if (!(isset($_GET['page']) && $_GET['page'] == 'klaviyo_settings')) {
                    $this->admin_message('config_warning');
                }
            }
        } 
    }
    
    function admin_message($message='default-error', $display_time=0) {
        $message_text = '';
        
        switch ($message) {
            case 'settings_update':
                $message_text = 'Klaviyo settings updated.';
                break;
            case 'config_warning':
                $message_text = 'Please go to the <a href="' . KLAVIYO_ADMIN . 'admin.php?page=klaviyo_settings">Klaviyo settings page</a> to add your API keys or to hide this warning.';
                break;
            case 'default_error':
                $message_text = 'An error occurred, please try again or contact Klaviyo support.';
                break;
            default:
                $message_text = $message;
                break;
        }

        echo '<div id="msg-' . $message . '" class="updated fade"><p>' . $message_text . '</p></div>' . "\n";
        if ($display_time != 0) {
            echo '<script type="text/javascript">setTimeout(function () { jQuery("#msg-' . $message . '").hide("slow");}, ' . $display_time * 1000 . ');</script>';   
        }
    }

    function add_message($message_text) {
        if (trim($this->admin_message_text) != '') {
            $this->admin_message_text .= '<br />';
        }
        $this->admin_message_text .= $message_text;
    }

    function display_message($display_time=0) {
        if (trim($this->admin_message_text) != '') {
            $this->admin_message($this->admin_message_text, $display_time);
        } else {
            $this->admin_message($this->default_message_text, $display_time);
        }
    }
}

?>