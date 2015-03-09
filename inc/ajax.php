<?php

header("Content-Type: text/html; charset=iso-8859-1");

session_start();

include_once 'config2.php';
include_once 'comm.php';
include_once 'connect.inc';

if(!$_GET['lang'] && !$_SESSION['misc']['lang']) $_GET['lang'] = $conf_default_lang;
if($_GET['lang']) $_SESSION['misc']['lang'] = $_GET['lang'];

$in['file'] = '../translations/'. basename($_SERVER['SCRIPT_NAME'], '.php') .'_'. $_SESSION['misc']['lang'] . '.php';
include '../'. $conf_include_path .'translation.php'; 
unset($in);

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
?>
<?php

# This file is always called when we need some ajax.
# The $_GET parameter content indicates which file will actually return the contents.

switch($_GET['content']) {
	case 'crags_combo':			# Crags selector in search
	   $parameters = array('table' => 'crags', 'code_field' => 'crag_id', 'desc_field' => 'cname'
						   , 'name' => 'crags_combo', 'on_change' => 'jump_crag()', 'class' => 'inputnormal'
						   , 'order' => ' cname', 'empty' => 1
						   , 'extra_condition' => ' prov_id = \''. $_GET['detail'] .'\''
						   , 'substr' => 35, 'selected' => $_SESSION['last_search']['crags_combo']);

		print_combo_db($parameters);
	break;
	case 'crags_combo_photo':
	   $parameters = array('table' => 'crags', 'code_field' => 'crag_id', 'desc_field' => 'cname'
						   , 'name' => 'crags_combo', 'on_change' => 'jump_crag()', 'class' => 'inputlarge'
						   , 'order' => ' cname', 'empty' => 1, 'substr' => 35
						   , 'extra_condition' => ' prov_id = \''. $_GET['detail'] .'\'');

		print_combo_db($parameters);
	break;
	case 'crags_combo_admin':
		$parameters = array('table' => 'crags', 'code_field' => 'crag_id', 'desc_field' => 'cname'
						   , 'name' => 'crags_combo2', 'on_change' => 'jump_crag2()', 'class' => 'inputnormal'
						   , 'order' => ' cname', 'empty' => 1, 'detail' => 1
						   , 'extra_condition' => ' prov_id = \''. $_GET['detail'] .'\''
						   , 'substr' => 35);//, 'selected' => $_SESSION['last_search']['crags_combo']);

		print_combo_db($parameters);
	break;
	case 'sectors_combo':		# Sectors selector in search
	   $parameters = array('table' => 'sectors', 'code_field' => 'sector_id', 'desc_field' => 'sname'
						   , 'name' => 'sectors_combo', 'class' => 'inputnormal', 'order' => ' sname'
						   , 'extra_condition' => ' crag_id = \''. $_GET['detail'] .'\'', 'empty' => 1
						   , 'substr' => 35, 'selected' => $_SESSION['last_search']['sectors_combo']);
						   
		print_combo_db($parameters);
	break;
	case 'sectors_combo_photo':
	   $parameters = array('table' => 'sectors', 'code_field' => 'sector_id', 'desc_field' => 'sname'
						   , 'name' => 'sectors_combo', 'class' => 'inputlarge', 'order' => ' sname'
						   , 'extra_condition' => ' crag_id = \''. $_GET['detail'] .'\'', 'empty' => 1
						   , 'substr' => 35, 'on_change' => 'jump_sector()');
						   
		print_combo_db($parameters);
	break;
	case 'sectors_combo_admin':
	   $parameters = array('table' => 'sectors', 'code_field' => 'sector_id', 'desc_field' => 'sname'
						   , 'name' => 'sectors_combo2', 'class' => 'inputnormal', 'order' => ' sname'
						   , 'extra_condition' => ' crag_id = \''. $_GET['detail'] .'\'', 'empty' => 1
						   , 'substr' => 35, 'detail' => 1);//, 'selected' => $_SESSION['last_search']['sectors_combo']);
						   
		print_combo_db($parameters);
	break;
	case 'routes_combo':
	   $parameters = array('table' => 'routes', 'code_field' => 'route_id', 'desc_field' => 'rname'
						   , 'name' => 'routes_combo', 'class' => 'inputlarge', 'order' => ' rname'
						   , 'extra_condition' => ' sector_id = \''. $_GET['detail'] .'\'', 'empty' => 1
						   , 'substr' => 35, 'detail' => 1);//, 'selected' => $_SESSION['last_search']['sectors_combo']);
						   
		print_combo_db($parameters);
	
	break;
	case 'route_image':			# Display route's image in detail_sector
		echo '<img src="'. $conf_images_path . $conf_images_routes_subpath . $_SESSION['misc']['images'][$_GET['detail']][via] .'" width="'. $_SESSION['misc']['images'][$_GET['detail']]['w'] .'" height="'. $_SESSION['misc']['images'][$_GET['detail']]['h'] .'">';
	break;
	case 'routes_table':		# Display users's routes table in detail_sector
		include 'ajax_draw_routes_table.php';
	break;
/*	case 'edit_route':			# Display route data edition in detail_sector
		include 'ajax_edit_route.php';
	break;*/
	case 'calendar':
		include 'ajax_calendar.php';
	break;
	case 'save_route':
		include 'ajax_save_route.php';
	break;
	case 'save_route_admin':
		$sql = 'UPDATE routes SET rname = \''. $_GET['rname'] .'\', number = \''. $_GET['number'] .'\', grade = \''. $_GET['grade'] .'\'
		,description = \''. $_GET['description'] .'\', equipment = \''. $_GET['equipment'] .'\', img_bck = \''. $_GET['img_bck'] .'\'
		,img_via = \''. $_GET['img_via'] .'\' WHERE route_id = '. $_GET['route_id'];
					
		$update_route = my_query($sql, $conex);
			
		if($update_route)
			print('<img src="'. $conf_images_path .'processing.gif" alt="Saving" border="0" />');
		else
			print('X');	
	break;
	case 'check_id':
		if($_GET['detail'] && $_GET['table'] && $_GET['column']) {
			if(exists_record($_GET['table'], $_GET['column'], $_GET['detail'])) {
				echo '<a href="JavaScript:check_avbl();">Comprobar disponibilidad</a>&nbsp;<span style="color:#FF3333">No disponible</span>';
			}
			else {
				echo '<a href="JavaScript:check_avbl();">Comprobar disponibilidad</a>&nbsp;<span style="color:#339933">Disponible</span>';
			}
		}	
	break;
	case 'rating':	# photo rating
		include_once 'comm_photos.php';
		asign_rate($_GET['photo_id'], $_GET['rate'], $_SESSION['Login']['UserID'], $_GET['div']);
	break;
	case 'photo_detail':
		include_once 'comm_photos.php';
		include 'ajax_show_photo_detail.php';
	break;
	case 'delete_photo_gallery':
		$sql = 'UPDATE photos SET flag_deleted = \'1\', flag_master = \'1\' WHERE photo_id = \''. $_GET['photo_id'] .'\' AND control_code = \''. $_GET['code'] .'\'';
		$delete_photo = my_query($sql, $conex);
		
		if($delete_photo)
			echo '<div class="error_message">La foto ha sido borrada</div>';
		else
			echo '<div class="error_message">Error al borrar la foto</div>';
	break;
	case 'random_photo':
		include_once $conf_oops_subpath .'robjects.php';
		include_once $conf_oops_subpath .'comm_objects.php';
		include_once 'comm_photos.php';

		if($_SESSION['misc']['rotate']['ph_ids']) {
			$ob_photo = new photo($_SESSION['misc']['rotate']['ph_ids'][$_SESSION['misc']['rotate']['last']]);
			$ob_photo->print_small_photo(true);
//			$_SESSION['misc']['rotate']['last']++;
			if($_SESSION['misc']['rotate']['last'] == count($_SESSION['misc']['rotate']['ph_ids']) - 1)
				$_SESSION['misc']['rotate']['last'] = 0;
			else
				$_SESSION['misc']['rotate']['last']++;
		}
	break;
	case 'captcha':
		include 'captcha_generator.php';
	break;
}

?>