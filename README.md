![Ajde logo](https://raw.github.com/hongaar/ajde/master/src/public/images/ajde-medium.png "Ajde logo")

___another web framework___
===========================

*Yet another PHP 5.0 MVC framework with out-of-the-box HTML / CSS / JS / caching and HTTP optimizations. Your project will be fast and cutting edges right from the start!*

**PLEASE NOTE: Ajde is in an early alpha stage, and the API is still changing.**


----------------


Introduction
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


Features
-------------------------

Ajde is not designed with a particular use-case in mind, and can be used as bare-bones web framework (i.e. using only routing and request/response system), as a application component framework, or as a complete CMS solution.

It has however no support for plug and play themes or plugins or whatsoever, so please don't compare it to WordPress and the likes. You will have to write your own templates in pure PHP and be prepared to alter modules to fit your exact specifications.

#### Core

 - Login system
 - Database & query abstraction
 - CRUD
 - ACL layer
 - CMS
 - Shopping cart with support for iDeal (WeDeal) & PayPal
 - Templating in straight PHP or XHTML
 - Dynamic routing
 - Web debugger & console
 - Mail class
 - much more...

#### Out of the box modules supporting

  - Google hosted JS libraries (jQuery, Prototype, etc.)
  - Google web fonts
  - Cookie Consent
  - Embed code generator for YouTube, Vimeo, SoundCloud & Mixcloud (OEmbed discovery planned)
  - Twitter Bootstrap 3
  - Fancybox 2
  - CKEditor 4
  - Image manipulation (supports PNG alpha layer resampling)
  - QR code generation
  - Gravatar
  - much more...


----------------




Example
-------------------------

#### Controller

```php
class SampleController extends Ajde_Acl_Controller
{
  public function sayhello()
  {
    // Set document title
    Ajde::app()->getDocument()->setTitle("A warm welcome word");

    // Set the text to display in the template
    $this->getView()->assign('message', 'Hello World!');

    // Returns the rendered template, which automatically adds js & css
    return $this->render();
  }
}
```

#### Template

```php
<?php
/* @var $this Ajde_Template_Parser_Phtml_Helper */
$this->requireJsLibrary('jquery', '1');
$this->requireJsRemote('http://www.cornify.com/js/cornify.js');
?>

<header class="row">
  <div class="col-12">
    <div class="page-header">
      <h1>
        <?php echo _e(Ajde::app()->getDocument()->getTitle()); ?>
      </h1>
    </div>
  </div>
</header>

<article class="row">
  <section class="col-12">
    <p>
      <?php echo _e($this->message); ?>
    </p>
  </section>
</article>
```

#### Javascript

```javascript
$(document).ready(function() {
        setInterval(function() {
                cornify_add();
        }, 1000);
});
```


#### Stylesheet

```css
/*#!less*/
@pink: #ff31d5;

body {
  background-color: @pink;
}
```

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

#### Secret

Create a new random string (preferably 32 characters or more) and put it in `private/config/Config_Application.php` (replace `randomstring`)

#### Prepare database

Create a new database, and run `private/dev/data.sql` on this database. Edit the database credentials in `private/config/Config_Application.php` to match the newly created database.

#### Prepare admin user

Register for a new account on `http://[WEBROOT]/user/register` and change the `usergroup` field of this user in the database (`user` table) to `2`. Now you can login on `http://[WEBROOT]/admin`.
Alternatively, log in as `admin` with password `ajde`. However, please create a new admin account, and delete the original admin user account afterwards.


----------------




Security
-------------------------


#### Register Globals

register_globals is turned off in the .htaccess file.

#### Error Reporting

If debugging is turned off (with setting the configuration variable $debug to false in a production environment), errors and exceptions are never shown in the front-end. Instead, they are logged to private/var/log (make sure this directory is writable, otherwise Ajde will error out horrendously).

#### Cross-Site Scripting (XSS)
Ajde aggressively filters out HTML from requests when using `Ajde_Http_Request`.

Ajde looks for the configuration variables `autoEscapeString` and `autoCleanHtml`, and when set to true, it applies `htmlspecialchars` and `strip_tags` - with only a limited set of allowed tags - to request variables retrieved with `Ajde_Http_Request::get()|getParam()|getString()` and `Ajde_Http_Request::getHtml()` respectively.

However, it is always the responsibility of the developer to filter user input!

**Note: When using `HTMLPurifier`, `Ajde_Http_Request::getHtml()` uses `HTMLPurifier::purify()` instead.**

**Note: Ajde doesn't prevent developers from using the raw `$_GET and $_POST` objects, which can contain unsafe data!**

#### Cross-Site Request Forgery (CSRF) Prevention

When using `Ajde_Component_Form` helper functions (`this->ACForm()` or `this->ACAjaxForm()`) in a template, and the configuration variables `requirePostToken` is set to true, Ajde will validate form token and timestamp to prevent CSRF attacks.

#### Session hijacking

Sessions are strengthened with the client user agent and IP address. On each request, the session is validated, eliminating several session hijacking attack vectors.

#### Exposing Sensitive Information

All scripts are located in the private/ folder, which is prevented from being accessed from outside. Server-side scripts in the public/ folder are never executed.

Only the private/var/* directories should be writable by the webserver.

#### User authentication

When using the Ajde_User extension, user cookies are hashed with the client IP address, a unique user secret and application secret, making it virtually impossible to use a stolen user authentication cookie.