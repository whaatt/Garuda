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
		
	//I kept the tournament pages with the dashboard pages, because on a refresh, the user should return to the dashboard.
	//Consider changing this later, but it seems to be the most seamless and unobtrusive solution for now.
		
	case 'dash': //Dashboard Pages
		switch($link){
			case 'Dashboard':
				?>
				<ul>
					<li><a class="active"><span>Dashboard</span></a></li>
					<li><a onclick="go_create(); return false;"><span>Create</span></a></li>
					<li><a onclick="go_join(); return false;"><span>Join</span></a></li>
					<li><a onclick="go_account(); return false;"><span>Account</span></a></li>
					<li><a onclick="go_logout(); return false;"><span>Logout</span></a></li>
				</ul>
				<? break;		
			case 'Create':
				?>
				<ul>
					<li><a onclick="go_dashboard(); return false;"><span>Dashboard</span></a></li>
					<li><a class="active"><span>Create</span></a></li>
					<li><a onclick="go_join(); return false;"><span>Join</span></a></li>
					<li><a onclick="go_account(); return false;"><span>Account</span></a></li>
					<li><a onclick="go_logout(); return false;"><span>Logout</span></a></li>
				</ul>
				<? break;
			case 'Join':
				?>
				<ul>
					<li><a onclick="go_dashboard(); return false;"><span>Dashboard</span></a></li>
					<li><a onclick="go_create(); return false;"><span>Create</span></a></li>
					<li><a class="active"><span>Join</span></a></li>
					<li><a onclick="go_account(); return false;"><span>Account</span></a></li>
					<li><a onclick="go_logout(); return false;"><span>Logout</span></a></li>
				</ul>
				<? break;
			case 'Account':
				?>
				<ul>
					<li><a onclick="go_dashboard(); return false;"><span>Dashboard</span></a></li>
					<li><a onclick="go_create(); return false;"><span>Create</span></a></li>
					<li><a onclick="go_join(); return false;"><span>Join</span></a></li>
					<li><a class="active"><span>Account</span></a></li>
					<li><a onclick="go_logout(); return false;"><span>Logout</span></a></li>
				</ul>
				<? break;
			case 'Tournament':
				?>
				<ul>
					<li><a class="active"><span>Tournament</span></a></li>
					<li><a onclick="go_members(); return false;"><span>Members</span></a></li>
					<li><a onclick="go_tossups(); return false;"><span>Tossups</span></a></li>
					<li><a onclick="go_bonuses(); return false;"><span>Bonuses</span></a></li>
					<li><a onclick="go_packets(); return false;"><span>Packets</span></a></li>
					<li><a onclick="go_leave(); return false;"><span>Leave</span></a></li>
				</ul>
				<? break;
			case 'Members':
				?>
				<ul>
					<li><a onclick="go_tournament(); return false;"><span>Tournament</span></a></li>
					<li><a class="active"><span>Members</span></a></li>
					<li><a onclick="go_tossups(); return false;"><span>Tossups</span></a></li>
					<li><a onclick="go_bonuses(); return false;"><span>Bonuses</span></a></li>
					<li><a onclick="go_packets(); return false;"><span>Packets</span></a></li>
					<li><a onclick="go_leave(); return false;"><span>Leave</span></a></li>
				</ul>
				<? break;
			case 'Tossups':
				?>
				<ul>
					<li><a onclick="go_tournament(); return false;"><span>Tournament</span></a></li>
					<li><a onclick="go_members(); return false;"><span>Members</span></a></li>
					<li><a class="active"><span>Tossups</span></a></li>
					<li><a onclick="go_bonuses(); return false;"><span>Bonuses</span></a></li>
					<li><a onclick="go_packets(); return false;"><span>Packets</span></a></li>
					<li><a onclick="go_leave(); return false;"><span>Leave</span></a></li>
				</ul>
				<? break;
			case 'Bonuses':
				?>
				<ul>
					<li><a onclick="go_tournament(); return false;"><span>Tournament</span></a></li>
					<li><a onclick="go_members(); return false;"><span>Members</span></a></li>
					<li><a onclick="go_tossups(); return false;"><span>Tossups</span></a></li>
					<li><a class="active"><span>Bonuses</span></a></li>
					<li><a onclick="go_packets(); return false;"><span>Packets</span></a></li>
					<li><a onclick="go_leave(); return false;"><span>Leave</span></a></li>
				</ul>
				<? break;
			case 'Packets':
				?>
				<ul>
					<li><a onclick="go_tournament(); return false;"><span>Tournament</span></a></li>
					<li><a onclick="go_members(); return false;"><span>Members</span></a></li>
					<li><a onclick="go_tossups(); return false;"><span>Tossups</span></a></li>
					<li><a onclick="go_bonuses(); return false;"><span>Bonuses</span></a></li>
					<li><a class="active"><span>Packets</span></a></li>
					<li><a onclick="go_leave(); return false;"><span>Leave</span></a></li>
				</ul>
				<? break;
			default:
				?>
				<ul>
					<li><a class="active"><span>Dashboard</span></a></li>
					<li><a onclick="go_create(); return false;"><span>Create</span></a></li>
					<li><a onclick="go_join(); return false;"><span>Join</span></a></li>
					<li><a onclick="go_account(); return false;"><span>Account</span></a></li>
					<li><a onclick="go_logout(); return false;"><span>Logout</span></a></li>
				</ul>
				<? break;
		}
		break;
}

?>