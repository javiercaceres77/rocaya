<?php

$is_user_admin = simple_select('users', 'user_id', $_SESSION['Login']['UserID'], 'isadmin');

if($is_user_admin) {
?>
<div class="default_text whereami"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>">ADMINISTRACIÓN</a>&nbsp;&gt;&nbsp;
<a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=manage_routes'; ?>">Gestión de rutas</a>&nbsp;&gt;&nbsp;Nueva/Editar escuela</div>
<div class="standard_container default_text"> <span class="standard_cont_title">Escuelas</span><br>
<?php

$languages = dump_table('languages', 'iso_tag', 'Name', ' WHERE active = \'1\'');

if($_GET['func'] == 'save') {
	if($_GET['detail']) {	# it's an update
		$desc_cols = '';
		foreach($languages as $iso_tag => $name) { 
			$desc_cols.= ', desc_'. $iso_tag .' = \''. $_POST['desc_'. $iso_tag] .'\'';
		}			

		$sql = 'UPDATE crags SET cname = \''. $_POST['cname'] .'\''. $desc_cols .', coordinates= \''. $_POST['coordinates'] .'\', access= \''. $_POST['access'] .'\', crag_id_url= \''. $_POST['crag_id_url'] .'\'
		WHERE crag_id = \''. $_GET['detail'] .'\'';

		$update_crag = my_query($sql, $conex);
		
		if($update_crag)
			echo 'Escuela actualizda correctamente<br><a href="index.php?mod=admin&view=manage_routes">&lt; Volver</a><br>';
		else
			echo '<span class="error_message">Error al actualizar la escuela</span>';
	}
	else {	# it's a new crag
		$desc_cols = '';
		$desc_cols_values = '';
		foreach($languages as $iso_tag => $name) {
			$desc_cols.= ', desc_'. $iso_tag;
			$desc_cols_values.= ', \''. $_POST['desc_'. $iso_tag] .'\'';
		}
		$sql = 'INSERT INTO crags (crag_id, prov_id, cname'. $desc_cols .', coordinates, access, crag_id_url) VALUES 
		(\''. $_POST['crag_id'] .'\', \''. $_POST['provinces'] .'\', \''. $_POST['cname'] .'\''. $desc_cols_values .', \''. $_POST['coordinates'] .'\', \''. $_POST['access'] .'\', \''. $_POST['crag_id_url'] .'\')';
		
		$insert_crag = my_query($sql, $conex);
		
		if($insert_crag)
			echo 'Escuela Insertada correctamente<br><a href="index.php?mod=admin&view=manage_routes">&lt; Volver</a><br>';
		else
			echo '<span class="error_message">Error al insertar la escuela</span>';
		
		$_GET['detail'] = $_POST['crag_id'];
	}
	
//	print_array($_POST);
}

if($_GET['detail']) {
	$sql = 'SELECT * FROM crags where crag_id = \''. $_GET['detail'] .'\'';
	$select_crag_details = my_query($sql, $conex);
	$crag_details = my_fetch_array($select_crag_details);

}
?><form name="edit_crag_form" action="" method="post">
<table border="0" cellspacing="4" cellpadding="4" class="default_text" align="center">
  <tr bgcolor="#EBEBEB">
    <td align="right">Provincia</td>
    <td><?php
$parameters = array('table' => 'provinces', 'code_field' => 'prov_id', 'desc_field' => 'pname'
				 ,'name' => 'provinces', 'class' => 'inputlarge', 'order' => ' pname ASC', 'detail' => 1);
if($_GET['detail']) {
	$parameters['disabled'] = 1;
	$parameters['selected'] = $crag_details['prov_id'];
}
print_combo_db($parameters);
	?></td>
  </tr>
  <tr bgcolor="#EBEBEB">
    <td align="right">Identificador</td>
    <td><input type="text" class="inputlarge" name="crag_id" id="crag_id" value="<?php echo $crag_details['crag_id']; ?>" <?php echo $_GET['detail']?'disabled':''; ?>><?php if(!$_GET['detail']) { ?>&nbsp;<span id="avbl_container"><a href="JavaScript:check_avbl();">Comprobar disponibilidad</a></span><?php } ?></td>
  </tr>
  <tr bgcolor="#EBEBEB">
    <td align="right">Nombre Escuela</td>
    <td><input type="text" class="inputlarge" name="cname" id="cname" value="<?php echo htmlentities($crag_details['cname']); ?>" size="250" /></td>
  </tr>
<?php 
foreach($languages as $iso_tag => $name) { 
	$desc_col = 'desc_'. $iso_tag;
?>
  <tr bgcolor="#EBEBEB">
    <td align="right">Descripción <?php echo htmlentities($name); ?></td>
    <td><textarea class="inputnormal" name="<?php echo $desc_col; ?>" size="1000" rows="5" /><?php echo htmlentities($crag_details[$desc_col]); ?></textarea></td>
  </tr>
<?php } ?>
  <tr bgcolor="#EBEBEB">
    <td align="right">Coordenadas</td>
    <td><input type="text" class="inputlarge" name="coordinates" id="coordinates" value="<?php echo $crag_details['coordinates']; ?>" size="75" /></td>
  </tr>
  <tr bgcolor="#EBEBEB">
    <td align="right">Acceso</td>
    <td><input type="text" class="inputlarge" name="access" id="access" value="<?php echo $crag_details['access']; ?>" size="1000" /></td>
  </tr>
  <tr bgcolor="#EBEBEB">
    <td align="right">crag_id_url</td>
    <td><input type="text" class="inputlarge" name="crag_id_url" id="crag_id_url" value="<?php echo $crag_details['crag_id_url']; ?>" size="250" /></td>
  </tr>
  <tr bgcolor="#EBEBEB">
    <td colspan="2" align="center"><input type="button" class="bottonnormal" onclick="JavaScript:save_crag();" value="  Guardar  " /></td>
    </tr>
</table>
</form>
</div>
<script language="javascript" src="inc/ajax.js"></script>
<script language="javascript">

function check_avbl() {
	my_value = document.edit_crag_form.crag_id.value;
	if(my_value) {
		url = 'inc/ajax.php?content=check_id&detail='+ my_value +'&table=crags&column=crag_id';
		getData(url, 'avbl_container');
	}
}

function save_crag() {
	document.edit_crag_form.action = '<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view='. $_GET['view'] .'&detail='. $_GET['detail'] .'&func=save'; ?>';
	document.edit_crag_form.submit();
}
</script>
<?php
}	// is user admin
?>
