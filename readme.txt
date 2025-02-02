=== WP Flash Uploader ===
Contributors: mdempfle, Michael Dempfle
Tags: admin, media, upload, synchronize, flash, 2.7, folder, ftp, media library, sync, uploader, images, gallery, image upload, image preview
Requires at least: 2.7
Tested up to: 2.9.2
Stable tag: 2.10.7
Donate link: Please check the settings of WP Flash Uploader

'WP Flash Uploader' is a replacement of the internal flash uploader which let you additionally manage your whole WP installation and synchronize your media library. 

== Description ==

'WP Flash Uploader' is a flash uploader that replaces the existing flash uploader 
and let you manage your whole WP installation. 
The WP Flash Uploader does contain 2 plugins: 'WP Flash Uploader' and 'Sync Media Library'. 
'Sync Media Library' is a plugin which allows you to synchronize the Wordpress database with your 
upload folder. You can upload by WFU, FTP or whatever and import this files to the Media Library. 

= Motivation: =
Wordpress has a flash uploader which was not working on any of my servers. So I decided to write a 
wrapper for the TWG Flash Uploader which works on most servers so far. 
WordPress 2.5+ includes a new Media manager, However, It only knows about files which have been uploaded 
via the WordPress interface, not files which have been uploaded via other means (eg, FTP or WFU).
So I had to implement something that does the synchronisation.
The final result are actually two plugins in one.  The 'WP Flash Uploader - WFU' and the 
'Sync Media Library'. WFU is the wrapper for the TWG Flash Uploader and with 'Sync Media Library' 
you can syncronize the upload folder with the Wordpress database. I implemented this as seperate 
menu items because maybe you want to upload your files with FTP and you can syncronize your 
files without using WFU. 

Have fun using WFU,
Michael

== Frequently Asked Questions ==

FAQ / Help: http://blog.tinywebgallery.com/wfu/wfu-faq/

== Website / Help / Forum ==

Website:    http://blog.tinywebgallery.com/wfu/
Forum:      http://www.tinywebgallery.com/en/forum.php

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the plugin from plugins page.
You get 'WP Flash Uploader' and 'Sync Media Library' in the 'Media' menu and 'WP Flash Uploader' in the 'Settings' menu. 

Go to Settings->WP Flash Uploader and check the 'Limitations' part. Most of the users can upload and are 
only restricted by their servers. And most if the problems can be solved!

== Screenshots ==
1. The WP Flash Uploader page where you can upload images
2. The Synch Media Library page where you can synchronize your upload folder with the Media Library

== Donation ==

Please go to the settings page of WP Flash Uploader. There you find a small donation section. Thank you for your support.

== Changelog ==
= 2.10.7 = 
 * Now compatible up to Wordpress 2.9.1!
 * Updated the flash to TFU 2.10.7
 * Wrong message was shown after importing only one image.
 * Improved the normalizeFileNames function
 * The Uploader and the Sync option can now be activated independently in the menu and the media manager tabs
 * The 'Sync' has now a detection of already crunched images of an original. This can be turned off in the settings because the detection is very basic.   
     
= 2.9.1 =
 * No further problems found. First official release of WFU

= 2.9.1 RC 2 =
 * Updated the Flash to TFU 2.9.1.1
 
= 2.9.1 RC 1 =
 * Initial Release - The version does start with 2.9.1 because it is based on TFU 2.9.1
 * This version contains the first basic wrapper for Wordpress. Much more is possible with the TWG Flash Uploader. 
 * No problems found in Beta 1 - Therefore the version is released as RC 1 now.
 *     
Please don't hesitate to post your requirements