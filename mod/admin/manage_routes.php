<?php

$is_user_admin = simple_select('users', 'user_id', $_SESSION['Login']['UserID'], 'isadmin');

if($is_user_admin) {
?>
<div class="default_text whereami"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>">ADMINISTRACIÓN</a>&nbsp;&gt;&nbsp;Gestión de rutas</div>
<div class="standard_container"> <span class="standard_cont_title">Escuelas</span><br>
  <form name="crag_form" action="" method="post">
    <table border="0" cellpadding="4" cellspacing="4" class="default_text">
      <tr>
        <td bgcolor="#EBEBEB">Insertar escuela nueva</td>
        <td bgcolor="#EBEBEB"><a href="JavaScript:new_crag();"><img src="<?php echo $conf_images_path; ?>go.png" alt="go" width="24" height="24" border="0"></a></td>
      </tr>
      <tr>
        <td bgcolor="#EBEBEB">Editar escuela&nbsp;&nbsp;
          <?php
$parameters = array('table' => 'provinces', 'code_field' => 'prov_id', 'desc_field' => 'pname'
				 ,'name' => 'provinces', 'on_change' => 'jump_prov()', 'class' => 'inputnormal'
				 ,'order' => ' prov_id ASC', 'detail' => 1, 'empty' => '1');//, 'selected' => $_SESSION['last_search']['provinces']);
print_combo_db($parameters);

	?>
          <span id="crags_combo_container">
          <select name="crags_combo" class="inputnormal">
          </select>
          </span> </td>
        <td bgcolor="#EBEBEB"><a href="JavaScript:edit_crag()"><img src="<?php echo $conf_images_path; ?>go.png" alt="go" width="24" height="24" border="0"></a></td>
      </tr>
    </table>
  </form>
</div>
<div class="standard_container"> <span class="standard_cont_title">Sectores</span><br>
  <form name="sector_form" action="" method="post">
    <table border="0" cellpadding="4" cellspacing="4" class="default_text">
      <tr>
        <td bgcolor="#EBEBEB">Insertar sector nuevo</td>
        <td bgcolor="#EBEBEB"><a href="JavaScript:new_sector();"><img src="<?php echo $conf_images_path; ?>go.png" alt="go" width="24" height="24" border="0"></a></td>
      </tr>
      <tr>
        <td bgcolor="#EBEBEB">Editar sector&nbsp;&nbsp;
          <?php
$parameters = array('table' => 'provinces', 'code_field' => 'prov_id', 'desc_field' => 'pname'
				 ,'name' => 'sprovinces', 'on_change' => 'jump_prov2()', 'class' => 'inputnormal'
				 ,'order' => ' prov_id ASC', 'detail' => 1, 'empty' => '1');//, 'selected' => $_SESSION['last_search']['provinces']);
print_combo_db($parameters);

	?>
          <span id="crags_combo_container2">
          <select name="crags_combo2" class="inputnormal">
          </select>
          </span> <span id="sectors_combo_container">
          <select name="sectors_combo2" class="inputnormal">
          </select>
          </span> </td>
        <td bgcolor="#EBEBEB"><a href="JavaScript:edit_sector()"><img src="<?php echo $conf_images_path; ?>go.png" alt="go" width="24" height="24" border="0"></a></td>
      </tr>
    </table>
  </form>
</div>
<div class="standard_container"> <span class="standard_cont_title">Rutas sin sector</span><br>
<?php

$sql = 'SELECT * FROM routes WHERE sector_id = \'\' OR sector_id IS NULL';

$select_routes = my_query($sql, $conex);

?><table width="100%" cellpadding="2" cellspacing="2" class="default_text" border="0">
<tr bgcolor="#EBEBEB"><th>Vía</th><th>Nombre</th><th>nº</th><th>grado</th><th>old_sector_id</th><th>old_crag_id</th><th>old_prov_id</th><th>edit</th></tr>
<?php
	while($record = my_fetch_array($select_routes)) {
		echo '<tr bgcolor="#EBEBEB"><td>'. $record['route_id'] .'</td><td>'. htmlentities($record['rname']) .'</td><td>'. $record['number'] .'</td><td>'. $record['grade'] .'</td><td>'. $record['old_sector_id'] .'</td><td>'. $record['old_crag_id'] .'</td><td>'. $record['old_prov_id'] .'</td><td><a href="JavaScript:edit_route(\''. $record['route_id'] .'\')">edit</a></tr>';
	}
?>
</table><br />
<br />
</div>
<script language="javascript" src="inc/ajax.js"></script>
<script language="javascript">

function jump_prov () {
	url = 'inc/ajax.php?content=crags_combo&detail='+ document.crag_form.provinces.value;
	getData(url, 'crags_combo_container');
}

function jump_crag () {
/*	url = 'inc/ajax.php?content=sectors_combo&detail='+ document.sector_form.crags_combo.value;
	getData(url, 'sectors_combo_container');*/
}

function jump_prov2 () {
	url = 'inc/ajax.php?content=crags_combo_admin&detail='+ document.sector_form.sprovinces.value;
	getData(url, 'crags_combo_container2');
}

function jump_crag2() {
	url = 'inc/ajax.php?content=sectors_combo_admin&detail='+ document.sector_form.crags_combo2.value;
	getData(url, 'sectors_combo_container');
}

function edit_crag() {
	document.location = '<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=new_edit_crag&detail='; ?>'+ document.crag_form.crags_combo.value;
}

function new_crag() {
	document.location = '<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=new_edit_crag'; ?>';
}

function edit_sector() {
	document.location = '<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=new_edit_sector&detail='; ?>'+ document.sector_form.sectors_combo2.value;
}

function new_sector() {
	document.location = '<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=new_edit_sector'; ?>';
}

function edit_route(rid) {
	document.location = '<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=new_edit_route&detail='; ?>' + rid;
}
</script>
<?php
}	// is user admin
?>
