<?php

$is_user_admin = simple_select('users', 'user_id', $_SESSION['Login']['UserID'], 'isadmin');

if($is_user_admin) {
?>
<div class="default_text whereami"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>">ADMINISTRACIÓN</a>&nbsp;&gt;&nbsp;
<a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=manage_routes'; ?>">Gestión de rutas</a>&nbsp;&gt;&nbsp;Nuevo/Editar sector</div>
<div class="standard_container default_text"> <span class="standard_cont_title">Sector</span><br>
<?php

//$languages = dump_table('languages', 'iso_tag', 'Name', ' WHERE active = \'1\'');

if($_GET['func'] == 'save') {
//print_array($_POST);
	if($_GET['detail']) {	# it's an update
		$sql = 'UPDATE sectors SET sname = \''. $_POST['sname'] .'\', sector_id_url = \''. $_POST['sector_id_url'] .'\' WHERE sector_id = \''. $_GET['detail'] .'\'';

		$update_sector = my_query($sql, $conex);
		
		if($update_sector)
			echo 'Sector actualizdo correctamente<br><a href="index.php?mod=admin&view=manage_routes">&lt; Volver</a><br>';
		else
			echo '<span class="error_message">Error al actualizar el sector</span>';
	}
	else {	# it's a new crag
		$sql = 'INSERT INTO sectors (sector_id, crag_id, sname, sector_id_url) VALUES (\''. $_POST['sector_id'] .'\', \''. $_POST['crags_combo'] .'\', \''. $_POST['sname'] .'\', \''. $_POST['sector_id_url'] .'\')';
		
		$insert_sector = my_query($sql, $conex);
		
		if($insert_sector)
			echo 'Sector Insertado correctamente<br><a href="index.php?mod=admin&view=manage_routes">&lt; Volver</a><br>';
		else
			echo '<span class="error_message">Error al insertar el sector</span>';
		
		$_GET['detail'] = $_POST['sector_id'];
	}
}

if($_GET['detail']) {
	$sql = 'SELECT s.sector_id, s.sname, s.crag_id, s.sector_id_url, c.cname, p.pname, p.prov_id
FROM sectors s 
INNER JOIN crags c ON s.crag_id = c.crag_id 
INNER JOIN provinces p ON c.prov_id = p.prov_id WHERE s.sector_id = \''. $_GET['detail'] .'\'';
	$select_sector_details = my_query($sql, $conex);
	$sector_details = my_fetch_array($select_sector_details);

?>
<a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=edit_sector_routes&detail='. $_GET['detail']; ?>">&gt; EDITAR RUTAS DE ESTE SECTOR</a><br />
<?php
}
?>
<form name="edit_sector_form" action="" method="post">
<table border="0" cellspacing="4" cellpadding="4" class="default_text" align="center">
  <tr bgcolor="#EBEBEB">
    <td align="right">Provincia</td>
    <td><?php
if($_GET['detail']) {
	echo $sector_details['pname'] .' ('. $sector_details['prov_id'] .')';
}
else {	# This is a new sector
	$parameters = array('table' => 'provinces', 'code_field' => 'prov_id', 'desc_field' => 'pname'
					 ,'name' => 'provinces', 'on_change' => 'jump_prov()', 'class' => 'inputnormal'
					 ,'order' => ' prov_id ASC', 'detail' => 1, 'empty' => 1);//, 'selected' => $_SESSION['last_search']['provinces']);
	print_combo_db($parameters);
	
}
	?></td>
  </tr>
  <tr bgcolor="#EBEBEB">
    <td align="right">Escuela</td>
    <td><?php
if($_GET['detail']) {
	echo $sector_details['cname'] .' ('. $sector_details['crag_id'] .')';
}
else {
?>  
	<span id="crags_combo_container">
          <select name="crags_combo" class="inputnormal">
          </select>
          </span>
<?php 
}
?>
</td>
  </tr>
  <tr bgcolor="#EBEBEB">
    <td align="right">Identificador</td>
    <td><input type="text" class="inputlarge" name="sector_id" id="sector_id" value="<?php echo $sector_details['sector_id']; ?>" <?php echo $_GET['detail']?'disabled':''; ?>><?php if(!$_GET['detail']) { ?>&nbsp;<span id="avbl_container"><a href="JavaScript:check_avbl();">Comprobar disponibilidad</a></span><?php } ?></td>
  </tr>
  <tr bgcolor="#EBEBEB">
    <td align="right">Nombre Sector</td>
    <td><input type="text" class="inputlarge" name="sname" id="sname" value="<?php echo htmlentities($sector_details['sname']); ?>" size="250" /></td>
  </tr>
  <tr bgcolor="#EBEBEB">
    <td align="right">sector_id_url</td>
    <td><input type="text" class="inputlarge" name="sector_id_url" id="sector_id_url" value="<?php echo $sector_details['sector_id_url']; ?>" size="250" /></td>
  </tr>
  <tr bgcolor="#EBEBEB">
    <td colspan="2" align="center"><input type="button" class="bottonnormal" onclick="JavaScript:save_sector();" value="  Guardar  " /></td>
    </tr>
</table>
</form>
</div>
<script language="javascript" src="inc/ajax.js"></script>
<script language="javascript">

function jump_prov () {
	url = 'inc/ajax.php?content=crags_combo&detail='+ document.edit_sector_form.provinces.value;
	getData(url, 'crags_combo_container');
}

function jump_crag() {

}

function check_avbl() {
	my_value = document.edit_sector_form.sector_id.value;
	if(my_value) {
		url = 'inc/ajax.php?content=check_id&detail='+ my_value +'&table=sectors&column=sector_id';
		getData(url, 'avbl_container');
	}
}

function save_sector() {
	document.edit_sector_form.action = '<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view='. $_GET['view'] .'&detail='. $_GET['detail'] .'&func=save'; ?>';
	document.edit_sector_form.submit();
}
</script>
<?php
}	// is user admin
?>
