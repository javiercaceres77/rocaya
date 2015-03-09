<?php
session_start();

include_once 'inc/config2.php';
include_once $conf_include_path .'comm.php';	
include_once $conf_include_path .'connect.inc';

$error = '';
if(!isset($_SESSION['Login']['login_attempts']))
	$_SESSION['Login']['login_attempts'] = 0;

if(!(check_value($_POST) && check_value($_GET))) {
	unset($_POST);	unset($_GET);
}
else {
//print('check_user: '. check_user($_POST['user'], $_POST['pass'])); exit();
	if(!check_user($_POST['user'], $_POST['pass'])) {
		if($_SESSION['Login']['login_attempts'] <= $conf_max_login_attepmts) {
			# bad user, write in the log and increase number of attempts
			$_SESSION['Login']['login_attempts']++;
			$message = 'Wrong user login attepmt number: '. $_SESSION['Login']['login_attempts'] .' - '. $_POST['user'];
			write_log('bad_login', $message);

			print('<script language="javascript"> document.location="'. $conf_main_page .'?login=wrong" </script>');
			exit();
		}
		else {
			# too many attepts to login
			unset($_POST, $_GET);
			session_unset();
			//block_ip();
			print('<script language="javascript"> document.location="'. $conf_main_page .'?login=blockeduser" </script>');
			exit();
		}
	}
	else {
		//$_SESSION['Login']['Code_User'] = $_POST['user'];
		$user_arr = simple_select('users', 'email', $_POST['user'], array('email', 'user_id', 'uname', 'default_lan'));
		$_SESSION['Login']['UserID'] = $user_arr['user_id'];
		$_SESSION['Login']['email'] = $user_arr['email'];
		$_SESSION['Login']['User_Name'] = $user_arr['uname'];
		$_SESSION['misc']['lang'] = $user_arr['default_lan'];
		
		refresh_users_modules(true);
	
		$message = 'Login: '. $_POST['user'] .' ['. $_SESSION['misc']['lang'] .']';
		write_log('login_user', $message);
//-BEGIN -------------------------------------------------------------------- Login user in forum ----------------------------------
/*		define('IN_PHPBB', true);
		$phpEx = 'php'; //substr(strrchr(__FILE__, '.'), 1);     //      Needed by phpBB
		$phpbb_root_path = 'phpBB3/'; // MY_ROOOT . '/forum/';                //      The forums root.
		include($phpbb_root_path . 'common.' . $phpEx);
		 
		// Start session management
		$user->session_begin();
		$auth->acl($user->data);
		$user->setup();
		
		$user_ok = false;
		
		if($user->data['is_registered'])
		{
			//User is already logged in
			$user_ok = true;
		}
		else
		{

			$username = $user_arr['uname']; //request_var('username', '', true);
			$password = request_var('pass', '', true);

			$result = $auth->login($username, $password);
		
			if ($result['status'] == LOGIN_SUCCESS)
			{
				$user_ok = true;
			}
			else
			{
				$user_ok = true; // even if the user is not registered in the forum, allow to login
			}
		}*/
//-END -------------------------------------------------------------------- Login user in forum ----------------------------------		
//		if($user_ok) {
			?>
			<script language="javascript">
				document.location = '<?= $conf_main_page; ?>?PHPSESSID=<?= $_POST['PHPSESSID'] ?>'; // if not passed by GET, $session is lost.
			</script>
			<?php
//		}
	}
}
?>