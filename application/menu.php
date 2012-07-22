<?php 

require_once('conf.php');

/* Menu Handler */

if(isset($_POST['link'])){ $link = $_POST['link']; } //Actual Link 
else{ $link = 'Default'; } //No POST Parameter

$context = $_SESSION['context']; //User Screen

switch($context){
	case 'main': //Without Authentication
		switch($link){
			case 'Welcome':
				?>
				<ul>
					<li><a class="active"><span>Welcome</span></a></li>
					<li><a onclick="go_register(); return false;"><span>Register</span></a></li>
					<li><a onclick="go_login(); return false;"><span>Login</span></a></li>
				</ul>
				<? break;		
			case 'Register':
				?>
				<ul>
					<li><a onclick="go_welcome(); return false;"><span>Welcome</span></a></li>
					<li><a class="active"><span>Register</span></a></li>
					<li><a onclick="go_login(); return false;"><span>Login</span></a></li>
				</ul>
				<? break;
			case 'Login':
				?>
				<ul>
					<li><a onclick="go_welcome(); return false;"><span>Welcome</span></a></li>
					<li><a onclick="go_register(); return false;"><span>Register</span></a></li>
					<li><a class="active"><span>Login</span></a></li>
				</ul>
				<? break;
			default:
				?>
				<ul>
					<li><a class="active"><span>Welcome</span></a></li>
					<li><a onclick="go_register(); return false;"><span>Register</span></a></li>
					<li><a onclick="go_login(); return false;"><span>Login</span></a></li>
				</ul>
				<? break;
		}
		break;
	case 'dash': //Dashboard Pages
		switch($link){
			case 'Dashboard':
				?>
				<ul>
					<li><a class="active"><span>Dashboard</span></a></li>
					<li><a onclick="go_create(); return false;"><span>Create</span></a></li>
					<li><a onclick="go_join(); return false;"><span>Join</span></a></li>
					<li><a onclick="go_logout(); return false;"><span>Logout</span></a></li>
				</ul>
				<? break;		
			case 'Create':
				?>
				<ul>
					<li><a onclick="go_dashboard(); return false;"><span>Dashboard</span></a></li>
					<li><a class="active"><span>Create</span></a></li>
					<li><a onclick="go_join(); return false;"><span>Join</span></a></li>
					<li><a onclick="go_logout(); return false;"><span>Logout</span></a></li>
				</ul>
				<? break;
			case 'Join':
				?>
				<ul>
					<li><a onclick="go_dashboard(); return false;"><span>Dashboard</span></a></li>
					<li><a onclick="go_create(); return false;"><span>Create</span></a></li>
					<li><a class="active"><span>Join</span></a></li>
					<li><a onclick="go_logout(); return false;"><span>Logout</span></a></li>
				</ul>
				<? break;
			default:
				?>
				<ul>
					<li><a class="active"><span>Dashboard</span></a></li>
					<li><a onclick="go_create(); return false;"><span>Create</span></a></li>
					<li><a onclick="go_join(); return false;"><span>Join</span></a></li>
					<li><a onclick="go_logout(); return false;"><span>Logout</span></a></li>
				</ul>
				<? break;
		}
		break;
}

?>