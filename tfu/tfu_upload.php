<?php
/**
 * TWG Flash uploader 2.10.x
 *
 * Copyright (c) 2004-2009 TinyWebGallery
 * written by Michael Dempfle
 *
 *
 *     This file uploads the images to your webspace.
 *
 *     The sessionid is always sent to this file because otherwise the
 *     session is lost in Firefox and Opera!
 *
 *     The uploaded files are resized if this is possible (jpg,png,gif).
 *
 *     The current build can write debug information to the file tfu.log. The number of
 *     files that are uploaded and the filenames! You can uncomment the debug lines if
 *     you have a problem.
 *
 *     Authentification is done by the session $_SESSION["TFU_LOGIN"]. You can set
 *     this in the tfu_config.php or implement your own way!
 */
define('_VALID_TWG', '42');

if (isset($_GET['TFUSESSID'])) { // this is a workaround if you set php_flag session.use_trans_sid=off + a workaround for some servers that don't handle sessions correctly if you open 2 instances of TFU
    session_id($_GET['TFUSESSID']);
}
session_start();

$install_path = ''; // do not change!
$path_fix = '';     // do not change!
$store = 0;         // do not change!

include 'tfu_helper.php';

restore_temp_session(); // this restores a lost session if your server handles sessions wrong!

include 'tfu_config.php';

if ($enable_upload_debug) tfu_debug('1. Config loaded');
/*
PLEASE ADD OWN CODE AFTER THIS POINT. 
Otherwise the session is maybe not started proplery!
*/


/**
 * This is some debug information - please uncomment this if I ask for it in a debug session ;).
 * tfu_debug("session id : " . session_id());
 * tfu_debug("session TFU: " . $_GET['TFUSESSID']);
 * tfu_debug("login: " . $_SESSION["TFU_LOGIN"]);
 * tfu_debug("dir: " . $_SESSION["TFU_DIR"]);
 */
// we check if a valid authenification was done in tfu_config.php
if (isset($_SESSION['TFU_LOGIN']) && isset($_GET['remaining']) && isset($_GET['tfu_rn']) && isset($_SESSION['TFU_RN']) && $_SESSION['TFU_RN'] == parseInputParameter($_GET['tfu_rn'])) {
    if ($enable_upload_debug) tfu_debug('2. Authenification sucessfull');
    $dir = getCurrentDir();
    if ($enable_upload_debug) tfu_debug('3. Directory read: ' . $dir);
    $size = (isset($_GET['size'])) ? parseInputParameter($_GET['size']) : 100000;
    $remaining = parseInputParameter($_GET['remaining']) - 1;
    if ($remaining < 0) { // not valid! we expect at least 1
        return;
    }
    
    if (!isset($_SESSION['TFU_LAST_UPLOADS']) || isset($_GET['firstStart'])) {
        // we delete the info of the last upload items!
        unset($_SESSION['TFU_LAST_UPLOADS']);
        $_SESSION['TFU_LAST_UPLOADS'] = array();
    }
    $_SESSION['TFU_UPLOAD_REMAINING'] = $_GET['remaining'];

    foreach ($_FILES as $fieldName => $file) {
        // we check the uploaded files first because we don't know if it's the flash or any other script!
        if ($enable_upload_debug) tfu_debug('4. Check valid extension: ' . $file['name']);
        check_valid_extension($file['name']);
        if ($enable_upload_debug) tfu_debug('4a. Extension valid.');
        $store = 1;
        if (is_supported_tfu_image($file['name'], $file['tmp_name']) && $size < 100000) {
            // we resize before moving the file to the final destination because maybe the user has some quotas
            $store = resize_file($file['tmp_name'], $size, 80, $file['name']);   
        }
        if ($store != 0) { // ok or try later
            $base_filename = $image = my_basename($file['name']);
            if ($normalise_file_names) {
              $image = normalizeFileNames($image);
            }
            $image = fix_decoding($image, $fix_utf8);
            
            $filename = $dir . '/' . $image;
            // here you can do additional checks if a file already exists any you don't want that the existing one will be overwritten.
            $uploaded = false;
			
			// This is only needed for JFU - ignore this small part if you use TFU standalone:
            $workaround_dir = ($dir == "./../../../..") && is_writeable("./../../../../cache"); // start workaround for some php versions (e.g. 5.0.3!) if you upload to the main folder !
			      if ($workaround_dir) { 
              $filename = $dir . "/cache/" . $image; 
            }
            // end JFU
            if ($enable_upload_debug) tfu_debug('5. Before move_uploaded_file : ' . $file['tmp_name'] . ' -> ' .$filename);
            if (!$enable_upload_debug) set_error_handler('on_error_no_output');
            $result = move_uploaded_file($file['tmp_name'], $filename);  
            if (!$enable_upload_debug) set_error_handler('on_error');        
            if ($result) {
                  // This is only needed for JFU - ignore this small part if you use TFU standalone:
                  if ($workaround_dir) { 
                    copy($filename, $dir . "/" . $image); unlink($filename); $filename = $dir . "/" . $image;
                  }
                  // end JFU
              $uploaded = file_exists($filename);
            }
            if ($enable_upload_debug) tfu_debug('5a. after move_uploaded_file.');
            // Retry - bad file name - I try to fix this and save it anyway!
            if (!$uploaded) { 
                // we normalize even if not selected because saving with the default name failed!
                $filename = $dir . '/' . (fix_decoding(normalizeFileNames($base_filename),$fix_utf8)); 
                if ($enable_upload_debug) tfu_debug('5b. retry move_uploaded_file : ' . $filename);
                if (@move_uploaded_file($file['tmp_name'], $filename)) {
                    $uploaded = file_exists($filename);
                }
            }
            
            if ($uploaded) {
                if ($enable_upload_debug) tfu_debug('6. Uploaded.');
                // we check the filesize later because of basedir restrictions in the the tmp dir!
                check_valid_filesize($filename);
                if ($file_chmod != 0) {
                  @chmod($filename, $file_chmod);
                }
                if ($store == 2) {
                  // we resize after the move because before it was not possible on this server
                  // no fallback right now because the file is already uploaded.
                  resize_file($filename, $size, 80, $base_filename);
                }
                
                // plugins are loaded here to do something after the upload - currently this is used for TWG. Other
                // plugins can be found on the website.
                if ($enable_upload_debug) tfu_debug('7. Internal processing done.');
                $plugins = glob("*_plugin.php");
                if ($plugins) {
                  natsort($plugins);
                  foreach ($plugins as $f) {
                    if ($enable_upload_debug) tfu_debug('8. Execute plugin: ' . $f);
                    include_once($f);
                    $exchangefilename = $filename;
                    $store = 0; // if the plugin resize this variable has to be initialized!
                    if (function_exists(basename ($f,".php"). "_process_upload_file")) {
                      call_user_func(basename ($f,".php"). "_process_upload_file" , $dir, $filename, $image);
                    }
                    if ($filename != $exchangefilename) { // The plugin has changed the filename.
                      $filename = $exchangefilename; 
                      $image = my_basename($exchangefilename);
                    }
                    
                  }
                  if ($enable_upload_debug) tfu_debug('8a. End plugins');
                }   
                
                $filename_save = $filename;   
                /* handles the description which can be sent with each file */
                if (isset ($_GET['description'])) {
                    // we have an additional description - stored as image name.txt
                    if ($description_mode_store == 'txt') {
                        if (!$handle = fopen($filename . '.txt', "w")) {
                            tfu_debug('Cannot create ' . $filename . '. The following data was sent: ' . $_GET['description']);
                        } else {
                            fwrite($handle, $_GET['description']);
                            fclose($handle);
                        }
                    } else { // we add the descritption to the upload that is added to the e-mail
                        $filename_save .= ' : ' . $_GET['description'];
                    }
                }
                /* end description */
                array_push($_SESSION['TFU_LAST_UPLOADS'], $filename_save);
                removeCacheThumb($filename);
                // this generates the two thumbnails of the preview
                // set this to true if you like this to be done at the upload an not on the fly.
                if (false) {
                    send_thumb($filename, 90, 400, 275, true);
                    send_thumb($filename, 90, 80, 55, true);
                }
                           
                // end plugin     
            } else {
             if ($enable_upload_debug) tfu_debug('6. NOT uploaded.');
            }
        }
    }
    if (count($_SESSION['TFU_LAST_UPLOADS']) > 0 && $remaining == 0 && $split_extension != 'FALSE') { // last item in the upload AND we have stored stuff!
        restore_split_files($_SESSION['TFU_LAST_UPLOADS']);
        resize_merged_files($_SESSION['TFU_LAST_UPLOADS'], $size);
    }
    // E-mail section
    // we only send an email for the last item of an upload cycle    
    if ($upload_notification_email != '' && $remaining == 0) {
        $youremail = $upload_notification_email_from;
        $email = $upload_notification_email;
        $submailheaders = "From: $youremail\n";
        $submailheaders .= "Reply-To: $youremail\n";
        $submailheaders .= "Return-Path: $youremail\n"; 
        // $submailheaders .= 'Content-Type: text/plain; charset=UTF-8';
        $subject = $upload_notification_email_subject;
        $filestr = "\n\n";
        foreach ($_SESSION['TFU_LAST_UPLOADS'] as $filename) {
            if ($upload_notification_use_full_path) {
              $filestr = $filestr . space_enc(fixUrl(getRootUrl() . $path_fix . $filename)) . "\n";
            } else {  
              $filestr = $filestr . str_replace('./', '', str_replace('../', '', $filename)) . "\n";
            }
        }
        if ($filestr == "\n\n") {
            $filestr .= 'Please check your setup. No files where uploaded.';
        }
        $username = (isset($_SESSION['TFU_USER'])) ? $_SESSION['TFU_USER'] : $_SERVER['REMOTE_ADDR']; // if we don't have a use we use the IP
        $mailtext = sprintf($upload_notification_email_text, $username , $filestr);
        if (isset ($_SESSION['TFU_PRE_UPLOAD_DATA'])) {
          $mailtext .= "\n\n" . $_SESSION['TFU_PRE_UPLOAD_DATA'];  
        }
        @mail ($email, html_entity_decode ($subject), (html_entity_decode ($mailtext)), $submailheaders);
    }
    if ($remaining == 0) { // cleanup
      unset($_SESSION['TFU_PRE_UPLOAD_DATA']);
    }
    // end of e-mail section
    if ($enable_upload_debug) tfu_debug('9. End upload');
    store_temp_session();
    if ($enable_upload_debug) tfu_debug('10. End store session');
} else if (isset($_GET['remaining']) && isset($_GET['firstStart'])) { // seems like the session is lost! therefore we create a temp dir that enables TFU session handling
    if ($enable_upload_debug) tfu_debug('2. Authenification NOT sucessfull');
    checkSessionTempDir();
    echo 'Not logged in!';
} else {
    if ($enable_upload_debug) tfu_debug('2a. Authenification NOT sucessfull');
    echo 'Not logged in!';
}
echo ' '; // important - solves bug for Mac!
flush();
?>