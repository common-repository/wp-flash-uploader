<?php
/*
Plugin Name: WP Flash Uploader
Plugin URI: http://blog.tinywebgallery.com/wfu
Description: The WP Flash Uploader does contain 2 plugins: '<strong>WP Flash Uploader</strong>' and '<strong>Sync Media Library</strong>'. The Wordpress Flash Uploader is a flash uploader that replaces the existing flash uploader and let you manage your whole  WP installation. 'Sync Media Library' is a plugin which allows you to synchronize the Wordpress database with your upload folder. You can upload by WFU, FTP or whatever and import this files to the Media Library. 
Version: 2.10.7
Author: Michael Dempfle
Author URI: http://www.tinywebgallery.com
*/
// all parts are in an extra file in the inc folder.

ini_set("display_errors","1");

include 'inc/wfu-flash.php';		
include 'inc/wfu-settings.php';
include 'inc/wfu-sync.php';

if (!class_exists("WFU")) {
    class WFU {
        var $adminOptionsName = "WFUAdminOptions";

        var $wfu_flash;
        var $wfu_settings;
        var $wfu_sync;

        function WFU() { //constructor
            $wfu_flash = new WFUFlash();
            $wfu_settings = new WFUSettings();
            $wfu_sync = new WFUSync();
        }

        function init() {
            $this->getAdminOptions();
        }
        //Returns an array of admin options
        function getAdminOptions() {
            $wfuAdminOptions = array(
                'wp_path' => '',
                'maxfilesize' => '',
                'resize_show' => 'true',
                'resize_data' => '100000,1024',
                'resize_label' => 'Original,1024',
                'resize_default' => '0',
                'allowed_file_extensions' => 'all',
                'forbidden_file_extensions' => 'php',
                'enable_folder_browsing' => 'true',
                'enable_folder_handling' => 'true',
                'enable_file_rename' => 'false',
                'show_size' => 'true',
                'normalize' => 'true', // don#t change this - wordpress cannot handle unnormalized files !!!
                'file_chmod' => '',
                'language_dropdown' => 'de,en,es',
                'use_image_magic' => 'false',
                'image_magic_path' => 'convert',
                'upload_notification_email' => '',
                'upload_notification_email_from' => '',
                'upload_notification_email_subject' => 'Files where uploaded by the WP Flash Uploader',
                'upload_notification_email_text' => 'The following files where uploaded by %s: %s',
                'enable_file_download' => 'true',
                'preview_textfile_extensions' => 'log,php',
                'edit_textfile_extensions' => 'txt,css,html',
                'exclude_directories' => 'svn',
                'enable_folder_move' => 'true',
                'enable_file_copymove' => 'true',
                'swf_text' => '',
                'show_wfu_media' => 'true',
                'show_sync_media' => 'true', 
                'show_wfu_tab' => 'true',
                'show_sync_tab' => 'true', 
                'hide_donate' => 'false',
                'hide_htaccess' => 'false',
                'detect_resized' => 'true'    
            );

            $wfuOptions = get_option($this->adminOptionsName);
            if (!empty($wfuOptions)) {
                foreach ($wfuOptions as $key => $option)
                $wfuAdminOptions[$key] = $option;
            }
            update_option($this->adminOptionsName, $wfuAdminOptions);
            return $wfuAdminOptions;
        }

        function activate(){
            global $wp_version;
            if( ! version_compare( $wp_version, '2.7-alpha', '>=') ) {
                
                $message = __('<h1>Wordpress Flash Uploader</h1><p> Sorry, This plugin requires WordPress 2.7+</p>.', 'wfu');
                if( function_exists('deactivate_plugins') ) {
                   deactivate_plugins(__FILE__);    
                } else {
                   $message .= __('<p><strong>Please deactivate this plugin.</strong></p>', 'wfu');
                }
                wp_die($message);
            }      
        }

        /* CSS für den Admin-Bereich von WFU */
        function addAdminHeaderCode() {
            echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/wp-flash-uploader/css/wfu.css" />' . "\n";
            $wfuOptions = $this->getAdminOptions();
        }

        function printWFU($istab = false) {
            WFUFlash::printWFU($this->getAdminOptions(), $istab);
        }

        function printSync($istab = false) {
            WFUSync::printSync($this->getAdminOptions(), $istab);
        }


        function validateInput($key, $old_value, $new_value) {
            $old_value = trim(strtolower($old_value));
            $new_value = trim(strtolower($new_value));

            // booleans
            $possible_values = array( 'true', 'false', 'button', 'button1' );
            $black_list = array('<', '>', './', '://','cookie','popup','open(', 'alert','refresh', 'varchar', 'onmouse', 'javascript');
            if ( in_array( $old_value, $possible_values ) ) { // we have a defined value - only possilbe vlaues are allowed
                return in_array( $new_value, $possible_values ); // new value has to be in array
            }
            // if we have still a possible value it's treated as bad input
            if ( in_array( $new_value, $possible_values ) ) {
                return false; // new value has to be in array
            }
            // currently a blacklist is used for validation - it's not very strict but we are already in the backend and not everyone should have access here anyway.
            foreach ($black_list as $value) {
                if (strpos($new_value, $value) !== false) {
                    return false;
                }
            }
            return true;
        }

        //Prints out the admin page
        function printAdminPage() {
            $wfuOptions = $this->getAdminOptions();

            // we save all settings
            if (isset($_POST['update_WFUSettings'])) {
                // we check all settings if they contain any incorrect elements
                $failure = false;
                // simple fields
                foreach ($wfuOptions as $key => $option) {
                    if (isset($_POST[$key])) { // if is set
                        // we validate
                        $ok = $this->validateInput($key, $option, $_POST[$key]);
                        if ($ok){
                            $wfuOptions[$key] = $_POST[$key];
                        } else {
                            $failure = true;
                        }
                    }
                }

                // fields that need special treatment
                update_option($this->adminOptionsName, $wfuOptions);

                if ($failure) {
                    echo '<div class="error linepad"><p><strong>';
                    echo _e("Settings did not validate. Only valid entries where saved.", "WFU");
                } else {
                    echo '<div class="updated linepad"><p><strong>';
                    echo _e("Settings Updated.", "WFU");
                }
                echo '</strong></p></div>';
            } else 	if (isset($_POST['register_WFU'])) {
                $isvalid = $this->validateInput('', '', $_POST['l']) && $this->validateInput('', '', $_POST['s']) && $this->validateInput('', '', $_POST['d']);
                $l = $_POST['l'];
                $d = $_POST['d'];
                $s = $_POST['s'];
                if ($isvalid && strlen($s) == 67) {
                    $filename = dirname(__FILE__) . "/tfu/twg.lic.php";
                    $file = fopen($filename, 'w');
                    fputs($file, "<?php\n");
                    fputs($file, "\$l=\"".$l."\";\n");
                    fputs($file, "\$d=\"".$d."\";\n");
                    fputs($file, "\$s=\"".$s."\";\n");
                    fputs($file, "?>");
                    fclose($file);

                    if (!file_exists($filename)) {
                        echo '<div class="error linepad"><p><strong>';
                        echo _e("The license file could not be created. Please create the file manually like described in the registration e-mail.", "WFU");
                    } else {
                        echo '<div class="updated linepad"><p><strong>';
                        echo _e("You license file was created successful. Please to to the flash and check if the registration works properly.", "WFU");
                    }
                } else {
                    echo '<div class="error linepad"><p><strong>';
                    echo _e("The license data is not valid. Please enter the data exaclty like in the registration e-mail. If you think your input is right please create the license file manually like described in the registration e-mail.", "WFU");
                }
                echo '</strong></p></div>';

            } else 	if (isset($_POST['unregister_WFU'])) {
                echo '<div class="updated linepad"><p><strong>';
                unlink (dirname(__FILE__) . "/tfu/twg.lic.php");
                echo _e("Registration file was deleted.", "WFU");
                echo '</strong></p></div>';
            }


            echo '<div class=wrap><form method="post" action="'. $_SERVER["REQUEST_URI"] . '">';
            WFUSettings::printWordpressOptions($wfuOptions);
            WFUSettings::printOptions($wfuOptions);
            WFUSettings::printAdvancedOptions();
            WFUSettings::printRegisteredSettings($wfuOptions);
            // Next version - basic checks are already made on the upload page.
            // WFUSettings::printSystemCheck();
            WFUSettings::printServerInfo();
            WFUSettings::printRegistration($wfuOptions);
            WFUSettings::printLicense();
            WFUSettings::printNextVersion();

            echo '
<p>&nbsp;</p>
<center><div class="howto">WFU - WP Flash Uploader - Copyright (c) 2004-2010 TinyWebGallery.</div></center>
</form>
</div>';
        }//End function printAdminPage()




        //Add a tab to the media uploader:
        function tabs($tabs) {
            if( current_user_can( 'unfiltered_upload' ) ) {
                $wfuOptions = $this->getAdminOptions();
                if ($wfuOptions['show_wfu_tab'] == "true") {
                    $tabs['wfu'] = __('WP Flash Uploader');
                }
                if ($wfuOptions['show_sync_tab'] == "true") {
                    $tabs['sync'] = __('Sync');
                }
            }
            return $tabs;
        }

        //Handle the actual page:
        function tab_wfu_handler(){
            if( ! current_user_can( 'unfiltered_upload' ) )
            return;
            //Set the body ID
            $GLOBALS['body_id'] = 'media-upload';
            //Do an IFrame header
            iframe_header( __('WP Flash Uploader', 'wfu') );
            //Add the Media buttons
            media_upload_header();
            //Do the content
            $this->printWFU(true);
            //Do a footer
            iframe_footer();
        }

        //Handle the actual page:
        function tab_sync_handler(){
            if( ! current_user_can( 'unfiltered_upload' ) )
            return;
            //Set the body ID
            $GLOBALS['body_id'] = 'media-upload';
            //Do an IFrame header
            iframe_header( __('Synch', 'synchwfu') );
            //Add the Media buttons
            media_upload_header();
            //Do the content
            $this->printSync(true);
            //Do a footer
            iframe_footer();
        }

        function add_tab_head_files() {
            //Enqueue support files.
            if ( 'media_upload_wfu' == current_filter()  ||  'media_upload_sync' == current_filter())
            wp_enqueue_style('media');
        }

        function aktt_plugin_action_links($links, $file) {
            $plugin_file = basename(__FILE__);
            $file = basename($file);
            if ($file == $plugin_file) {
                $settings_link = '<a href="options-general.php?page='.$plugin_file.'">'.__('Settings', 'wfu').'</a>';
                array_unshift($links, $settings_link);
            }
            return $links;
        }

    } } //End Class WFU

if (class_exists("WFU")) {
    $dl_pluginSeries = new WFU();
}

//Initialize the admin panel
if (!function_exists("WFU_ap")) {
    function WFU_ap() {
        global $dl_pluginSeries;
        if (!isset($dl_pluginSeries)) {
            return;
        }
        if (function_exists('add_options_page')) {
            add_options_page('WP Flash Uplader', 'WP Flash Uploader', 9, basename(__FILE__), array(&$dl_pluginSeries, 'printAdminPage'));
        }
        $wfuOptions = &$dl_pluginSeries->getAdminOptions();
        if (function_exists('add_media_page')&& $wfuOptions['show_wfu_media'] == "true") {
            add_media_page('WP Flash Uploader', 'WP Flash Uploader', 9, basename(__FILE__), array(&$dl_pluginSeries, 'printWFU'));
        }
        if (function_exists('add_media_page')&& $wfuOptions['show_sync_media'] == "true") {
            add_media_page('Sync Media Library', 'Sync Media Library', 9, basename(__FILE__) . '?', array(&$dl_pluginSeries, 'printSync'));
        }
    }
}


//Actions and Filters	
if (isset($dl_pluginSeries)) {
    register_activation_hook(__FILE__, array(&$dl_pluginSeries, 'activate'));
    //Actions
    add_action('admin_menu', 'WFU_ap');
    add_action('wp-flash-uploader/wp-flash-uploader.php',  array(&$dl_pluginSeries, 'init'));
    add_action('admin_head', array(&$dl_pluginSeries, 'addAdminHeaderCode'),99);

    add_action('media_upload_wfu', array(&$dl_pluginSeries, 'add_tab_head_files') );
    add_action('media_upload_sync', array(&$dl_pluginSeries, 'add_tab_head_files') );

    add_filter('media_upload_tabs', array(&$dl_pluginSeries, 'tabs'));
    add_action('media_upload_wfu', array(&$dl_pluginSeries, 'tab_wfu_handler'));
    add_action('media_upload_sync', array(&$dl_pluginSeries, 'tab_sync_handler'));
    //Filters
    add_filter('plugin_action_links', array(&$dl_pluginSeries, 'aktt_plugin_action_links'),10,2);

}

?>