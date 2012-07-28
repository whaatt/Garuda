<?php

/* Config File */

/* Site Properties */

$conf['name'] = "Garuda"; //Site Name
$conf['site'] = "http://www.skalon.com"; //Developer Site
$conf['help'] = "http://www.skalon.com/contact/help"; //Help Site
$conf['contact'] = "http://www.skalon.com/contact/garuda"; //Contact Site
$conf['zen'] = "http://totemdog.com/zen/"; //1337 Site

/* Resource Properties */

$conf['css_main'] = "css/style.css"; //Master Stylesheet Location
$conf['css_table'] = "css/table.css"; //Table Stylesheet Location
$conf['css_ui'] = "css/ui.css"; //UI Stylesheet Location
$conf['css_favicon'] = "css/favicon.ico"; //Favicon Location

$conf['js_jQuery'] = "js/jQuery.js"; //jQuery Location
$conf['js_scripts'] = "js/scripts.js"; //Master Scripts Location
$conf['js_tables'] = "js/tables.js"; //Table Script Location
$conf['js_time'] = "js/time.js"; //Time Script Location
$conf['js_ui'] = "js/ui.js"; //UI Script Location

/* Database Properties */

$conf['db_name'] = "<censored>"; //Database Name
$conf['db_user'] = "<censored>"; //Database User
$conf['db_pass'] = "<censored>"; //Database Password

/* Database Connection */

mysql_connect("localhost", $conf['db_user'], $conf['db_pass']) or die(mysql_error()); //Connect
mysql_select_db($conf['db_name']) or die(mysql_error()); //Select

/* Start Session */

session_start();
if (!isset($_SESSION['context'])){$_SESSION['context'] = 'main';}

/* Static Text */

$conf['welcome'] = "<p>Welcome to " . $conf['name'] . ", the cutting-edge tool to edit and assemble Quiz Bowl packets. To get started, click the Register tab above if you have not already created an account. Otherwise, click the Login tab. Please note that access to some parts of the site is permission-based.</p><p>If you would like to host a clone of " . $conf['name'] . " on your site, please contact me through the pertinent link. For technical support or general requests, please use the Help link. Finally, for tournament info, contact your personal administrator. Thanks for coming! Visit back soon.</p>"; //Welcome
$conf['register'] = "<p>The form below will allow you to register for a " . $conf['name'] . " account. Your username, password, and name must be between five and twenty characters (between one and forty for the latter). Passwords should match. Please note your login information carefully, and remember that it is good practice to use your real name. You will only be able to submit your credentials when all of the fields have been filled out. Thanks for your understanding!</p>";//Registration
$conf['login'] = "<p>You may log into " . $conf['name'] . " below. Please use the contact link in the upper right corner of the page if you forget your login. Provide as many details about your account as you can so I can conclusively verify that you are the rightful owner of your account. Sorry about any inconvenience!</p>";//Login
$conf['create'] = "<p>The form below will allow you to create a new set of tournament packets. You must enter a tournament name that is fewer than two hundred characters long. You do not have to necessarily provide a target date. If this is a set for private use and testing, please check the appropriate box. Finally, the tournament info box is where you can write a short description of your set. This must be fewer than one thousand characters. Thank you!</p><p>In the subject selection field, you can list the disciplines into which your tournament's questions will fit. Simply type the names of the topics on separate lines, and please keep these names under fifty characters. You may complete or change this information later, if you'd prefer to do that. Also, setting the allocation and distribution of these subjects will be possible at the time you generate your tournament's packets for print or download.</p>";//Creation
$conf['dashboard'] = "<p>The table below lists tournaments you have permission to access. They are displayed either because you created the set or you joined the set. If the set you are looking for does not appear here, please ask your tournament director for the appropriate passwords, then use the Join page to do so. Tournaments in blue have no target date, tournaments in green will be in the future, and tournaments in red are past their target. Thank you!</p>";
$conf['footer'] = "Hastily coded by <a href='" . $conf['site'] . "'>Sanjay Kannan</a>."; //Copyright

?>