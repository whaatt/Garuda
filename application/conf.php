<?php

/* Config File */

/* Site Properties */

$conf['name'] = "Garuda"; //Site Name
$conf['path'] = "/garuda/"; //Path To Installation
$conf['site'] = "http://www.skalon.com"; //Developer Site
$conf['help'] = "http://www.skalon.com/contact/help"; //Help Site
$conf['contact'] = "http://www.skalon.com/contact/garuda"; //Contact Site
$conf['play'] = "http://www.skalon.com/qub"; //Play Site
$conf['tracking'] = "UA-XXXXXX-Y"; //Google Analytics Tracking Code

/* Resource Properties */

$conf['css_main'] = "css/style.css"; //Master Stylesheet Location
$conf['css_tables'] = "css/tables.css"; //Table Stylesheet Location
$conf['css_editor'] = "editor/css/redactor.css"; //Editor Stylesheet Location
$conf['css_tooltip'] = "css/tooltip.css"; //Tooltip Stylesheet Location
$conf['css_modal'] = "css/facebox.css"; //Modal Stylesheet Location
$conf['css_ui'] = "css/ui.css"; //UI Stylesheet Location
$conf['css_favicon'] = "css/favicon.ico"; //Favicon Location

$conf['js_jQuery'] = "js/jQuery.js"; //jQuery Location
$conf['js_scripts'] = "js/scripts.js"; //Master Scripts Location
$conf['js_editor'] = "editor/js/redactor.js"; //Editor Script Location
$conf['js_google'] = "https://www.google.com/jsapi"; //Google Location
$conf['js_tables'] = "js/tables.js"; //Table Script Location
$conf['js_modal'] = "js/facebox.js"; //Modal Script Location
$conf['js_tooltip'] = "js/tooltip.js"; //Modal Script Location
$conf['js_time'] = "js/time.js"; //Time Script Location
$conf['js_ui'] = "js/ui.js"; //UI Script Location

/* Database Properties */

$conf['db_name'] = "<censored>"; //Database Name
$conf['db_user'] = "<censored>"; //Database User
$conf['db_pass'] = "<censored>"; //Database Password

/* Database Connection */

$connection = mysqli_connect("localhost", $conf['db_user'], $conf['db_pass'], $conf['db_name']) or die('MYSQL Connection Error'); //Connect

/* Start Session */

session_set_cookie_params(0, $conf['path']); session_start();
if (!isset($_SESSION['context'])){$_SESSION['context'] = 'main';}

/* Static Text */

$conf['description'] = "Collaborate with your friends to write tournaments.";
$conf['welcome'] = "<p>Welcome to " . $conf['name'] . ", the cutting-edge tool to edit and assemble Quiz Bowl packets. To get started, click the Register tab above if you have not already created an account. Otherwise, click the Login tab. Please note that access to some parts of the site is permission-based.</p><p>If you would like to host a clone of " . $conf['name'] . " on your site, please contact me through the pertinent link. For technical support or general requests, please use the Help link. Finally, for tournament info, contact your personal administrator. Thanks for coming! Visit back soon.</p>"; //Welcome
$conf['register'] = "<p>The form below will allow you to register for a " . $conf['name'] . " account. Your username, password, and name must be between five and twenty characters (between one and forty for the latter). Passwords should match. Please note your login information carefully, and remember that it is good practice to use your real name. You will only be able to submit your credentials when all of the fields have been filled out. Thanks for your understanding!</p>";//Registration
$conf['login'] = "<p>You may log into " . $conf['name'] . " below. Please use the contact link in the upper right corner of the page if you forget your login. Provide as many details about your account as you can so I can conclusively verify that you are the rightful owner of your account. Sorry about any inconvenience!</p>";//Login
$conf['create'] = "<p>The form below will allow you to create a new set of tournament packets. You must enter a tournament name that is fewer than two hundred characters long. You do not have to necessarily provide a target date. If this is a set for private use and testing, please check the appropriate box. Finally, the tournament info box is where you can write a short description of your set. This must be fewer than one thousand characters. Thank you!</p><p>In the subject selection field, you can list up to fifty disciplines into which your tournament's questions will fit. Simply type the names of the topics on separate lines, and please keep these names under fifty characters. You may complete or change this information later, if you'd prefer to do that. Also, setting the allocation and distribution of these subjects will be possible at the time you generate your tournament's packets for print or download.</p>";//Creation
$conf['join'] = "<p>The form below will allow you to join a previously created tournament. You must type the tournament's name exactly as it was created; please contact your tournament director to obtain this. You must then type a valid access code for the set, also provided by your tournament director. If the tournament name and code validate, you will be successfully added to the tournament with the appropriate permissions, based on the access code.</p>";
$conf['account'] = "<p>The form below will allow you to edit your account information. Your username is listed in the upper right-hand corner of the page, and cannot be edited for security purposes. If you have an important need to change your username, please contact me with an explanation of your case, and I will definitely consider it. Your name (pre-filled) is modifiable, and you may set a new password, but your current password must validate exactly. Thank you!</p><p>Please note that you do not have to change your password when you submit this form, and you may leave the pertinent field blank. Whatever you input as your name, whether it was pre-filled or edited, will be updated in the database; you may simply leave the name field as-is and only change your password, if you would prefer that. Also note that the standard name and password requirements, as described at registration, are in effect.</p>";
$conf['dashboard'] = "<p>The table below lists tournaments you have permission to access. They are displayed either because you created the set or you joined the set. If the set you are looking for does not appear here, please ask your tournament director for the appropriate passwords, then use the Join page to do so. Tournaments in blue have no target date, tournaments in green will be in the future, and tournaments in red are past their target. Thank you!</p>";
$conf['footer'] = "Hastily coded by <a href=\"" . $conf['site'] . "\">Sanjay Kannan</a>."; //Copyright
$conf['tournament'] = "<p>The information shown below describes the properties of this set or tournament. For security purposes, you currently cannot edit any of the following parameters if you are not an administrator; however, if you have a valid reason to do so, please contact your tournament director who can hopefully help you. Tournament member information (editable by administrators), tossups, and bonuses can all be found on their respective pages in the menu.</p>";
$conf['unauthorized'] = "<p>This page is only for authenticated users. Please refresh the page to restart your session. Sorry for any inconvenience!</p>";
$conf['unauthorized2'] = "<p>This function is only for authenticated users. Please refresh the page to restart your session. Sorry for any inconvenience!</p>";
$conf['noperms'] = "<p>You do not have sufficient tournament permissions to access this function. Sorry about the inconvenience!</p>";
$conf['noperms2'] = "<p>You do not have permissions to access this tournament. Please contact your director. Thanks!</p>";
$conf['codes'] = "<p>As tournament director, you may view the security codes for this set. Please provide these to your personnel when they attempt to gain set access. Once users have joined the tournament membership, only you and administrators may modify user permissions, using the Members page in the main menu above.";
$conf['members'] = "<p>The following table lists users with membership in this tournament or set. This should serve as a directory for most users to view their fellow personnel, and administrators may use the pertinent function to update and modify a user's status. Please contact your tournament director for more information.</p>";
$conf['stats'] = "<p>The four scatterplots below represent the distributions of questions in your set. The plots on the left with the blue and green data points represent individual users, while the plots on the right represent each subject allocation. Within each graph, you may hover over a data point to see specific question counts along with user and subject information. Special thanks to the awesome folks at Google for releasing this Chart API.</p>";
$conf['tossups'] = "<p>The following table lists all tossups associated with this tournament or set. Ordinary editors may add new tossups and edit their own entries. Managers have added access permissions, and are allowed to modify, delete, or approve any tossup associated with their focus subject set on the Members page. Administrators can modify, delete, approve, or promote any tossup shown here. A promoted tossup can be used to generate packets.</p>";
$conf['bonuses'] = "<p>The following table lists all bonuses associated with this tournament or set. Ordinary editors may add new bonuses and edit their own entries. Managers have added access permissions, and are allowed to modify, delete, or approve any bonus associated with their focus subject set on the Members page. Administrators can modify, delete, approve, or promote any bonus shown here. A promoted bonus can be used to generate packets.</p>";
$conf['packets'] = "<p>The first table below lists all of the tossups and bonuses that have been promoted. Underneath the first table, you may download any packets that have been generated. You may either manually or automatically assign questions to packets; to automatically assign the sets, please ";
$conf['deny'] = "<p>You must be signed into a tournament in order to access packets, for security reasons.</p>";
$conf['invalid'] = "<p>The packet you selected is either invalid or has no tossups or bonuses in it.</p>";

?>