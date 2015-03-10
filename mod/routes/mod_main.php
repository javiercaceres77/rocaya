<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><div class="standard_container default_text"> <span class="standard_cont_title">Escuelas con croquis</span><br />
        <?php

# Select routes in DB that have sketches
$sql = 'SELECT c.crag_id, c.crag_id_url, c.cname, p.pname, count(*) AS num_routes
FROM crags c
INNER JOIN routes r ON c.crag_id = r.crag_id
INNER JOIN provinces p ON p.prov_id = c.prov_id
WHERE r.img_bck IS NOT NULL AND r.img_via IS NOT NULL AND r.img_bck <> \'\' AND r.img_via <> \'\'
GROUP BY c.crag_id, c.crag_id_url, c.cname, p.pname
ORDER BY p.pname, c.cname';

$select_routes = my_query($sql, $conex);

$curr_province = '';
while($record = my_fetch_array($select_routes)) {
	if($record['pname'] != $curr_province) {
		$curr_province = $record['pname'];
		echo '<br /><div style="border-bottom: 1px solid #333333; width:100%" class="title_3">'. $record['pname'] .'</div>';
	}
	echo '<div style="margin-left:15px"><a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_crag&detail='. $record['crag_id'] .'&id='. $record['crag_id_url'] .'">'. $record['cname'] .'</a> ('. $record['num_routes'] .' v&iacute;as)</div>';
}

?>
</div>
<div class="standard_container default_text"> <span class="standard_cont_title">Otras Escuelas</span><br />
        <?php
$sql = 'SELECT c.crag_id, c.crag_id_url, c.cname, p.prov_id, p.pname, r.crag_id as rcrag_id, count(*) as num_rutas
FROM crags c
LEFT JOIN routes r ON c.crag_id = r.crag_id
INNER JOIN provinces p ON p.prov_id = c.prov_id
WHERE r.img_bck IS NULL OR r.img_via IS NULL OR r.img_bck = \'\' OR r.img_via = \'\'
GROUP BY c.crag_id, c.crag_id_url, c.cname, p.pname, r.crag_id
ORDER BY p.pname, c.cname';

$select_routes = my_query($sql, $conex);

$curr_province = '';
$first = true;
while($record = my_fetch_array($select_routes)) {
	if($record['pname'] != $curr_province) {
		if($first)
			$first = false;
		else
			echo '</div>';

		$curr_province = $record['pname'];
		echo '<div style="border-bottom: 1px solid #333333; width:100%" class="title_3"><span id="plus_minus_'. $record['prov_id'] .'"><a href="JavaScript:expand_prov(\''. $record['prov_id'] .'\')"><img src="'. $conf_images_path .'plus.gif" border="0" alt="expandir provincia" width="16" height="16"></a></span>&nbsp;'. $record['pname'] .'</div>';
		echo '<div style="margin-left:15px;margin-bottom:10px;display:none;" id="prov_container_'. $record['prov_id'] .'">';
	}
//	$color_style = $record['rcrag_id']?'; color:#A92D29':'';
	$num_routes = $record['rcrag_id']?' ('. $record['num_rutas'] .' vías)':'';
	$link1 = $record['rcrag_id']?'<a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_crag&detail='. $record['rcrag_id'] .'&id='. $record['crag_id_url'] .'">':'';
	$link2 = $record['rcrag_id']?'</a>':'';
		
	echo $link1 . $record['cname'] . $num_routes . $link2 .'<br>';
	
}
?>
      </div></td>
    <?php 
if($_SESSION['Login']['UserID'] != $conf_generic_user_id) {
?>
    <td valign="top" width="33%"><div class="standard_container default_text"> <span class="standard_cont_title"> <?php echo last_climb; ?></span><br />
        <?php 

	$desc_column = 'desc_'. substr($_SESSION['misc']['lang'], 0, 2);
	
	# Select last climb data for user.
	$sql = 'SELECT s.sname, s.sector_id, s.sector_id_url, c.crag_id, c.crag_id_url, c.cname, r.rname, r.grade, ur.climb_date, ct.'. $desc_column .' AS description
	FROM users_routes ur
	INNER JOIN routes r ON r.route_id = ur.route_id
	INNER JOIN crags c ON c.crag_id = r.crag_id
	INNER JOIN sectors s ON r.sector_id = s.sector_id
	LEFT JOIN climbs_types ct ON ur.climb_type = ct.climb_type_id
	WHERE ur.user_id = '. $_SESSION['Login']['UserID'] .'
	AND ur.climb_date = (
	SELECT max( climb_date )
	FROM users_routes WHERE user_id = '. $_SESSION['Login']['UserID'] .')';
	
	$select_last_climb = my_query($sql, $conex);
	
	$last_climb = my_fetch_array($select_last_climb);

?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>Fecha</td>
            <td><?php echo date2lan($last_climb['climb_date'], 'long'); ?></td>
          </tr>
          <tr>
            <td>V&iacute;a</td>
            <td><?php echo $last_climb['rname'] .' ('. $last_climb['grade'] .')'; ?></td>
          </tr>
          <tr>
            <td>Tipo</td>
            <td><?php echo $last_climb['description']; ?></td>
          </tr>
          <tr>
            <td>Sector</td>
            <td><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_sector&detail='. $last_climb['sector_id'] .'&id='. $last_climb['sector_id_url']; ?>"><?php echo $last_climb['sname']; ?></a></td>
          </tr>
          <tr>
            <td>Escuela</td>
            <td><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_crag&detail='. $last_climb['crag_id'] .'&id='. $last_climb['crag_id_url']; ?>"><?php echo $last_climb['cname']; ?></a></td>
          </tr>
        </table>
        <br />
      </div>
      <!--<div class="standard_container default_text"> <span class="standard_cont_title"> <?php echo ucfirst(search); ?>?</span><br />
        Insertar aquí un buscador?</div>-->
    </td>
    <?php
}		//if($_SESSION['Login']['UserID'] != $conf_generic_user_id) {
?>
  </tr>
</table>
<script language="javascript">
function expand_prov(my_element) {
	document.getElementById('plus_minus_'+ my_element).innerHTML = '<a href="JavaScript:collapse_prov(\''+ my_element +'\')"><img src="<?php echo $conf_images_path; ?>minus.gif" border="0" width="16" height="16"></a>';
	document.getElementById('prov_container_'+ my_element).style.display = 'block';
}

function collapse_prov(my_element) {
	document.getElementById('plus_minus_'+ my_element).innerHTML = '<a href="JavaScript:expand_prov(\''+ my_element +'\')"><img src="<?php echo $conf_images_path; ?>plus.gif" border="0" width="16" height="16"></a>';
	document.getElementById('prov_container_'+ my_element).style.display = 'none';
}
</script>
