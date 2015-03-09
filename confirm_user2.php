<?php 


	define('IN_PHPBB', true);                               //      Checked by phpBB
	$phpEx = 'php'; //substr(strrchr(__FILE__, '.'), 1);     //      Needed by phpBB
	$phpbb_root_path = 'phpBB3/'; // MY_ROOOT . '/forum/';                //      The forums root.
	include $phpbb_root_path . 'common.' . $phpEx; //      The file loader.
	
	include $phpbb_root_path . 'includes/functions_user.php';
	include $phpbb_root_path . 'includes/ucp/ucp_register.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php

session_start();

include 'inc/config2.php';
include $conf_include_path .'comm.php';	
include $conf_include_path .'connect.inc';

if(!$_GET['lang'] && !$_SESSION['misc']['lang']) 
	$_GET['lang'] = $conf_default_lang;
	
if($_GET['lang'])
	$_SESSION['misc']['lang'] = $_GET['lang'];

include $conf_include_path .'translation.php'; 


?>
<title>::: ROCAYA :::</title>
<link href="ccs/main.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table width="565" border="0" cellspacing="5" cellpadding="5" align="center">
  <tr>
    <td class="default_text"><?php
$all_ok = false;

if($_GET['code']) {
	if(exists_record('users', 'reg_control', $_GET['code'])) {
		$user_data_array = simple_select('users', 'reg_control', $_GET['code'], array('user_id', 'email', 'uname', 'picture', 'default_lan'));
		
		$update_user = my_query('UPDATE users SET active = \'1\', reg_control = NULL, picture = NULL WHERE reg_control = \''. $_GET['code'] .'\'', $conex);
		if($update_user) {
			# Assign user to modules
			$sql = 'INSERT INTO user_modules (user_id, mod_id, access, modify) VALUES 
				('. $user_data_array['user_id'] .', \'forum\', \'1\', \'1\'),
				('. $user_data_array['user_id'] .', \'home\', \'1\', \'1\'),
				('. $user_data_array['user_id'] .', \'report\', \'1\', \'1\'),
				('. $user_data_array['user_id'] .', \'routes\', \'1\', \'1\')';

			$insert_user_mods = my_query($sql, $conex);

			if($insert_user_mods) {
				print('<p>'. user_reg_done .'</p>');
				$all_ok = true;
			}
		}
	}
}

if($all_ok) {

	// BEGIN register user in forum ---------------------------------------------------		
	// username of the user being added
	$username = $user_data_array['uname']; //$_POST['name'];
	
	// the user’s password, which is hashed before inserting into the database
	$password = decode($user_data_array['picture']); //$_POST['pass'];

	// an email address for the user
	$email_address = $user_data_array['email']; //$_POST['user'];
	
	// default is 4 for registered users, or 5 for coppa users.
	$group_id = ($coppa) ? 5 : 4;
	// since group IDs may change, you may want to use a query to make sure you are grabbing the right default group...
	$group_name = ($coppa) ? 'REGISTERED_COPPA' : 'REGISTERED';
	$sql = 'SELECT group_id FROM ' . GROUPS_TABLE . " WHERE group_name = '" . $db->sql_escape($group_name) . "'	AND group_type = " . GROUP_SPECIAL;

	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$group_id = $row['group_id'];
	
	// timezone of the user... Based on GMT in the format of '-6', '-4', 3, 9 etc...
	$timezone = '0';
	
	// two digit default language for this use of a language pack that is installed on the board.
	$language = substr($user_data_array['default_lan'], 0, 2);
	
	// user type, this is USER_INACTIVE, or USER_NORMAL depending on if the user needs to activate himself, or does not.
	// on registration, if the user must click the activation link in their email to activate their account, their account
	// is set to USER_INACTIVE until they are activated. If they are activated instantly, they would be USER_NORMAL
	$user_type = USER_NORMAL;
	
	// here if the user is inactive and needs to activate thier account through an activation link sent in an email
	// we need to set the activation key for the user... (the goal is to get it about 10 chars of randomization)
	// you can use any randomization method you want, for this example, I’ll use the following...
	$user_actkey = md5(rand(0, 100) . time());
	$user_actkey = substr($user_actkey, 0, rand(8, 12));
	
	// IP address of the user stored in the Database.
	$user_ip = $user->ip;
	
	// registration time of the user, timestamp format.
	$registration_time = time();
	
	// inactive reason is the string given in the inactive users list in the ACP.
	// there are four options: INACTIVE_REGISTER, INACTIVE_PROFILE, INACTIVE_MANUAL and INACTIVE_REMIND
	// you do not need this if the user is not going to be inactive
	// more can be read on this in the inactive users section
//			$user_inactive_reason = INACTIVE_REGISTER;
	
	// time since the user is inactive. timestamp.
//			$user_inactive_time = time();
	
	// these are just examples and some sample (common) data when creating a new user.
	// you can include any information 
	$user_row = array(
		'username'              => $username,
		'user_password'         => phpbb_hash($password),
		'user_email'            => $email_address,
		'group_id'              => (int) $group_id,
		'user_timezone'         => (float) $timezone,
		'user_dst'              => $is_dst,
		'user_lang'             => $language,
		'user_type'             => $user_type,
		'user_actkey'           => $user_actkey,
		'user_ip'               => $user_ip,
		'user_regdate'          => $registration_time,
		'user_inactive_reason'  => $user_inactive_reason,
		'user_inactive_time'    => $user_inactive_time,
	);

	// Custom Profile fields, this will be covered in another article.
	// for now this is just a stub
	// all the information has been compiled, add the user
	// the user_add() function will automatically add the user to the correct groups
	// and adding the appropriate database entries for this user...
	// tables affected: users table, profile_fields_data table, groups table, and config table.
	$user_id = user_add($user_row);		

	if($user_id) {
		print('<strong>'. user_registered_in_forum .'</strong><br>');
	}

	// END register user in forum ---------------------------------------------------



//	print('<script language="javascript"> document.location = "index.php"; </script>');
//	session_unset();
//	exit();
}
  ?>
      <a href="<?= $conf_main_page; ?>"><?php echo return_2_main; ?></a> </td>
  </tr>
</table>
</body>
</html>
