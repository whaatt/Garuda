<?php

/* Config File */

/* Site Properties */

$conf['name'] = "Garuda"; //Site Name
$conf['site'] = "http://www.skalon.com"; //Developer Site
$conf['help'] = "http://www.skalon.com/contact/help"; //Help Site
$conf['contact'] = "http://www.skalon.com/contact/garuda"; //Contact Site
$conf['zen'] = "http://totemdog.com/zen/"; //1337 Site

/* Resource Properties */

$conf['css1'] = "css/style.css"; //Stylesheet One Location
$conf['css2'] = "css/table.css"; //Stylesheet Two Location
$conf['css3'] = "css/ui.css"; //Stylesheet Three Location
$conf['favicon'] = "css/favicon.ico"; //Favicon Location

$conf['jQ'] = "js/jQuery.js"; //jQuery Location
$conf['js1'] = "js/scripts.js"; //Script One Location
$conf['js2'] = "js/dataTables.js"; //Script Two Location
$conf['js3'] = "js/UI.js"; //Script Three Location

/* Database Properties */

$conf['db_name'] = ""; //Database Name
$conf['db_user'] = ""; //Database User
$conf['db_pass'] = ""; //Database Password

/* Database Connection */

mysql_connect("localhost", $conf['db_user'], $conf['db_pass']) or die(mysql_error()); //Connect
mysql_select_db($conf['db_name']) or die(mysql_error()); //Select

/* Start Session */

session_start();
if (!isset($_SESSION['context'])){$_SESSION['context'] = 'main';}

/* Static Text */

$conf['welcome'] = "<p>Welcome to " . $conf['name'] . ", the cutting-edge tool to edit and assemble Quiz Bowl packets. To get started, click the Register tab above if you have not already created an account. Otherwise, click the Login tab. Please note that access to some parts of the site is permission-based.</p><p>If you would like to host a clone of " . $conf['name'] . " on your site, please contact me through the pertinent link. For technical support or general requests, please use the Help link. Finally, for tournament info, contact your personal administrator. Thanks for coming! Visit back soon.</p>"; //Welcome
$conf['register'] = "<p>The form below will allow you to register for a " . $conf['name'] . " account. Your username, password, and name must be between five and twenty characters (between one and forty for the latter). Passwords should match. Please note your login information carefully, and remember that it is good practice to use your real name. You will only be able to submit your credentials when all of the fields have been filled out. Thanks for your understanding!</p>";
$conf['login'] = "<p>You may log into " . $conf['name'] . " below. Please use the contact link in the upper right corner of the page if you forget your login. Provide as many details about your account as you can so I can conclusively verify that you are the rightful owner of your account. Sorry about any inconvenience!</p>";
$conf['footer'] = "Hastily coded by <a href='" . $conf['site'] . "'>Sanjay Kannan</a>."; //Copyright

?>