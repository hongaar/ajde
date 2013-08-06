Ajde is a web framework to kickstart your PHP projects
======================================================

*Yet another PHP 5.0 MVC framework with out-of-the-box HTML / CSS / JS / caching and HTTP optimizations. Your project will be fast and cutting edges right from the start!*

**PLEASE NOTE: Ajde is in an early alpha stage, and the API is still changing.**


----------------


Features
-------------------------

#### A framework for Grade A performance

Ajde aims to be a framework for creating fast websites. Employing out-of-the box minifiers + combining for resources and caching them to relieve your server, taking care of all the right headers and .htaccess settings for you, Ajde lifts the performance of your application to a next level.

#### A framework for designers

A simple to use API and a straightforward templating system for your web application. Ajde will automatically include your related JavaScript and stylesheets if you follow the naming convention and you can write your templates in native PHP or entirely in pre-parsed XHTML.

Our main goal is to facilitate you writing your template and navigation structure fast with no need to compile or run any scripts. We provide the latest Bootstrap CSS code and JavaScript libraries can be loaded dynamically as needed.

Writing your application logic and extending Ajde is easy and extensive exception handling is turned on by default for the development environment.

#### A framework for developers

Onboard support for multiple languages, human readable (SEO) URLs, event binding, session and cookie management, security measures, and external libraries. Ajde is a MVC/CMS framework, and provides you with a database abstraction layer (based on PDO, MySQL adapter available) and an easy CRUD interface.

It follows the naming conventions of the Zend Framework and you can use most of the ZF components in your application. We already streamlined inclusion of this library for you! (Just put it in the lib/ directory, but beware, the small footprint of your application will be gone...)

#### A framework for clients

Current development on the Ajde CMS is taking place in the `cms` branch. To get the latest snapshot of Ajde, make sure to clone this branch.

----------------


Install
-------------------------

#### Server configuration

You need the following to run Ajde:

 - Linux, Mac or Windows 
 - Apache webserver
 - PHP, version 5.2.3 or newer
 - MySQL

Directories which have to be writable by the webserver:

 - private/var/cache
 - private/var/tmp
 - private/var/log
 - public/images/uploads

#### Directories to delete in production

Remove these files and directories to cleanup your Ajde install for production:

 - loadtest.php
 - phpinfo.php
 - test/

#### Security

Create a new random string (preferably 32 characters or more) and put it in `private/config/Config_Application.php` (replace `randomstring`)

#### Prepare database

Create a new database, and run `private/dev/data.sql` on this database. Edit the database credentials in `private/config/Config_Application.php` to match the newly created database.

#### Prepare admin user

Register for a new account on `http://[WEBROOT]/user/register` and change the `usergroup` field of this user in the database (`user` table) to `2`. Now you can login on `http://[WEBROOT]/admin`.
Alternatively, log in as `admin` with password `ajde`. However, please create a new admin account, and delete the original admin user account afterwards. 