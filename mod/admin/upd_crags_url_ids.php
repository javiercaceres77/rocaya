<?php

$is_user_admin = simple_select('users', 'user_id', $_SESSION['Login']['UserID'], 'isadmin');

if($is_user_admin['isadmin']) {
	$arr_sectors = dump_table('crags', 'crag_id', 'cname', ' WHERE crag_id_url = \'\'');
?>

<div class="standard_container default_text"> <span class="standard_cont_title">Módulo de administración</span><br />
  <a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>">&lt; Vovler</a><br />
  Actualizando la tabla de Escuelas con url_ids:
  <table border="0" cellspacing="3" cellpadding="3"
    <tr>
      <th bgcolor="#CCCCCC">Crag_id</th>
      <th bgcolor="#CCCCCC">Crag name</th>
      <th bgcolor="#CCCCCC">crag_id_url</th>
      <th></th>
    </tr>
    <?php

	$search = array('%27', '%2C', '%29', '%28', '%F1','%E1','%FA','%E9','%ED','%F3','+');
	$replace= array('',    '',    '',    '',    'n',  'a',  'u',  'e',  'i',  'o',  '_');
	
	foreach($arr_sectors as $key => $value) {
		$str_clean = str_replace($search, $replace, urlencode(strtolower($value)));
		$sql = 'UPDATE crags SET crag_id_url = \''. $str_clean .'\' WHERE crag_id = \''. $key .'\'';
		echo '<tr><td bgcolor="#CCCCCC">'. $key .'</td><td bgcolor="#CCCCCC">'. htmlentities($value) .'</td><td bgcolor="#CCCCCC">'. $str_clean .'</td>';
		$upd_sector = my_query($sql, $conex);
		if($upd_sector)
			echo '<td bgcolor="#33FF66">Actualizada OK</td>';
		else
			echo '<td bgcolor="#CC3333">Error</td>';
		echo '</tr>';		
	}
?>
  </table>
</div>
<?php
}
?>
