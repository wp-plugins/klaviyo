<?php
/*
 * Plugin Name: Klaviyo
 * Version: 1.0
 * Plugin URI: http://wordpress.org/extend/plugins/klaviyo-wordpress/
 * Description: Adds the necessary JavaScript code to use <a href="http://www.klaviyo.com/?src=klaviyo-wordpress">Klaviyo</a>. After enabling this plugin visit <a href="options-general.php?page=klaviyo.php">the options page</a> and enter your Klaviyo ID and enable learnlets.
 * Author: Andrew Bialecki
 * Author URI: http://www.klaviyo.com/
 */

// Constants for enabled/disabled state
define("klaviyo_enabled", "enabled", true);
define("klaviyo_disabled", "disabled", true);

// Defaults, etc.
define("key_klaviyo_status", "klaviyo_status", true);
define("key_klaviyo_id", "klaviyo_id", true);
define("key_klaviyo_footer", "klaviyo_footer", true);

define("klaviyo_status_default", klaviyo_disabled, true);
define("klaviyo_extra_default", "", true);
define("klaviyo_footer_default", klaviyo_disabled, true);

// Create the default key and status
add_option(key_klaviyo_status, klaviyo_status_default, 'If Klaviyo logging in turned on or off.');
add_option(key_klaviyo_id, klaviyo_extra_default, 'Additional Klaviyo tracking options');
add_option(key_klaviyo_footer, klaviyo_footer_default, 'If Klaviyo is outputting in the footer');

// Create a option page for settings
add_action('admin_menu', 'add_klaviyo_option_page');

// Hook in the options page function
function add_klaviyo_option_page() {
    add_options_page('Klaviyo Options', 'Klaviyo', 8, basename(__FILE__), 'klaviyo_options_page');
}

function klaviyo_options_page() {
    // If we are a postback, store the options
    if (isset($_POST['info_update'])) {
        // Update the status
        $klaviyo_status = $_POST[key_klaviyo_status];
        if (($klaviyo_status != klaviyo_enabled) && ($klaviyo_status != klaviyo_disabled))
            $klaviyo_status = klaviyo_status_default;
        update_option(key_klaviyo_status, $klaviyo_status);

        // Update the extra tracking code
        $klaviyo_id = $_POST[key_klaviyo_id];
        update_option(key_klaviyo_id, $klaviyo_id);

        // Update the footer
        $klaviyo_footer = $_POST[key_klaviyo_footer];
        if (($klaviyo_footer != klaviyo_enabled) && ($klaviyo_footer != klaviyo_disabled))
            $klaviyo_footer = klaviyo_footer_default;
        update_option(key_klaviyo_footer, $klaviyo_footer);

        // Give an updated message
        echo "<div class='updated fade'><p><strong>Klaviyo settings saved.</strong></p></div>";
    }
    // Output the options page
    ?>

        <div class="wrap">
        <form method="post" action="options-general.php?page=klaviyo.php">
        <?php //ga_nonce_field(); ?>
            <h2>Klaviyo Options</h2>
            <h3>Basic Options</h3>
            <?php if (get_option(key_klaviyo_status) == klaviyo_disabled) { ?>
                <div style="margin:10px auto; border:3px #f00 solid; background-color:#fdd; color:#000; padding:10px; text-align:center;">
                Klaviyo integration is currently <strong>DISABLED</strong>.
                </div>
            <?php } ?>
            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
                <tr>
                    <th width="30%" valign="top" style="padding-top: 10px;">
                        <label for="<?php echo key_klaviyo_status ?>">Klaviyo is:</label>
                    </th>
                    <td>
                        <?php
                        echo "<select name='".key_klaviyo_status."' id='".key_klaviyo_status."'>\n";
                        
                        echo "<option value='".klaviyo_enabled."'";
                        if(get_option(key_klaviyo_status) == klaviyo_enabled)
                            echo " selected='selected'";
                        echo ">Enabled</option>\n";
                        
                        echo "<option value='".klaviyo_disabled."'";
                        if(get_option(key_klaviyo_status) == klaviyo_disabled)
                            echo" selected='selected'";
                        echo ">Disabled</option>\n";
                        
                        echo "</select>\n";
                        ?>
                    </td>
                </tr>
            </table>
            <h3>Advanced Options</h3>
                <table class="form-table" cellspacing="2" cellpadding="5" width="100%">
                <tr>
                    <th width="30%" valign="top" style="padding-top: 10px;">
                        <label for="<?php echo key_klaviyo_footer ?>">Footer code:</label>
                    </th>
                    <td>
                        <?php
                        echo "<select name='".key_klaviyo_footer."' id='".key_klaviyo_footer."'>\n";
                        
                        echo "<option value='".klaviyo_enabled."'";
                        if(get_option(key_klaviyo_footer) == klaviyo_enabled)
                            echo " selected='selected'";
                        echo ">Enabled</option>\n";
                        
                        echo "<option value='".klaviyo_disabled."'";
                        if(get_option(key_klaviyo_footer) == klaviyo_disabled)
                            echo" selected='selected'";
                        echo ">Disabled</option>\n";
                        
                        echo "</select>\n";
                        ?>
                        <p style="margin: 5px 10px;">Enabling this option will insert the Klaviyo code in your site's footer instead of your header. This will speed up your page loading if turned on. Not all themes support code in the footer, so if you turn this option on, be sure to check that your learnlets still appear.</p>
                    </td>
                </tr>
                <tr>
                    <th valign="top" style="padding-top: 10px;">
                        <label for="<?php echo key_klaviyo_id; ?>">Klaviyo ID:</label>
                    </th>
                    <td>
                        <?php
                        echo "<input type='text' name='".key_klaviyo_id."' ";
                        echo "id='".key_klaviyo_id."' ";
                        echo "value='".stripslashes(get_option(key_klaviyo_id))."' />\n";
                        ?>

                        <p style="margin: 5px 10px;">Enter your Klaviyo ID.  You can find your <a href="http://www.klaviyo.com/home" target="_blank" title="Open Klaviyo site">Klaviyo ID here</a>. A Klaviyo account is required to use this plugin.</p>
                    </td>
                </tr>
                </table>
            <p class="submit">
                <input type='submit' name='info_update' value='Save Changes' />
            </p>
        </div>
        </form>

<?php
}

// Add the script
if (get_option(key_klaviyo_footer) == klaviyo_enabled) {
    add_action('wp_footer', 'add_klaviyo_connect');
} else {
    add_action('wp_head', 'add_klaviyo_connect');
}

// If we can indentify the current user output
function get_klaviyo_connect_identify() {
    global $current_user;
    get_currentuserinfo();
    if ($current_user->user_email) {
        echo "_learnq.push([\"identify\", {\n";
        echo "\"$email\" : \"".$current_user->user_email."\",\n";
        echo "}]);\n";
    } else {
        // See if current user is a commenter
        $commenter = wp_get_current_commenter();
        if ($commenter['comment_author_email']) {
            echo "_learnq.push([\"identify\", {\n";
            echo "\"$email\" : \"".$commenter['comment_author_email']."\",\n";
            echo "}]);\n";
        }
    } 
}


// The guts of the Klaviyo script
function add_klaviyo_connect() {
    global $current_user;
    get_currentuserinfo();
    $klaviyo_id = stripslashes(get_option(key_klaviyo_id));
    
    // If Klaviyo is enabled and has a valid key
    if (get_option(key_klaviyo_status) != klaviyo_disabled) {
        // Insert code
        echo "<!-- Start Klaviyo By WP-Plugin: Klaviyo -->\n";
        echo "<script type=\"text/javascript\">\n";
        echo "var _learnq = _learnq || [];\n";
        
        if ( '' != $klaviyo_id ) {
            echo "_learnq.push([\"account\", \"".$klaviyo_id."\"]);\n";
        }
        
        // Optional
        get_klaviyo_connect_identify();

        echo "(function() {\n";
        echo "   var pa = document.createElement('script'); pa.type = 'text/javascript'; pa.async = true;\n";
        echo "   pa.src = '//a.klaviyo.com/media/js/learnmarklet.js';\n";
        echo "   var s = document.getElementsByTagName('script')[0];\n";
        echo "   s.parentNode.insertBefore(pa, s);\n";
        echo "})();\n";
        echo "</script>\n";
        echo"<!-- end: Klaviyo Code. -->\n";
    }
}

?>