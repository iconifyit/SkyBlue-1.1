_NOTE: SkyBlueCanvas was originally written in 2003 and was faithfully maintained until 2012. But alas, time moves on and interests change. The project is no longer being actively maintained but the code is very stable. Feel free to download and use it. I am happy to answer questions about the code from time-to-time but I'm not very focused on the CMS any longer._

SkyBlueCanvas Lightweight CMS is an open source, free content management system written in php and built specifically for small web sites. The entire site you are viewing is a demonstration of the SkyBlueCanvas lightweight CMS. SkyBlueCanvas is custom-built for those instances when more robust systems like Joomla, WordPress and Drupal are too much horsepower.

Lightweight and simple does not mean simplistic, however. SkyBlueCanvas includes a lot of the same basic abilities as more robust systems but in a simpler form. The software is not meant to be all things to all users but it does offer features you expect like a familiar Plugin API, Extensibility and skinnability.

# Installation Instructions

## What You Will Need (System Requirements)

* PHP v4-5.x running on Linux, Unix, FreeBSD, OpenBSD or MacOS X
* Sorry, Windows servers are not supported at this time
* A FTP Client (Get FileZilla from Mozilla for free)
* mod_rewrite enabled (optional but recommended)
* JavaScript enabled in your web browser

## FTP Installation

1. Download the SkyBlueCanvas ZIP or TAR file and extract the contents into a folder on your compter.
2. Connect to your web server using your FTP client and upload the SkyBlueCanvas code.
3. Upload the contents of the ./SkyBlue-1.1/cms/ folder to your website directory
4. Change the file permissions of the entire skyblue folder to 755 (some servers may require 775). Apply the changes recursively to all directories and files in the skyblue folder.
5. Rename the file /skyblue/htaccess.txt to .htaccess (dot htaccess) to enable SEF URLs
6. Point your web browser to http://yourdomain.com/ or if you have installed SkyBlueCanvas in a sub-directory of your main domain, http://yourdomain.com/sub_directory_where_skyblue_is_installed/.
7. Follow the instructions that appear on your screen. This will be a simple form you need to fill in.
8. When you log in to the Admin Control Panel, go to Admin > Settings > Default Info, and enter the info in the form.
That’s it.

## Command-line Installation (Advanced Users)

1. Download the tar file and place it in the root of your new website.
2. Unpack the ZIP or TAR file to your computer
3. Upload the contents of the ./SkyBlue-1.1/cms/ folder to your website directory
4. Rename the file /skyblue/htaccess.txt to .htaccess (dot htaccess) to enable SEF URLs
5. Change the owner/group of the directory contents (chown -R www:www `*`)
6. Change the file permissions of the entire skyblue folder to 755 (some servers may require 775). Apply the changes recursively to all directories and files in the skyblue folder.
7. Point your web browser to http://yourdomain.com/ or if you have installed SkyBlueCanvas in a sub-directory of your main domain, http://yourdomain.com/sub_directory_where_skyblue_is_installed/.
8. Follow the instructions that appear on your screen. This will be a simple form you need to fill in.
9. When you log in to the Admin Control Panel, go to Admin > Settings > Default Info, and enter the info in the form. This will enable emailing through your site and will set the full URL so that RSS feeds will point to your site pages.
10. That’s it.

## Trouble Shooting

If you get an error when you point your browser to the Setup Wizard, try setting the permissions of the /skyblue/ directory to 777. It is not recommended that you leave the permissions set to 777. The first screen of the Setup Wizard will suggest the proper settings. Once you have completed the installation, you can change the file permissions to the correct setting. If you continue to have trouble, check your hosting provider’s documentation or contact their Support Center and ask for “the correct settings for their server to allow web software to read and write files”.

You are now ready to start adding your site’s content. You can log into the SkyBlueCanvas admin control panel by going to http://www.yourdomain.com/admin.php.
