<?php
$desc_column = 'desc_'. substr($_SESSION['misc']['lang'], 0, 2);

# get crag details
$sql = 'SELECT c.cname, c.'. $desc_column .' as description, p.pname
FROM crags c
INNER JOIN provinces p 
ON p.prov_id = c.prov_id
WHERE c.crag_id = \''. $_GET['detail'] .'\'';

$select_crag_details = my_query($sql, $conex);

$crag_details = my_fetch_array($select_crag_details);

?>

<div class="default_text whereami"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>">CROQUIS</a> &gt; <?php echo $crag_details['cname']; ?></div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" width="33%" class="default_text"><div class="standard_container"><span class="standard_cont_title"> <?php echo $crag_details['cname']; ?> </span><br>
        <?php echo $crag_details['description']; ?><br>
        <br>
        <div class="title_3">Sectores en esta escuela:</div>
        <?php

# get sectors and number of routes on each sector for this crag
$sql = 'SELECT s.sname, s.sector_id, s.sector_id_url, count(s.sector_id) as num_routes 
FROM sectors s
INNER JOIN routes r
ON s.sector_id = r.sector_id
WHERE s.crag_id = \''. $_GET['detail'] .'\'
GROUP BY s.sname, s.sector_id, s.sector_id_url
ORDER BY s.sector_id';

$select_sectors = my_query($sql, $conex);
?>
        <ul class="standard_bullet_list">
          <?php  
while($record = my_fetch_array($select_sectors)) {
?>
          <li><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_sector&detail='. $record['sector_id'] .'&id='. $record['sector_id_url']; ?>">
            <?= $record['sname'] ?>
            </a> (<?php echo $record['num_routes']; ?> vías)</li>
          <?php
}  
?>
        </ul>
      </div>
      <!-- comentarios de la escuela aqui --></td>
    <td valign="top" class="default_text"><?php 
	// if user is logged in
if($_SESSION['Login']['UserID'] != $conf_generic_user_id) {
	# get climbs for this user in this crag
	$sql = 'SELECT r.route_id, r.sector_id, r.rname, r.grade, s.sname, s.sector_id, ur.climb_type, ur.climb_date, s.sector_id_url
FROM routes r
INNER JOIN users_routes ur ON ur.route_id = r.route_id
INNER JOIN sectors s ON s.sector_id = r.sector_id
WHERE ur.user_id = '. $_SESSION['Login']['UserID'] .' AND r.crag_id = \''. $_GET['detail'] .'\'
ORDER BY ur.climb_date DESC';

	$select_user_routes = my_query($sql, $conex);
	
	$num_user_routes = my_num_rows($select_user_routes);
?>
      <div class="standard_container"> <span class="standard_cont_title">Estadísticas</span><br>
        <?php
	if($num_user_routes > 0) {
		$arr_climb_types = dump_table('climbs_types', 'climb_type_id', 'desc_es');
?>
        En esta escuela has hecho <?php echo $num_user_routes; ?> vías;<br>
        Tus últimas ascensiones son:
        <ul class="standard_bullet_list">
          <?php
		$counter = 20;
		while($record = my_fetch_array($select_user_routes)) {
			$counter--;
			echo '<li>'. date2lan($record['climb_date'], 'med') .', '. $record['rname'] .' ('. $record['grade'] .'), '. $arr_climb_types[$record['climb_type']] .' &ndash; ';
			echo 'Sector <a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_sector&detail='. $record['sector_id'] .'&id='. $record['sector_id_url'] .'">'. $record['sname'] .'</a></li>';
			if($counter == 0) break;
		}
?>
        </ul>
        <?php
	}	//	if($num_user_routes > 0) {
	else
		echo 'No has hecho ninguna ascensión en esta escuela todavía.';
?>
      </div>
      <?php
	if($_SESSION['Login']['modules']['stats']) {	# The stats module is available
		echo '<br><br><a href="'. $conf_main_page .'?mod=stats&view=user_stats">Ver estadísticas completas</a>';
	}
?>
      <?php 
} //	if($_SESSION['Login']['UserID'] != $conf_generic_user_id) {
?>
      <div class="standard_container"> <span class="standard_cont_title">Comentarios de la escuela</span><br>
<?php
$crag_obj = new crag($_GET['detail']);
$crag_obj->print_crag_comments();

if($_SESSION['Login']['UserID'] != $conf_generic_user_id)
	$crag_obj->print_crag_comment_box();
else
	echo '<a href="'. $conf_main_page .'?mod=home&view=new_user">Regístrate</a> para escribir comentarios<br />';
?>
</div></td>
  </tr>
</table>
