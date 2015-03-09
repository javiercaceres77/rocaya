<div class="standard_container"><span class="standard_cont_title">Asistente para recuperar contraseña</span><br>
  <?php 

$captcha_ok = true;

if($_GET['func'] == 'restore' && $_POST['user']) {
	$captcha_ok = $_POST['captcha'] == $_SESSION['misc']['captcha'];

	if($captcha_ok) {
		# Check that the e-mail exists in the DB
		$arr_user_id = simple_select('users', 'email', $_POST['user'], 'user_id', ' AND active = \'1\'');
		if($arr_user_id['user_id']) {
			$check_code = md5(rand());
	
			$sql = 'UPDATE users SET restoring_pwd = \'1\', restore_pwd_date = \''. date('Y-m-d') .'\', reg_control = \''. $check_code .'\' WHERE user_id = \''. $arr_user_id['user_id'] .'\'';
			
			$update_user = my_query($sql, $conex);
			if($update_user) {
				$to = $_POST['user'];
				// subject
				$subject = 'Restaurar contraseña en ROCAYA.COM';
				// message
				$message = '
			<html>
			<body>
			  <p>'. ucfirst(hello) .'</p>
			  <p>Hemos recibido una solicitud para restaurar tu contraseña en ROCAYA.COM.<br />
				 '. mail_conf_line2 .'</p>
			  <p><a href="http://www.rocaya.com/'. $conf_main_page .'?mod=home&view=reset_pwd_post&uid='. $arr_user_id['user_id'] .'&code='. $check_code .'">http://www.rocaya.com/'. $conf_main_page .'?mod=home&view=reset_pwd_post&uid='. $arr_user_id['user_id'] .'&code='. $check_code .'</a></p>
			  <p>'. mail_conf_line3 .'</p>
			</body>
			</html>
			';
				// To send HTML mail, the Content-type header must be set
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				// Additional headers
				//$headers .= 'To: '. $_POST['user'] .'<'. $_POST['user'] .'>' . "\r\n";
				$headers .= 'From: No Reply <no_replay@rocaya.com>' . "\r\n";
				//$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
				//$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";
				
				// Mail it
				$email_sent = mail($to, $subject, $message, $headers);
	
?>
  <table width="66%" border="0" cellpadding="4" cellspacing="4" class="default_text" align="center">
    <tr>
      <td><?php
				if($email_sent) {
?>
        <img src="<?php echo $conf_images_path; ?>email.gif" align="absmiddle"> Hemos enviado un correo a la dirección <?php echo $_POST['user']; ?><br />
        Comprueba tu buzón de entrada y la bandeja de spam y sigue las instrucciones del mesaje<br />
        Gracias
        <?php
					exit();
				}	//		if($email_sent) {
				else
					echo 'Error al enviar el mensaje para restaurar la contraseña, por favor, revisa la dirección: '. $_POST['user'];
				
				echo '<br /><br /><a href="'. $conf_main_page .'">'. return_2_main .'</a></div>';
			
			}	//		if($update_user) {
			else {
				echo '<div class="error_message">Error en la base de datos</div>';
			}
?></td>
    </tr>
  </table>
  <?php	
			}	//	if($arr_user_id['user_id']) {
			else {
?>
  <div class="default_text">El usuario con correo "<?php echo $_POST['user']; ?>" no está registrado en Rocaya <br />
    <br />
    <a href="<?php echo $conf_main_page; ?>"><?php echo return_2_main; ?></a></div>
  <?php
		}	// else -- 	if($arr_user_id['user_id']) {
	}	//if($captcha_ok)
}	//if($_GET['func'] == 'restore' && $_POST['user']) {

if(!($_GET['func'] == 'restore' && $_POST['user']) || !$captcha_ok) {
?>
  <form name="reset_pwd_form" method="post" action="<?php echo $conf_main_page; ?>?view=reset_pwd&func=restore">
    <table width="66%" border="0" cellpadding="4" cellspacing="4" class="default_text" align="center">
      <tr>
        <td>Escribe tu dirección de correo electrónico y te enviaremos instrucciones para restaurar tu contraseña</td>
      </tr>
      <tr>
        <td class="bg_standard">e-mail&nbsp;&nbsp;
          <input name="user" type="text" class="inputlarge" id="user" maxlength="60" value="<?php echo $_POST['user']; ?>" />
        </td>
      </tr>
      <tr>
        <td class="default_text"><?php echo msg_solve_captcha; ?>:</td>
      </tr>
      <tr>
        <td class="bg_standard"><?php
include $conf_include_path .'captcha_generator.php';
?>&nbsp;&nbsp;<input name="captcha" type="text" class="inputlarge" id="captcha" maxlength="3" style="width:80px;" />
          <a href="JavaScript:reload_captcha();"><img src="<?= $conf_images_path; ?>reload.png" alt="Reload captcha" title="Actualizar Captcha" width="16" height="16" border="0" align="absmiddle" /></a> <span class="default_text error_message">
          <?php
    if(!$captcha_ok)
		echo captcha_error .'; '. try_again;
	else
		echo '&nbsp;';
	?>
          </span> </td>
      </tr>
      <tr>
        <td align="center"><input type="submit" class="inputnewnowidth" value="    Enviar    " /></td>
      </tr>
    </table>
  </form>
<?php 
} // if(!($_GET['func'] == 'restore' && $_POST['user']) || !$captcha_ok) {
?>
</div>
<script language="javascript">
function reload_captcha() {
	document.reset_pwd_form.action = '<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view='. $_GET['view']; ?>';
	document.reset_pwd_form.submit();
}

</script>