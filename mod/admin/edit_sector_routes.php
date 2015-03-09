<?php

$is_user_admin = simple_select('users', 'user_id', $_SESSION['Login']['UserID'], 'isadmin');

if($is_user_admin) {
?>

<div class="default_text whereami"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>">ADMINISTRACIÓN</a>&nbsp;&gt;&nbsp; <a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=manage_routes'; ?>">Gestión de rutas</a>&nbsp;&gt;&nbsp;Editar rutas del sector</div>
<div class="standard_container default_text">
<span class="standard_cont_title">Añadir Rutas al sector</span><br>
<?php 
	$num_routes_to_insert = 10;

	# Get info about the sector
	$sql = 'SELECT c.crag_id, c.cname, s.sname FROM sectors s INNER JOIN crags c ON s.crag_id = c.crag_id WHERE s.sector_id = \''. $_GET['detail'] .'\'';
	
	$select_sector_details = my_query($sql, $conex);
	$sector_detail = my_fetch_array($select_sector_details);

	if($_GET['func'] == 'save') {
		# convert $_POST into a more managable array
		$routes_data = array();
		for($i = 1; $i <= $num_routes_to_insert; $i++) {
			if($_POST['number'. $i]) {	# number is the only mandatory column for a route?
				$routes_data[$i] = array('rname' => $_POST['rname'. $i]
										,'number' => $_POST['number'. $i]
										,'grade' => $_POST['grade'. $i]
										,'description' => $_POST['description'. $i]
										,'equipment' => $_POST['equipment'. $i]
										,'img_bck' => $_POST['img_bck'. $i]
										,'img_via' => $_POST['img_via'. $i]);
			}
		}
		
		# insert the routes_data array into the database
		$sql = 'INSERT INTO routes (sector_id, crag_id, rname, number, grade, description, equipment, img_bck, img_via) VALUES ';
		$first = true;
		foreach($routes_data as $route) {
			if($first)
				$first = false;
			else
				$sql.=', ';
			$sql.= '(\''. $_GET['detail'] .'\', \''. $sector_detail['crag_id'] .'\', \''. $route['rname'] .'\', \''. $route['number'] .'\', \''. $route['grade'] .'\', \''. 
					$route['description'] .'\', \''. $route['equipment'] .'\', \''. $route['img_bck'] .'\', \''. $route['img_via'] .'\')';
		}
		
		$insert_routes = my_query($sql, $conex);
		
		if($insert_routes)
			echo 'Rutas insertadas correctamente<br><a href="index.php?mod=admin&view=manage_routes">&lt; Volver</a><br>';
		else
			echo '<span class="error_message">Error al insertar las rutas</span>';

	}
	
	echo '<div class="title_3"><a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=new_edit_sector&detail='. $_GET['detail'] .'">'. $_GET['detail'] .' - '. $sector_detail['sname'] .'</a> ('. $sector_detail['crag_id'] .' - '. $sector_detail['cname'] .')</div>';

?>
<form name="sector_routes_form" id="sector_routes_form" action="" method="post">
  <table width="100%" cellpadding="2" cellspacing="2" class="default_text" border="0">
    <tr bgcolor="#EBEBEB">
      <th bgcolor="#FFFFFF">&nbsp;</th>
      <th>Nombre ruta</th>
      <th>Número</th>
      <th>Grado</th>
      <th>Descripción</th>
      <th>Equipamiento</th>
      <th>Imág. fondo</th>
      <th>Imág. vía</th>
    </tr>
    <?php 	for($i = 1; $i <= $num_routes_to_insert; $i++) {	?>
    <tr>
      <td align="right"><?php echo $i; ?></td>
      <td><input type="text" class="inputnormal" name="rname<?php echo $i; ?>" id="rname<?php echo $i; ?>" maxlength="250" /></td>
      <td><input type="text" class="inputnormal" name="number<?php echo $i; ?>" id="number<?php echo $i; ?>" maxlength="12" style="width:60px;" /></td>
      <td><input type="text" class="inputnormal" name="grade<?php echo $i; ?>" id="grade<?php echo $i; ?>" maxlength="25" style="width:90px;" /></td>
      <td><input type="text" class="inputnormal" name="description<?php echo $i; ?>" id="description<?php echo $i; ?>" maxlength="1500" /></td>
      <td><input type="text" class="inputnormal" name="equipment<?php echo $i; ?>" id="equipment<?php echo $i; ?>" maxlength="1500" style="width:90px;" /></td>
      <td><input type="text" class="inputnormal" name="img_bck<?php echo $i; ?>" id="img_bck<?php echo $i; ?>" maxlength="25" /></td>
      <td><input type="text" class="inputnormal" name="img_via<?php echo $i; ?>" id="img_via<?php echo $i; ?>" maxlength="25" /></td>
    </tr>
    <?php } 	//for($i = 1; $i < 10; $i++) {	?>
    <tr>
      <td colspan="8" align="center"><input type="button" class="bottonnormal" onclick="JavaScript:save_routes();" value="  Guardar  " />
  </table>
  </div>
  <div class="standard_container">
  <span class="standard_cont_title">Rutas en el sector</span><br>
  <?php

	echo '<div class="title_3"><a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=new_edit_sector&detail='. $_GET['detail'] .'">'. $_GET['detail'] .' - '. $sector_detail['sname'] .'</a> ('. $sector_detail['crag_id'] .' - '. $sector_detail['cname'] .')</div>';

	$sql = 'SELECT route_id, rname, number, grade, description, equipment, img_bck, img_via FROM routes WHERE sector_id = \''. $_GET['detail'] .'\'';

	$select_routes = my_query($sql, $conex);
	
?>
  <table width="100%" cellpadding="2" cellspacing="2" class="default_text" border="0">
    <tr bgcolor="#EBEBEB">
      <th>Cód.</th>
      <th>Nombre</th>
      <th>Nº</th>
      <th>Grado</th>
      <th>Descripción</th>
      <th>Equip.</th>
      <th>Imág. fondo</th>
      <th>Imág. vía</th>
      <th>&nbsp;</th>
    </tr>
    <?php
	$count=1;
	while($record = my_fetch_array($select_routes)) {	
		if($count%2 == 0)
			$bgcolor = '#EBEBEB';
		else
			$bgcolor = '#FFFFFF';
		$count++;
	?>
    <tr bgcolor="<?php echo $bgcolor; ?>">
      <td align="right"><?php echo $record['route_id']; ?></td>
      <td><input type="text" class="inputdisc" name="orname<?php echo $record['route_id']; ?>" id="orname<?php echo $record['route_id']; ?>" maxlength="250" value="<?php echo $record['rname']; ?>" /></td>
      <td><input type="text" class="inputdisc" name="onumber<?php echo $record['route_id']; ?>" id="onumber<?php echo $record['route_id']; ?>" maxlength="12" style="width:60px;" value="<?php echo $record['number']; ?>" /></td>
      <td><input type="text" class="inputdisc" name="ograde<?php echo $record['route_id']; ?>" id="ograde<?php echo $record['route_id']; ?>" maxlength="25" style="width:90px;" value="<?php echo $record['grade']; ?>" /></td>
      <td><input type="text" class="inputdisc" name="odescription<?php echo $record['route_id']; ?>" id="odescription<?php echo $record['route_id']; ?>" maxlength="1500" value="<?php echo $record['description']; ?>" /></td>
      <td><input type="text" class="inputdisc" name="oequipment<?php echo $record['route_id']; ?>" id="oequipment<?php echo $record['route_id']; ?>" maxlength="1500" style="width:90px;" value="<?php echo $record['equipment']; ?>" /></td>
      <td><input type="text" class="inputdisc" name="oimg_bck<?php echo $record['route_id']; ?>" id="oimg_bck<?php echo $record['route_id']; ?>" maxlength="25" value="<?php echo $record['img_bck']; ?>" /></td>
      <td><input type="text" class="inputdisc" name="oimg_via<?php echo $record['route_id']; ?>" id="oimg_via<?php echo $record['route_id']; ?>" maxlength="25" value="<?php echo $record['img_via']; ?>" /></td>
      <td><a href="JavaScript:save_route(<?php echo $record['route_id']; ?>)"><span id="save_container<?php echo $record['route_id']; ?>"><img align="absmiddle" src="<?php echo $conf_images_path; ?>save.gif" width="16" height="16" border="0" /></span></a><!--&nbsp;<a href="JavaScript:delete_route(<?php echo $record['route_id']; ?>)"><img align="absmiddle" src="<?php echo $conf_images_path; ?>delete.gif" width="16" height="16" border="0" /></a>--></td>
    </tr>
    <?php	}	?>
  </table>
</form>
</div>
<script language="javascript" src="inc/ajax.js"></script>
<script language="javascript">
function save_routes() {	// this is for new routes
	document.sector_routes_form.action = '<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view='. $_GET['view'] .'&detail='. $_GET['detail'] .'&func=save'; ?>';
	document.sector_routes_form.submit();
}

function save_route(r_id) {		// this is for one existing routes
	my_rname = eval('document.sector_routes_form.orname'+ r_id +'.value');
	my_number = eval('document.sector_routes_form.onumber'+ r_id +'.value');
	my_grade = eval('document.sector_routes_form.ograde'+ r_id +'.value');
	my_description = eval('document.sector_routes_form.odescription'+ r_id +'.value');
	my_equipment = eval('document.sector_routes_form.oequipment'+ r_id +'.value');
	my_img_bck = eval('document.sector_routes_form.oimg_bck'+ r_id +'.value');
	my_img_via = eval('document.sector_routes_form.oimg_via'+ r_id +'.value');

/*
	var params = 'route_id='+ r_id +'&rname='+ my_rname +'&number='+ my_number +'&grade='+ my_grade +'&description='+ my_description +'&equipment='+ my_equipment +'&img_bck='+ my_img_bck +'&img_via='+ my_img_via;	
	url = 'inc/ajax.php?content=save_route_admin';
	
	getDataPOST(url, 'save_container'+ r_id, params, 'post_save');
*/

	url = 'inc/ajax.php?content=save_route_admin&route_id='+ r_id +'&rname='+ my_rname +'&number='+ my_number +'&grade='+ my_grade +'&description='+ my_description +'&equipment='+ my_equipment +'&img_bck='+ my_img_bck +'&img_via='+ my_img_via;	
	getData_param(url, 'save_container'+ r_id, 'post_save('+ r_id +')');//, params, 'post_save');
}

function delete_route(r_id) {
	if(confirm('¿Seguro que quieres borrar la ruta?'))
		alert(r_id);
}

function post_save(r_id) {
	var my_container = document.getElementById('save_container'+ r_id);
	my_container.innerHTML = '<img align="absmiddle" src="<?php echo $conf_images_path; ?>save.gif" width="16" height="16" border="0" />';
}
</script>
<?php
}	// is user admin
?>
