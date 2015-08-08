<?php

header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

session_start();


if($_GET['func'] == 'logout')  session_unset();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="title" content="ROCAYA" />
<meta name="description" content="Web de escalada con croquis gratuitos y diario de escalada donde registrar tus ascensiones, foro de escalada, fotos, estadísticas, noticias, vídeos y reportajes de escalada. Croquis El Pontón de la Oliva, Cañón de Uceda, Peñarrubia, Patones Pueblo, La Pedriza, El Vellón, etc "  />
<meta name="Keywords" content="escalada, climbing, croquis, El Pontón de la Oliva, Cañón de Uceda, Peñarrubia, Patones Pueblo, La Pedriza, El Vellón, ticklist, foro, mosquetón, cuerda, ruta, ascensión, encadenar, peque, a vista, a flash" />
<link rel="icon" type="image/png" href="img/favicon.png" />
<link rel="shortcut icon" href="img/favicon.ico" />
<link href="css/main.css" rel="stylesheet" type="text/css" />
<?php

# Includes
	
	include 'inc/config2.php';
	include $conf_include_path .'comm.php';
	include $conf_include_path . $conf_oops_subpath .'robjects.php';
	include $conf_include_path . $conf_oops_subpath .'comm_objects.php';
	include $conf_include_path . $conf_oops_subpath .'blog.php';
	include $conf_include_path .'connect.inc';
	if($_GET['mod'] == 'photos')
		include $conf_include_path .'comm_photos.php';
	
	if(!$_GET['lang'] && !$_SESSION['misc']['lang']) $_GET['lang'] = $conf_default_lang;
	if($_GET['lang']) $_SESSION['misc']['lang'] = $_GET['lang'];
	
	include $conf_include_path .'translation.php'; 

# Sanitize get and post 
	if((!check_value($_POST)) || (!check_value($_GET))) {
		session_unset();
		?>
<script language="javascript">
		document.location = 'index.php';
	</script>
<?php
		exit();
	}
	
# Get user info
	if(!isset($_SESSION['Login']['UserID']))
		$_SESSION['Login']['UserID'] = $conf_generic_user_id;

# Manage modules
	if(!$_GET['mod']) $_GET['mod'] = $conf_default_mod;
	
	refresh_users_modules(true);
	// get users' accessible modules
/*	if(!isset($_SESSION['Login']['modules'])) {
		$iso_lang = substr($_SESSION['misc']['lang'], 0, 2);

		$sql = 'SELECT m.mod_id, m.mname_'. $iso_lang .', m.desc_'. $iso_lang .', m.icon, um.access, um.modify
		FROM modules m INNER JOIN user_modules um ON um.mod_id = m.mod_id
		WHERE um.user_id = '. $_SESSION['Login']['UserID'] .' AND um.access = 1 AND m.active = 1 ORDER BY mod_order';
		$select_modules = my_query($sql, $conex);
	
		while($record = my_fetch_array($select_modules)) {
			$_SESSION['Login']['modules'][$record['mod_id']] = array('name' => $record['mname_'. $iso_lang]
																	,'desc' => $record['desc_'. $iso_lang]
																	,'icon' => $record['icon']
																	,'access' => $record['access']
																	,'modify' => $record['modify']);
		}
	}
*/
# Get screen resolution
	if(!$_GET['w'] && !$_SESSION['misc']['screen_width']) $_GET['w'] = $conf_default_screen_w;
	if($_GET['w']) $_SESSION['misc']['screen_width'] = $_GET['w'];

?>
<script language="javascript">
/*function submit_login_form() {
	document.login_form.screen_width.value = screen.width;
	// booooring, write checks that e-mail and password are not empty
	document.login_form.submit();
}*/
/*function check_screen_width() {
	if(screen.width <= 640)
		document.location = 'index.php?w=640';
}*/
</script>
<title>::: ROCAYA ::: <?php echo ucfirst($_SESSION['Login']['modules'][$_GET['mod']]['name']); ?></title>
</head>
<body>
<table border="0" align="center" cellpadding="0" cellspacing="0" class="main_body_table">
  <tr>
    <td><table height="120" width="100%" border="0" cellpadding="0" cellspacing="0" class="header_table">
        <tr>
          <td>&nbsp;</td>
          <!-- removed longin form from here! -->
        </tr>
      </table>
      <!--<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td style="background:url(img/menu03left.png); background-repeat:no-repeat; background-position:right; width:24px; height:50px;">&nbsp;</td>
          <?php 
# --------------------  MENU BAR --------------------- #
foreach($_SESSION['Login']['modules'] as $mod_id => $mod_info) {
	$title = $mod_info['desc']?' title="'. $mod_info['desc'] .'"':'';
	$icon = $mod_info['icon']?'<img src="img/'. $mod_info['icon'] .'" border="0" />':'';
	if($mod_id == $_GET['mod']) echo '<td align="center" style="background:url(img/menu03center.png); background-repeat:repeat-x;"><a href="'. $conf_main_page .'?mod='. $mod_id .'" '. $title .'>'. $icon .'<span class="menu_orange menu_active">' .substr($mod_info['name'], 0, 1) .'</span><span class="menu_active">'. substr($mod_info['name'], 1) .'</a></span></td>';
	else 						echo '<td align="center" style="background:url(img/menu03center.png); background-repeat:repeat-x;"><a href="'. $conf_main_page .'?mod='. $mod_id .'" '. $title .'>'. $icon . '<span class="menu_orange">' .substr($mod_info['name'], 0, 1) .'</span><span class="menu_link">'. substr($mod_info['name'], 1) .'</a></span></td>';
}
?>
          <td style="background:url(img/menu03right.png); background-repeat:no-repeat; background-position:left; width:22px; height:50px;">&nbsp;</td>
        </tr>
      </table>--></td>
  </tr>
  <tr>
    <td><?php 
# -------------------- INCLUDE THE MODULE view --------------------- #	
if(!$_GET['view']) $_GET['view'] = 'mod_main';
$include_file = 'mod/'. $_GET['mod'] .'/'. $_GET['view'] .'.php';

include $include_file;

	?></td>
  </tr>
  <!-- ------------------------ FOOTER ----------------------- -->
  <tr>
    <td align="center" class="small_text">
    <a href="<?= $conf_main_page; ?>?mod=home&view=tycs">T&eacute;rminos y condiciones</a> | <a href="<?= $conf_main_page; ?>?mod=home&view=contact">Contacto</a> | <a href="<?= $conf_main_page; ?>?mod=home&view=about">Quienes somos</a><br /><br /></td>
  </tr>
</table>
</body>
</html>
