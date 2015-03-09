<div class="standard_container"><span class="standard_cont_title">Asistente para recuperar contraseña</span><br>
  <form name="reset_pwd_form" method="post" action="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view='. $_GET['view'] .'&uid='. $_GET['uid'] .'&code='. $_GET['code']; ?>&func=restore">
    <table width="66%" border="0" cellpadding="4" cellspacing="4" class="default_text" align="center">
      <tr>
        <td colspan="3"><?php 

$password_long_enough = true;
$passwords_match = true;

$arr_user = simple_select('users', 'user_id', $_GET['uid'], array('active','uname','email','reg_control','restoring_pwd','restore_pwd_date'));

if($_GET['func'] == 'restore') {
	if($arr_user) {
		if($arr_user['reg_control'] == $_GET['code'] && $arr_user['restore_pwd_date'] == date('Y-m-d') && $arr_user['restoring_pwd'] == '1' && $arr_user['active'] == '1') {
		// Check that passwords match and are long enough
			$password_long_enough = (strlen($_POST['pass'])	>= 5 && strlen($_POST['pass']) <= 12);
			if($password_long_enough) {
				$passwords_match = $_POST['pass'] == $_POST['pass2'];
			}
	
			if($password_long_enough && $passwords_match) {
				$word = digest(substr($arr_user['email'],0,2) . $_POST['pass']);
				$sql = 'UPDATE users SET word = \''. $word .'\', restoring_pwd = \'0\', reg_control = \'NULL\' WHERE user_id = \''. $_GET['uid'] .'\' AND reg_control = \''. $_GET['code'] .'\'';
				$update_pwd = my_query($sql, $conex);
				if($update_pwd) {
?>
          Tu contraseña ha sido actualizada correctamente.<br />
          Por motivos de seguridad no podemos actualizar automáticamente tu contraseña en el foro.<br />
          <a href="http://www.rocaya.com/phpBB3/ucp.php?mode=sendpassword">Debes hacerlo manualmente haciedo click aquí</a><br />
          Recuerda que el nombre de usuario en el foro es tu nombre y no tu dirección de correo electrónico.
          <?php				
					exit();
				}	//if($update_pwd) {
			}	//if($password_long_enough && $passwords_match) {
		}	//if($arr_user['reg_control'] == $_GET['code'] && $arr_user['restore_pwd_date'] == date('Y-m-d') && $arr_user['restoring_pwd'] == '1' && $arr.....
	}	//	if($arr_user) {
}	//if($_GET['func'] == 'restore') {

if($arr_user['reg_control'] == $_GET['code'] && $arr_user['restore_pwd_date'] == date('Y-m-d') && $arr_user['restoring_pwd'] == '1' && $arr_user['active'] == '1') {
?>
          Restaurar contraseña para usuario&nbsp;&nbsp;<strong><?php echo $arr_user['uname']; ?></strong>&nbsp;&nbsp;</td>
      </tr>
      <tr>
        <td align="right" class="default_text bg_standard"><?php echo ucfirst(password); ?></td>
        <td><input name="pass" type="password" class="inputlarge" id="pass" maxlength="12" /></td>
        <td rowspan="2" class="default_text error_message"><?php
	if(!$password_long_enough)
		echo password_not_long_enough .'. '. try_again;
	elseif(!$passwords_match)
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
        <td colspan="3" align="center"><input type="submit" class="inputnewnowidth" value="    Enviar    " /></td>
      </tr>
      <?php
}	//if($arr_user['reg_control'] == $_GET['code'] && $arr_user['restore_pwd_date'] == date('Y-m-d') && $arr_user['restoring_pwd'] == '1' && $arr_user['active'] == '1')
else {
?>
      <tr>
        <td class="error_message">Ha habido un error al intentar restaurar la contraseña.<br />
          Por favor, inténtalo de nuevo: <a href="<?php echo $conf_main_page; ?>?mod=home&view=reset_pwd">Restaurar contraseña</a> </td>
      </tr>
      <?php			
}	//else -- if($arr_user['reg_control'] == $_GET['code'] && $arr_user['restore_pwd_date'] == date('Y-m-d') && $arr_user['restoring_pwd'] == '1' && $......

?>
    </table>
  </form>
  <br />
</div>
