<?php
/**
 *   This file contains all methods used on the main wfu page from the WFU class
 *
 */

if (!class_exists("WFUFlash")) {
    class WFUFlash {

        function printWFU($devOptions, $istab) {
            $show_flash = true;
            $htaccess_path = dirname(__FILE__) . '/../tfu/.htaccess';
            $reg_path = dirname(__FILE__) . '/../tfu/twg.lic.php';

            $relative_dir = dirname($_SERVER['PHP_SELF']);
            $relative_dir = rtrim($relative_dir,"\\/.") . '/'; // we replace to get a consistent output with different php versions!
            $base_dir = "../wp-content/plugins/wp-flash-uploader/tfu";

            ob_start();
            @session_start();
            ob_end_clean();

            $_SESSION["IS_ADMIN"] = "true";
            WFUFlash::storeSettingsToSession($devOptions);

            // the options we need from Wordpress:
            $uploads_use_yearmonth_folders = get_option('uploads_use_yearmonth_folders');
            $upload_path = get_option('upload_path');
            if ($upload_path == '') {
              $upload_path = 'wp-content/uploads';
            }
            // we have to make the path relative! if we find wp-content we remove everything before!
            if (stristr($upload_path, 'wp-content') !== false) {
                $upload_path = stristr($upload_path, 'wp-content');
            }

            if (!isset($_POST['upload_wordpress'])) {

                $upload_base_dir = '../../../../'. $upload_path;

                if ($uploads_use_yearmonth_folders) {
                    $today = getdate();
                    $year = $today['year'];
                    $month = sprintf("%02d", $today['mon']);
                    $folder = $upload_base_dir .'/'.$year.'/'.$month;
                    $path = dirname(__FILE__) . '/'. $upload_base_dir .'/'.$year.'/'.$month;
                } else {
                    $folder = $upload_base_dir;
                    $path = dirname(__FILE__) . '/' . $upload_base_dir;
                }
                if (!file_exists($path)) {  
                    if (!@WFUFlash::mkdir_recursive($path)) {
                        // default handling is not possible we show the message what to do
                        if (!file_exists(dirname(__FILE__) . '/' . $upload_base_dir)) {
                            echo '<p><br></p><div class="error wfu_reg"><p>';
                            echo 'The upload folder does not exist and could not be created. To enable uploads please create the folder "'.$upload_path.'" with your FTP program and set the permissions that this folder is writeable.';
                            if ($uploads_use_yearmonth_folders) {
                                echo '<p>This normally means that sub folder cannot be created either.</p><p>You have "Organize my uploads into month- and year-based folders" enabled. You should now do one of the following: <ul><li>To use the full filter functionality of the Wordpress Media Library you have to create a sub folder with the year and again a sub folder with the current month. e.g. 2009/08.<br>OR</li><li>Disable "Organize my uploads into month- and year-based folders" under Settings->Miscellaneous Settings. Then all files will be uploaded to "'.$upload_path.'" and you don\'t have to create a subfolder each month.</li><ul></p>';
                            }
                            echo '<p>After this you should be able to upload.</p>';
                            echo '</p></div>';
                            return;
                        } else {
                            if ($uploads_use_yearmonth_folders) {
                                $path = dirname(__FILE__) . '/'. $upload_base_dir .'/'.$year;
                                $upload_base_display = substr(stristr($upload_base_dir, '../../../../'),12);
                                if (!file_exists($path)) {
                                    echo '<div class="updated"><p>';
                                    echo 'The upload folder "'. $upload_base_display .'" does not contain a folder of the current year. To use the full filter functionality of the Wordpress Media Library you have to create a sub folder with the year and again a sub folder with the month. e.g. 2009/06.<br>Your other option is to disable "Organize my uploads into month- and year-based folders" under Settings->Miscellaneous Settings.<br>The upload folder is now set to the folder "'.$upload_base_display.'".';
                                    echo '</p></div>';

                                } else {
                                    echo '<div class="updated"><p>';
                                    echo 'The upload folder "'. $upload_base_display .'" does contain a folder for the current year but not for the current month. To use the full filter functionality of the Wordpress Media Library you have to create a sub folder with the year and again a sub folder with the month. e.g. 2009/07. WFU already tried to create this folder. If you are not able to create the month folder directly delete the year folder and create the year AND the month.<br>Your other option is to disable "Organize my uploads into month- and year-based folders" under Settings->Miscellaneous Settings.<br>The upload folder is now set to the folder "'.$upload_base_display.'".';
                                    echo '</p></div>';
                                }
                                $folder = $upload_base_dir;
                                $path = dirname(__FILE__) . '/'. $upload_base_dir;
                            }
                        }
                    }
                }
            }

            if (isset($_POST['upload_wordpress'])) {
                $folder = '../../../../';
                $path = '../';
            } else if (isset($_POST['create_htaccess'])) {
                WFUFlash::create_htaccess();
            } else if (isset($_POST['delete_htaccess'])) {
                WFUFlash::delete_htaccess();
            }
            if (!@is_writeable($path)) {
                // we try to chmod to make the folder writeable. We increase the permissions step by step
                // (755, 775 and finally 777). If nothing works we display a message
                ob_start();
                @chmod($path, 0755);
                ob_end_clean();
                @clearstatcache();
                if (!@is_writeable($path)) {
                    ob_start();
                    @chmod($path, 0775);
                    @clearstatcache();
                    if (!@is_writeable($path)) {
                        @chmod($path, 0777);
                        @clearstatcache();
                    }
                    ob_end_clean();
                    if (!@is_writeable($path)) {

                        echo '<div class="error"><p>';
                        echo 'The upload folder "'. substr(stristr($path, '../../../../'),12) .'" is not writeable. Please change the permissions with a FTP program.</p>';
                        echo '</p></div>';
                    }
                }
            }

            $_SESSION["TFU_FOLDER"] =  $folder;
            ob_start();
            $id = session_id();
            session_write_close();
            ob_end_clean();
            if ($istab) {
                echo '<div style="clear: both;"></div>';
            }
            echo '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">';
            echo '<div class="wrap wfupadding">';
            echo '<div id="icon-upload" class="icon_jfu"><br></div>';
            echo '<h2>WP Flash Uploader</h2>';

            if (current_user_can('manage_options') && !$istab) {
                echo '<p>Please select if you want to upload a media file or if you want to manage Wordpress.</p>';
                echo '<div class="submit" style="padding:0px;">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="upload_media" value="';
                echo _e('Media', 'WFU');
                echo  '" />&nbsp;&nbsp;&nbsp;&nbsp;';
                echo '<input type="submit" name="upload_wordpress" value="';
                echo _e('Wordpress', 'WFU');
                echo '" />';

                echo '<p class="howto">If you select \'Media\' the files are copied where the normal wordpress upload would upload the file (upload/&lt;year&gt;/&lt;month&gt;).<br>If you select \'Wordpress\' you can upload to the main wordpress folder and manage your Wordpress installation.</p>';
            }
            echo '<p>Choose files to upload. You can add titles and description for the media files after the upload on the media library page.</p>';
            if (!file_exists($reg_path)) {
                if ($istab) {
                    echo '<p>Please <strong>synchronize</strong> the media library after the upload on the "Sync" tab.</p>';
                } else {
                    echo '<p>Please <strong>synchronize</strong> the media library after the upload on the "Sync media library" menu entry.</p>';
                }
            }
            echo '
           <script type="text/javascript" src="../wp-content/plugins/wp-flash-uploader/tfu/swfobject.js"></script>
<script type="text/javascript">
   document.write(\'<div id="flashcontent"><div class="noflash">TWG Flash Uploader requires at least Flash 8.<br>Please go to <a target="blank" href="http://www.adobe.com/go/EN_US-H-GET-FLASH">adobe</a> and install it.';
            if (file_exists($htaccess_path)) {
                echo '<p>You have created a .htaccess file which seems not to help on your server. Please go to <a target="blank" class="nounderline" href="http://blog.tinywebgallery.com/wfu/wfu-faq/">blog.tinywebgallery.com/wfu/wfu-faq/</a> for more help.</p>';
            }
            echo '<\/div><\/div>\');

var flashvars = {};
var params = {};
var attributes = {};

params.allowfullscreen = "true";
params.scale = "noScale";
flashvars.wordpress="true";
flashvars.session_id="'. $id .'";	
flashvars.base="'.$base_dir.'";
flashvars.relative_dir="'.$relative_dir.'";';

if ($devOptions['swf_text']) {
  $elements = split("&",$devOptions['swf_text']);
  foreach ($elements as $element) {
    echo "flashvars." . str_replace("=", "=\"", $element) . "\";";
  }
}
echo '
swfobject.embedSWF("../wp-content/plugins/wp-flash-uploader/tfu/tfu_210.swf", "flashcontent", "650", "340", "8.0.0", "", flashvars, params, attributes);
 
</script>
';
            echo '<br>&nbsp;';

            if (file_exists($reg_path)) {
                echo '<div id="status" name="status"><strong>Synchronisation status:</strong> <span id="status_text">Files will be automatically synchronized after upload.</span></div><br>
    <div id="statusframediv" style="display:none;" name="statusframediv"><iframe id="statusframe" name="statusfame" src="about:blank"></iframe></div>';
                echo '<script type="text/javascript">';
                echo 'function uploadFinished(loc) {';
                echo 'document.getElementById("statusframe").src="upload.php?page=wp-flash-uploader.php?&import_media_library=true"';
                echo '}';
                echo 'function deleteFile(loc) {';
                echo 'document.getElementById("statusframe").src="upload.php?page=wp-flash-uploader.php?&clean_media_library=true"';
                echo '} </script>';
            } else {
                echo '<div id="status" name="status"><strong>Synchronisation status:</strong> Please synchronize the files manually.</div><br>';
            }

            if (!$istab && current_user_can('manage_options') && $devOptions['hide_htaccess'] == 'false') {
                if (!file_exists($htaccess_path)) {
                    echo '<div class="setting-description" style="float:left">If you get the error message in the flash that you have to copy the provided <br>.htaccess file please click on the button on the right to create this file.</div>';
                    echo '<div class="submit" style="padding:5px; style="float:left">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="create_htaccess" value="';
                    echo _e('Create .htaccess', 'WFU');
                    echo  '" />';
                } else {
                    echo '<div class="howto" style="float:left">You have a .htaccess file in your flash directory. If your upload <br>still not work please remove this file by clicking on the right button.<br>Please go to <a target="blank" class="nounderline" href="http://blog.tinywebgallery.com/wfu/wfu-faq/">blog.tinywebgallery.com/wfu/wfu-faq/</a> for more help.</div>';
                    echo '<div class="submit" style="padding:5px; margin-left:20px; float:left;">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delete_htaccess" value="';
                    echo _e('Delete .htaccess', 'WFU');
                    echo  '" />';
                }
            }
            echo '</div></form><div style="clear:both" />';

            if (!$istab && $devOptions['hide_donate'] == 'false') {
                echo '<br><table><tr><td>You like this plugin? Support the development with a small donation. </td><td>&nbsp;&nbsp;&nbsp;<A target="_blank" HREF="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40mdempfle%2ede&item_name=WP%20Flash%20Uploader&item_number=Support%20Open%20Source&no_shipping=0&no_note=1&tax=0&currency_code=EUR&lc=EN&bn=PP%2dDonationsBF&charset=UTF%2d8"><img src="../wp-content/plugins/wp-flash-uploader/img/btn_donate_SM.gif"></A></td></tr></table>';
            }
        }

        function create_htaccess() {
            $filename = dirname(__FILE__) . "/../tfu/.htaccess";
            ob_start();
            $file = @fopen($filename, 'w');
            @fputs($file, "SecFilterEngine Off\nSecFilterScanPOST Off");
            @fclose($file);
            ob_end_clean();
            if (file_exists($filename)) {
                echo '<div class="updated"><p><strong>';
                echo 'The .htaccess file was created successfully.';

            } else {
                echo '<div class="error"><p><strong>';
                echo 'The .htaccess file could not be created. Please make the folder wp-content/plugins/tfu writable. You can change this permission back after the file was created.';
            }
            echo '</p></strong></div>';
        }

        function delete_htaccess() {
            $file = dirname(__FILE__) . "/../tfu/.htaccess";
            @unlink($file);
            echo '<div class="updated"><p><strong>';
            echo 'The .htaccess file was deleted.';
            echo '</p></strong></div>';

        }

        function storeSettingsToSession($devOptions) {
            global $current_user;

            if (!empty($devOptions)) {
                foreach ($devOptions as $key => $option) {
                    $_SESSION['TFU_' . strtoupper($key)] = $option;
                }
            }
            get_currentuserinfo();
            $_SESSION['TFU_USER'] =  $current_user->user_login;
            $_SESSION['TFU_USER_EMAIL'] = $current_user->user_email;
            $_SESSION['TFU_USER_ID'] = $current_user->ID;
        }
        
        function mkdir_recursive($pathname)
        { 
            is_dir(dirname($pathname)) || WFUFlash::mkdir_recursive(dirname($pathname));
            return is_dir($pathname) || @mkdir($pathname);
        }
        
    }}
?>