<?php
/**
 * TWG Flash uploader 2.10.x
 *
 * Copyright (c) 2004-2009 TinyWebGallery
 * written by Michael Dempfle
 *
 *    This file is the main configuration file of the flash.
 *
 *    Please read the documentation found in this file!
 *
 * There are 2 interesting settings you should look at first:
 *    - $login  - you can implement your own autentification by setting this flag!
 *                If you use 'auth' a login screen appears. If you use true please read the
 *                'Important' in tfu_login.php
 *            
 *    - $folder - The folder where your uploads will be saved!
 *
 *    Please edit tfu_login.php and add your authentification there. Read the howto about makeing
 *    the flash secure and/or the help text in tfu_login.php.
 *    
 *    You should create a file called my_tfu_config.php and copy your changes there.
 *    All setting in this file overwrite the settings here. You can then update
 *    mucheasier to the latest version!    
 *       
 *    Have fun using TWG Flash Uploader
 *
 *    CONFIGURATION
 */

if (defined('_VALID_TWG')) {
    $login = 'true';                     // The login flag - has to set by yourself below 'true' is logged in, 'auth' shows the login form, 'reauth' should be set if the authentification has failed. 'false' if the flash should be shown with an eroror message that the authorisation finally failed.
    $folder = 'upload';                  // this is the root upload folder. If you use login='auth' by default the folder from the user profile in .htusers.php is used!
    $maxfilesize = getMaximumUploadSize(); // The max files size limit of the server in KB. You can specify your own limit here e.g. 512. This setting is restricted by your server settings! Please read FAQ 4 of the TFU FAQ how to set upload_max_filesize and post_max_size. For flash 10 users the $maxfilesize_split is the real limit! Please set both properly.
    $resize_show = is_gd_version_min_20(); // Show the resize box! Valid is 'true' and 'false' (Strings!) - the function is_gd_version_min_20 checks if the minimum requirements for resizing images are there!
    $resize_data = '100000,1280,1024,800'; // The data for the resize dropdown
    $resize_label = 'Original,1280,1024,800'; // The labels for the resize dropdown
    $resize_default = '0';               // The preselected entry in the dropdown (1st = 0)
    $allowed_file_extensions = 'jpeg,gif,png,jpg';    // Allowed file extensions! 'all' allowes all types - this list is the supported files in the browse dropdown! If this field is empty then the upload grid is removed and the server only view is enabled. Please note: The filter of the file chooser dialog is limited. Don't use more than ~25 extensions. If you specify more TFU automatically uses 'All Files' - Then all files are listed and not supported extensions are checked by the flash after pressing 'Open'.
    $forbidden_file_extensions = 'php';  // Forbidden file extensions! - only usefull if you use 'all' and you want to skip some exensions! php e.g. means php* ! then php4 and so on is covered as well!
    // Enhanced features - this are only defaults! if TFU detects that this is not possible this functions are disabled!
    $hide_remote_view = '';              // If you want to disable the remote view set 'true' as value!
    $show_preview = is_gd_version_min_20(); // Show the small preview. Valid is 'true' and 'false' (Strings!) - the function is_gd_version_min_20 checks if the minimum requirements for resizing images are there!
    $show_big_preview = 'true';          // Show the big preview - clicking on the preview image shows a bigger preview
    $show_delete = 'true';               // Shows the delete button - if download is set to button this moves to the menu!
    $enable_folder_browsing = 'true';    // Without browsing creation and deletion is disabled by default!
    $enable_folder_creation = 'true';    // Show the menu item to create folders
    $enable_folder_deletion = 'true';    // Show the menu item to delete folders - this works recursive!
    $enable_folder_rename = 'true';      // Show the menu item to rename folders
    $enable_file_rename = 'true';        // Show the menu item to rename files - default is false because this is a securiy issue - check the point below - should only be activated in very save environments or if you keep the file extension in the registered version.!
    $keep_file_extension = 'true';       // You can disalow to change the file extension! - since 1.7.1 this is available for everyone because this can be a security issue when someone renames files to .php - Every user of TFU should be safe!
    // 
    // The default language has to be set at the flash with e.g. ?lang=de
    //
    $language_dropdown = 'de,en,es,br,cn,ct,cz,da,fr,it,jp,nl,no,pl,pt,ru,se,sk,tw'; // New 2.6 - You can enable a dropdown for the language selection. If you leave this empty no selector is shown (you can still change the language with the url parameter). Otherwise you specify the flags here. They are displayed in the given order! The default language is still given by the url parameter!
    $use_image_magic = false;            // You can enable image magick support for the resize of the upload. If you know that you have image magic on your server you can set this to true. image magick uses less memory then gd lib and it does copy exif information!
    $image_magic_path = 'convert';       // This is the image magick command used to convert the images. convert is the default of image magic.
    $check_image_magic = true;           // You can disable if the image magic test is performed at all - because if it fails the rest of the page fails too - especially in JFU this kills the whole page!
    // $timezone - This setting can be found at the top of tfu_helper.php
    
    // some optional things
    $login_text = '';                    // e.g. 'Please login';  // Login Text
    $relogin_text = '';                  // e.g. 'Wrong Username/Password. Please retry'; // Retry login text
    $upload_file = 'tfu_upload.php';     // Upload php file - this is relative to he flash
    $base_dir = $install_path;           // this is the base dir of the other files - tfu_read_Dir, tfu_file and the lang folder. since 2.6 there are no seperate settings for tfu_readDir and tfu_file anymore because it's actually not needed.
    $sort_files_by_date = false;         // sort files that last uploaded files are shown on top
    $warning_setting = 'all';            // the warning is shown if remote files do already exist - can be set to all,once,none
    $split_extension = 'part';           // This is the extension when you upload splitted files - tfu can merge them after upload. A splited file has to ge like: file.extension.part1, file.extension.part2 ... - the file extension cannot be empty - if emptpy the default is part! to disable splited uploads use 'FALSE';
    $show_size = 'true';                 // true = 'true' ; false = '' - by default the size of the files are shown - but you can disable this by removing the parameter and setting it to false;
    $hide_directory_in_title = 'false';  // You can disable the display of the upload dir in the title bar if you set this to 'true'
    $truncate_dir_in_title = 'false';    // You can truncate everything before the main upload directory if you set this to true. So only sub directories are shown in the title.
    // the text of the email is stored in the tfu_upload.php if you like to change it :)
    $upload_notification_email = '';     // you can get an email everytime a fileupload was initiated! The mail is sent at the first file of an upload queue! '' = no emails - php mail has to be configured properly!
    $upload_notification_email_from = ''; // the sender of the notification email!
    $upload_notification_email_subject = 'Files were uploaded by the TWG Flash Uploader'; // Subject of the email - you should set a nicer one after the login or in tfu_upload.php
    $upload_notification_email_text = 'The following files where uploaded by %s: %s'; // Text of the email - the first %s ist the username (if no is set 'not set is used'), the 2nd %s is the list of files that where uploaded!
    $upload_notification_use_full_path = false;  // You can either have only the path and the filename (false) or the full url (true) in the notification e-mail. This feature is not supported for JFU yet. Will be part of the next version.

    $exclude_directories = array('data.pxp', '_vti_cnf', '.svn', 'CVS', 'thumbs'); // new 2.6 - You can enter directories here that are ignored in TFU. You can enter files as well that should be hidden!
    $keep_internal_session_handling = false;  // new 2.7.5 - TFU can detect servers with session problems. And it removes the session_cache folder it it is not needed. If you set this to true the session_Cache folder is not removed automatically. You should set this to true if you have only sometimes problems with the upload!
    $normalise_file_names = false;         // new 2.7.5 - This setting convertes all filenames to lowercase and special characters are removed e.g. !"#$%&'()*+,-- []\^_`���� are replaces with an _
    $normalise_directory_names = false;   // new 2.8.1 - This setting convertes all directory names that are created or renamed to lowercase and special characters are removed e.g. !"#$%&'()*+,-- []\^_`���� are replaces with an _
    
    // This switch is for supporting filesystems for e.g. chinese characters.
    $fix_utf8 = ''; // Please read the faq 8 for TFU on the homepage first before change anything here -> http://www.tinywebgallery.com/en/tfu/web_faq.php#10
    $debug_file = dirname(__FILE__) . "/tfu.log";                     
    
    /**
     * Extra settings for the registered version
     */
    $titel = 'TWG Flash Uploader';       // This is the title of the flash - can not be set in the freeware version!
    $remote_label = '';                  // 'Remote' This is a optional setting - you can change the display string above the file list if you want to use a different header - can only be changed in the registered version! - if you want to have a & you have to urlencode the string!
    $preview_label = '';                 // 'Preview' This is a optional setting - you can change the display string of the header if you don't use the preview but maybe this function to determine the selection in the remote file list - can only be changed in the registered version!  - if you want to have a & you have to urlencode the & !
    $upload_finished_js_url = '';        // 'status.???' - You can specify a url that is called by the flash in the js function uploadFinished(param) This makes it possible e.g. to show a kind of result in a iframe below the flash. - only available in the registered version! Check the tfu.htm for examples of the Javascript function.
    $preview_select_js_url = '';         // 'preview.???' - You can specify a url that is called by the flash in the js function previewSelect(param) This makes it possible e.g. to show a kind of result in a iframe below the flash. this is only executed if show_preview=true - only available in the registered version! Check the tfu.htm for examples of the Javascript function.
    $delete_js_url = '';                 // 'delete.???' - You can specify an url that is called by the flash in the js function deleteFile(param) This makes it possible e.g. to show a kind of result in a iframe below the flash is someone deletes a file. - only available in the registered version!
    $js_change_folder = '';              // 'change_folder.???' - You can specify an url that is called by the flash in the js function changeFolder(param) This makes it possible e.g. to show a kind of result in a iframe below the flash is someone changes a folder. - only available in the registered version!
    $js_create_folder = '';              // 'create_folder.???' - You can e.g. specify an url that is called by the flash in the js function createFolder(status,param). status is the status of the folder creation. Possible status values are: exists (folder exists), true (folder created), false (folder not created) - only available in the registered version!
    $js_rename_folder = '';              // 'ren_folder.???' - You can e.g. specify an url that is called by the flash in the js function renameFolder(status,param). status is the status of the folder rename. Possible status values are: exists (destination folder exists), true (folder renamed), false (folder not renamed) - only available in the registered version!
    $js_delete_folder = '';              // 'del_folder.???' - You can e.g. specify an url that is called by the flash in the js function deleteFolder(status,param). status is the status of the folder delete. Possible status values are: true (folder deleted), false (folder not deleted) - only available in the registered version!
    $js_copymove = '';                   // 'copymove.???' - You can e.g. specify an url that is called by the flash in the js function copymove(doCopyFolder,type,total,ok,error,exits,param). Check the example in tfu.htm for a description of all parameters - only available in the registered version!
    $show_full_url_for_selected_file = ''; // 'true' - if you use this parameter the link to the selected file is shown - can be used for direct links - only available in the registered version!
    $directory_file_limit = '100000';    // you can specify a maximum number of files someone is allowed to have in a folder to limit the upload someone can make! - only available in the registered version!
    $queue_file_limit = '100000';        // you can specify a maximum number of files someone can upload at once! - only available in the registered version!
    $queue_file_limit_size = '100000';   // you can specify the limit of the upload queue in MB! - only available in the registered version!
    $hide_help_button = 'true';          // since TFU 2.5 it is possible to turn off the ? (no extra flash like before is needed anymore!) - it is triggered now by the license file! professional licenses and above and old licenses that contain a TWG_NO_ABOUT in the domain (=license for 20 Euro) enable that this switch is read - possible settings are 'true' and 'false'
    $enable_file_download = 'button1';   // You can enable the download of files! valid entries 'true', false', 'button', 'button1' - 'button' show the dl button insted the menu button if all other elements of the menu are set to false - 'button1' shows the download button instead of the delete button and the delete function is moved to the menu if enabled! - only available in the registered version!
      $download_multiple_files_as_zip = 'true'; // You can enable that multiple files are downloadd as one zip. The zip files are created dynamically. Therefore it could take some time until the download starts.
        $zip_folder = $folder;           // This is the folder where the zip file is created temporary. Please make sure that this directory is writeable!
      $direct_download = '';             // true = 'true' ; false = '' or 'false' - If the downloads are corrupt or fail on https with ie you can enable direct download. Then the flash tries to get the files directly and not over php. The disadvantage is that the url has to be urlencoded and not all filenames are possible then - do only use this if the download does not work with the default setting. zip download does not work with this setting! I recommend to enable normalize for files and directories to have valid filenames.
    $enable_folder_move = 'true';        // New 2.6 - Show the menu item to move folders - you need a professional license or above to use this
    $enable_file_copymove = 'true';      // New 2.6 - Show the menu item to move and copy files - you need a professional license or above to use this
    $preview_textfile_extensions = 'out,log'; // New 2.7 - This are the files that are previewed in the flash as textfiles. Right now I only have 'save' extensions. But you can have any extension here. If you don't use a . this settings are extensions. But you can restrict is to single files as well by using the full name. e.g. foldername.txt. * is supported as wildcard! Only available for registered users.
    $edit_textfile_extensions = 'txt,css';   // New 2.7 - This are the files that can be edited in the flash. But you can restrict is to single files as well by using the full name. e.g. foldername.txt. * is supported as wildcard! Only available for registered users.
    $allowed_view_file_extensions = 'all'; // New 2.7.0.6 - You can define the file extensions that are shown on the server view. If you set 'all' all files except the one from $forbidden_view_file_extensions are shown. If you define a list of extensions here only these are shown - Only available for registered users.
    $forbidden_view_file_extensions = ''; // New 2.7.0.6 - If you have set $allowed_view_file_extensions = 'all' then you can define a list of file extensions that are not shown - Only available for registered users.
    $description_mode = 'false';         // New 2.7.4.5 - You can enable a description mode where the size and the date is replaced by a description field. The data of this field is sent to the server and stored in a txt file called <filename>.txt or sent by e-mail to you. Only available for professional license or above.
    $description_mode_show_default = 'true'; // New 2.7.4.5 - Shows/hides a 'Enter description' in the description field if you like. The text is stored in the language file if you want to change it. Only available for professional license or above.
    $description_mode_store = 'email';   // New 2.7.4.5 - ('txt','email') The description is either saved to a textfile called <filename>.txt or is added to the notification e-mail. Only available for professional license or above.
    
    // New 2.9  
    $overwrite_files='true';             // New 2.9 - true - Existing files are overwritten; false - existing files are not overwritten.  
    $description_mode_mandatory='false'; // New 2.9 - true - a description has to be provided for each file; false - description is optional.
    $normalizeSpaces='false';            // new 2.9 - if you enable normalize file names or directory names you can decide here if spaces are replaces with an _ or not.
    $file_chmod=0;                       // New 2.9 - by using 0 the default mode of the files is used. Then the creation depend on the umask of the server.  If you want the files to have different permissions please use the octal representation e.g. 0777, 0755, 0644 ...
    $dir_chmod=0;                        // New 2.9 - by using 0 the default mode of the directory is used. Then the creation depend on the umask of the server. If you want the directory to have different permissions please use the octal representation e.g. 0777, 0755, 0644 ...
    $enable_dir_create_detection=true;   // New 2.9.0.2 - If you cannot create directories you can try to disable the automatic detection which prevents this. If you set this to 'false' the flash tries to create the directory; maybe it works ;). - try to upload files into the directory and create another subdirectory too. If this works you can leave this to false. This setting is currently not mapped in JFU 2.9 - will be added in 2.10! For 2.10 I will add a small ftp layer to create dirs. 
    
    // new 2.10 
    $enable_upload_debug = false;        // New 2.10 - This enables the debuging ouput at the upload. You should only use this after contacting me!
    $enable_enhanced_debug = false;      // New 2.10 - This shows the request to each debug line.  
    // This settings will be added to JFU backend in 2.11 - you can change this directly here if you need to!
    $form_fields = '';                   // New 2.10 - You can enable TFU to read form fields from the html page and add then to the upload as 'get' parameters. Please read howto 15 how to configure this. Only available for professional license or above.
    $hide_hidden_files = false;          // New 2.10.6 - You can hide hidden files and directories in the remote view. All files and folders starting with a . are hidden if you set this to true. 
    // special extension - a post upload panel - this is only implemented for JFU and not documented yet!
    $post_upload_panel='false';
    
    /*  This is example data for the post upload panel - this is not documented yet!
    $post_upload_panel.="&post_name=my_name";
    $post_upload_panel.="&post_company=my_firma";
    $post_upload_panel.="&post_email=my_email";
    $post_upload_panel.="&post_address=my_add";
    $post_upload_panel.="&post_postcode=my_postcode" ;
    $post_upload_panel.="&post_city=my_city" ;
    $post_upload_panel.="&post_country=my_country";
    $post_upload_panel.="&post_telephone=my_telephone";
    $post_upload_panel.="&post_fax=my_fax";
    */
    
/* new 2.7.8 we include your configuration file! Then updatin is easier because your settings are not overwritten. No my_tfu_config.php is included in the download! */
if (file_exists(dirname(__FILE__) . '/my_' . basename(__FILE__))) {
    include dirname(__FILE__) . '/my_' . basename(__FILE__);
}
} else {
    define('_VALID_TWG', '42');
    $install_path = '';
    include_once (dirname(__FILE__) . "/" . 'tfu_helper.php');
    include (basename(__FILE__)); // needed for the info!
    printServerInfo();
}
?>