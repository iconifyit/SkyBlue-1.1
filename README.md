SkyBlueCanvas Lightweight CMS is an open source, free content management system written in php and built specifically for small web sites. The entire site you are viewing is a demonstration of the SkyBlueCanvas lightweight CMS. SkyBlueCanvas is custom-built for those instances when more robust systems like Joomla, WordPress and Drupal are too much horsepower.

Lightweight and simple does not mean simplistic, however. SkyBlueCanvas includes a lot of the same basic abilities as more robust systems but in a simpler form. The software is not meant to be all things to all users but it does offer features you expect like a familiar Plugin API, Extensibility and skinnability.

# Installation Instructions

## What You Will Need (System Requirements)

    PHP v4-5.x running on Linux, Unix, FreeBSD, OpenBSD or MacOS X
    Sorry, Windows servers are not supported at this time
    A FTP Client (Get FileZilla from Mozilla for free)
    mod_rewrite enabled (optional but recommended)
    JavaScript enabled in your web browser

## FTP Installation

    Download the SkyBlueCanvas ZIP or TAR file and extract the contents into a folder on your compter.
    Connect to your web server using your FTP client and upload the SkyBlueCanvas code.
    Change the file permissions of the entire skyblue folder to 755 (some servers may require 775). Apply the changes recursively to all directories and files in the skyblue folder.
    Rename the file /skyblue/htaccess.txt to .htaccess (dot htaccess) to enable SEF URLs
    Point your web browser to http://yourdomain.com/ or if you have installed SkyBlueCanvas in a sub-directory of your main domain, http://yourdomain.com/sub_directory_where_skyblue_is_installed/.
    Follow the instructions that appear on your screen. This will be a simple form you need to fill in.
    When you log in to the Admin Control Panel, go to Admin > Settings > Default Info, and enter the info in the form.
    That’s it.

## Command-line Installation (Advanced Users)

    Download the tar file and place it in the root of your new website.
    Unpack the tarball (tar -zxf skyblue.tar.gz)
    Rename the file /skyblue/htaccess.txt to .htaccess (dot htaccess) to enable SEF URLs
    Change the owner/group of the directory contents (chown -R www:www `*`)
    Change the file permissions of the entire skyblue folder to 755 (some servers may require 775). Apply the changes recursively to all directories and files in the skyblue folder.
    Point your web browser to http://yourdomain.com/ or if you have installed SkyBlueCanvas in a sub-directory of your main domain, http://yourdomain.com/sub_directory_where_skyblue_is_installed/.
    Follow the instructions that appear on your screen. This will be a simple form you need to fill in.
    When you log in to the Admin Control Panel, go to Admin > Settings > Default Info, and enter the info in the form. This will enable emailing through your site and will set the full URL so that RSS feeds will point to your site pages.
    That’s it.

## Trouble Shooting

If you get an error when you point your browser to the Setup Wizard, try setting the permissions of the /skyblue/ directory to 777. It is not recommended that you leave the permissions set to 777. The first screen of the Setup Wizard will suggest the proper settings. Once you have completed the installation, you can change the file permissions to the correct setting. If you continue to have trouble, check your hosting provider’s documentation or contact their Support Center and ask for “the correct settings for their server to allow web software to read and write files”.

You are now ready to start adding your site’s content. You can log into the SkyBlueCanvas admin control panel by going to http://www.yourdomain.com/admin.php.
