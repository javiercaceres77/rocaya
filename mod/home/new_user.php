<?php

// initialize all error check variables
$review_errors = false;
$name_is_not_empty = true;
$email_is_valid = true;
$user_already_exist = false;
$password_long_enough = true;
$passwords_match = true;
$captcha_ok = true;

if($_GET['func'] == 'save') {
	if(check_value($_POST)) {
		$_SESSION['misc']['reg_user']['user'] = $_POST['user'];
		$_SESSION['misc']['reg_user']['name'] = $_POST['name'];
		
		// Check that name is not empty
		$name_is_not_empty = $_POST['name'] != '';
		
		// Check e-mail address
		$email_is_valid = check_email($_POST['user']);
		if($email_is_valid) {
			$user_already_exist = exists_record('users', 'email', $_POST['user']);
		}
		
		// Check that passwords match and are long enough
		$password_long_enough = (strlen($_POST['pass'])	>= 5 && strlen($_POST['pass']) <= 12);
		if($password_long_enough) {
			$passwords_match = $_POST['pass'] == $_POST['pass2'];
		}

		// Check captcha
		$captcha_ok = $_POST['captcha'] == $_SESSION['misc']['captcha'];
		
		// if all the above is ok, proceed to register
		if($email_is_valid && !$user_already_exist && $password_long_enough && $passwords_match && $captcha_ok && $name_is_not_empty) {
			$ob_word = encode($_POST['pass']);
			$word = digest(substr($_POST['user'],0,2) . $_POST['pass']);
			//$reg_in_forum = $_POST['register_forum'] == 'on'?1:0;
			$no_info = $_POST['no_info'] == 'on'?1:0;
			
			$check_code = md5(rand());
			
			$sql = 'INSERT INTO users (uname, email, word, active, default_lan, reg_control, date_registered, no_info, picture)
VALUES (\''. $_POST['name'] .'\', \''. $_POST['user'] .'\', \''. $word .'\', \'0\', \''. $_SESSION['misc']['lang'] .'\', \''. $check_code .'\', \''. date('Y-m-d') .'\', \''. $no_info .'\', \''. $ob_word .'\')';
			$insert_user = my_query($sql, $conex);

			if(!$insert_user) {
				echo 'There was an error while inserting the user-id in the database<br><a href="'. conf_main_page .'">'. return_2_main .'</a>';
				exit();
			}
		}
		else
			$review_errors = true;
	}
}

# Send confirmation e-mail to the user
if($insert_user) {
	$to = $_POST['user'];
	// subject
	$subject = defined('mail_conf_subject')? mail_conf_subject : 'Registry confirmation in ROCAYA.COM';
	// message
	$message = '
<html>
<body>
  <p>'. ucfirst(hello) .'</p>
  <p>'. mail_conf_line1 .'<br />
     '. mail_conf_line2 .'</p>
  <p><a href="http://www.rocaya.com/confirm_user2.php?code='. $check_code .'">http://www.rocaya.com/confirm_user2.php?code='. $check_code .'</a></p>
  <p>'. mail_conf_line3 .'</p>
</body>
</html>
';
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	// Additional headers
	$headers .= 'To: '. $_POST['name'] .'<'. $_POST['user'] .'>' . "\r\n";
	$headers .= 'From: No Reply <no_replay@rocaya.com>' . "\r\n";
	//$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
	//$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";
	
	// Mail it
	$email_sent = mail($to, $subject, $message, $headers);

	if($email_sent) {
		echo '<br /><div class="title_2" style="width:450px">';
		echo user_correctly_reg .' '. $_POST['user'] .'<br />';
		echo '<img src="'. $conf_images_path .'email.gif" align="absmiddle">&nbsp;'. confirmation_email_sent .' ('. check_spam .')<br />';
	}
	
	echo '<br /><br /><a href="'. $conf_main_page .'">'. return_2_main .'</a></div>';
	exit();

}	//    if($insert_user) {


?>
<script language="javascript">
function reload_captcha() {
	document.new_user_form.action = '<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view='. $_GET['view']; ?>';
	document.new_user_form.submit();
}

</script>
<form action="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view='. $_GET['view'] .'&func=save'; ?>" method="post" name="new_user_form">
  <table align="center" border="0" cellspacing="5" cellpadding="5" style="max-width:640px;">
    <tr>
      <td colspan="3" class="title_1"><?php echo new_user_rocaya; ?></td>
    </tr>
    <?php if($review_errors) { # ------------------------<--------------- test this  ?>
    <tr>
      <td colspan="3" class="default_text"><?php echo review_errors; ?></td>
    </tr>
    <?php } ?>
    <tr>
      <td width="40%" align="right" class="default_text bg_standard"><?php echo select_your_language; ?></td>
      <td width="30%"><?php print_languages_flags(); ?></td>
      <td width="30%">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" class="default_text bg_standard"><?php echo name_nickname; ?></td>
      <td><input name="name" type="text" class="inputlarge" id="name" value="<?= $_SESSION['misc']['reg_user']['name'] ?>" maxlength="60" /></td>
      <td class="default_text error_message"><?php 
	if(!$name_is_not_empty)
		echo name_cant_be_empty;
	else
		echo '&nbsp;';
	?></td>
    </tr>
    <tr>
      <td align="right" class="default_text bg_standard">e-mail</td>
      <td><input name="user" type="text" class="inputlarge" id="user" value="<?= $_SESSION['misc']['reg_user']['user'] ?>" maxlength="60" /></td>
      <td class="default_text error_message"><?php 
	if(!$email_is_valid)
		echo error_email_not_valid;
	else if($user_already_exist) 
		echo error_email_already_exist;
	else
		echo '&nbsp;';
	?></td>
    </tr>
    <tr>
      <td align="right" class="default_text bg_standard"><?php echo ucfirst(password); ?></td>
      <td><input name="pass" type="password" class="inputlarge" id="pass" maxlength="12" /></td>
      <td rowspan="2" class="default_text error_message"><?php
    if(!$password_long_enough)
		echo password_not_long_enough .'. '. try_again;
	else if(!$passwords_match)
		echo passwords_not_match .'. '. try_again;
	else
		echo '&nbsp;';
	?></td>
    </tr>
    <tr>
      <td align="right" class="default_text bg_standard"><?php echo ucfirst(repeat .' '. password); ?></td>
      <td class="default_text"><input name="pass2" type="password" class="inputlarge" id="pass2" maxlength="12" /></td>
    </tr>
    <tr>
      <td align="right" class="default_text bg_standard"><?php echo no_info_from; ?> Rocaya.com</td>
      <td colspan="2" class="default_text"><input type="checkbox" name="no_info" id="no_info" />
        <img src="<?= $conf_images_path; ?>help2.gif" alt="<?php echo no_info_from_help; ?>" width="16" height="16" title="<?php echo no_info_from_help; ?>" /></td>
    </tr>
    <!--<tr>
    <td align="right" bgcolor="#E3E4EE" class="default_text">He leído y aceptado los<a href="x"> términos y condiciones</a> de registro en Rocaya.com</td>
    <td colspan="2" class="default_text"><label>
      <input type="checkbox" name="terms_conditions" id="terms_conditions" />
    </label></td>
    </tr>-->
    <tr>
      <td colspan="2" class="default_text"><?php echo msg_solve_captcha; ?>:</td>
      <td class="default_text">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" class="default_text bg_standard"><?php
include $conf_include_path .'captcha_generator.php';
?></td>
      <td class="default_text"><input name="captcha" type="text" class="inputlarge" id="captcha" maxlength="3" style="width:80px;" />
        <a href="JavaScript:reload_captcha();"><img src="<?= $conf_images_path; ?>reload.png" alt="Reload captcha" title="Actualizar Captcha" width="16" height="16" border="0" align="absmiddle" /></a></td>
      <td class="default_text error_message"><?php
    if(!$captcha_ok)
		echo captcha_error .'. '. try_again;
	else
		echo '&nbsp;';
	?></td>
    </tr>
    <tr>
      <td colspan="3" align="center" class="default_text"><input name="Submit" type="submit" class="bottonlarge" value="       <?php echo ucfirst(register) ?>       " /></td>
    </tr>
    <tr>
      <td colspan="3" class="default_text"><p><a href="index.php"><?php echo return_2_main; ?></a><br />
          <?php echo forgot_password; ?></p></td>
    </tr>
  </table>
</form>
