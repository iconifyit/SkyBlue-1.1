_NOTE: SkyBlueCanvas was originally written in 2003 and was faithfully maintained until 2012. But alas, time moves on and interests change. The project is no longer being actively maintained but the code is very stable. Feel free to download and use it. I am happy to answer questions about the code from time-to-time but I'm not very focused on the CMS any longer._

# Introduction

SkyBlueCanvas is built for small web sites and non-technical users. The system distills the core concepts of content editing down to their simplest form and eliminates a lot of the complexities associated with larger systems. SkyBlueCanvas is not meant to be all things to all users and is not intended to compete with, but to act as an alternative to more robust systems.
Overview

A SkyBlueCanvas site is broken down into five simple units. These units are:

* Pages
* Media (Pictures and Files)
* Collections
* Templates (Skins)
* Settings

## Pages

SkyBlueCanvas is built first-and-foremost around the concept of pages as the base container. A page can contain text, pictures and other containers (also called collections).

When a new page is created in SkyBlueCanvas, it is automatically associated with a menu item. You create a page, add text and pictures, then indicate in which menu the page should appear (or no menu at all).
Media

SkyBlueCanvas currently supports two types of media – pictures and files. Files are downloadable assets such as PDFs, Microsoft Office documents and ZIP or TAR files. The system allows you to upload files, move them between directories and rename them. Additionally, in the list view, pictures can be previewed by simply holding the cursor over the camera icon beside the image name.

## Collections

Collections are perhaps the trickiest concept to grasp in SkyBlueCanvas. A collection is a type of container made up of a variable number of items. Examples of collections include link lists, photo galleries, FAQs and Menus. They are literally collections of things or smaller units that appear within a page. The concept of a collection, allows you, the user to add, edit and delete individual items within a container without having to worry about building the container itself.

## Templates

Templates – or skins – are the presentation layer of a web site and allow you to change the appearance of your site without making any changes to the content (text and pictures). The SkyBlueCanvas template system is very simple and very flexible. What your site looks like is limited only by your imagination.

Skins are comprised of XHTML, CSS, JavaScript? and image files. SkyBlueCanvas templates contain absolutely no executable code like PHP and incorporates a simple token replacement concept. You build your XHTML files as you would for any web page and indicate a content region using simple tokens like {region:header}, {region:left} and {region:main}.

## Settings

As stated previously, SkyBlueCanvas is a zero-configuration system, meaning there are no complex configuration options and settings to manipulate before the system is ready to use. The settings to which we now refer are things like the Site Name, Default Contact Info and administrator username and password.