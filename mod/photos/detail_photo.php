<?php

# $_GET can have the following values:
#	g_o_id / g_o_ty = gallery object_id / gallery object_type
#	g_id = gallery_id

if($_GET['g_id'])
	$where = ' gallery_id = '. $_GET['g_id'];
elseif($_GET['g_o_id'] && $_GET['g_o_ty'])
	$where = ' object_type = \''. $_GET['g_o_ty'] .'\' AND object_id = \''. $_GET['g_o_id'] .'\'';
else
	$where = false;

if($where) {		# get gallery information
	$select_gallery = my_query('SELECT gallery_id, gname FROM galleries WHERE is_public = \'1\' AND '. $where, $conex);
	$arr_gallery = my_fetch_array($select_gallery);
	if($arr_gallery['gallery_id'])
		$str_gallery = '<a href="'. $conf_main_page .'?mod=photos&view=gallery&detail='. $arr_gallery['gallery_id'] .'">'. $arr_gallery['gname'] .'</a> &gt; ';
}

# Extract info about the picture
$sql = 'SELECT p.file_name, p.width, p.height, p.title, p.description, p.author_name, p.date_taken, p.route_id, p.sector_id, p.crag_id, p.owner_id, p.control_code, p.climber_name
FROM photos p
WHERE p.photo_id = '. $_GET['detail'] .'
AND p.flag_inappropriate = \'0\' AND p.flag_deleted = \'0\' AND p.flag_corrupt_unfound_file = \'0\' AND p.flag_is_thumbnail = \'0\' AND p.is_public = \'1\'';

$select_photo = my_query($sql, $conex);
$arr_photo = my_fetch_array($select_photo);

$max_title_length = 100;
$title = $arr_photo['title']?$arr_photo['title']:'[sin título]';
$title = strlen($title) > $max_title_length ? substr($title, 0, $max_title_length) .'...' : $title;

?>

<div class="default_text whereami"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>">FOTOS</a>&nbsp;&gt;&nbsp;
  <?= $str_gallery . $title; ?>
</div>
<div class="standard_container default_text">
    <span class="standard_cont_title">
    <?= $title; ?>
    </span><br />
<?php	
	# The picture here
	echo '<div align="center"><img class="thin_border_picture" border="0" src="'. $conf_images_path . $conf_photos_subpath . $arr_photo['file_name'] .'" width="'. $arr_photo['width'] .'" height="'. $arr_photo['height'] .'" title="'. $arr_photo['title'] .'" /></div>';

?>
    <table width="100%" border="0" cellpadding="2" cellspacing="2">
      <tr>
        <td align="left" valign="top"><?php

	echo 'Autor: '. $arr_photo['author_name'];
	echo $arr_photo['climber_name']?'<br>Escalador: '. $arr_photo['climber_name']:'';
	echo '<br>Fecha: '. date2lan($arr_photo['date_taken'], 'long');
	echo '<br>Descripción: '. $arr_photo['description'] .'<br />';
	
	if($arr_photo['route_id'])
		echo 'Vía / Escuela: '. get_crag_route_names($arr_photo['route_id']) .'<br>';
	elseif($arr_photo['sector_id'])
		echo 'Sector / Escuela: '. get_crag_sector_names($arr_photo['sector_id']) .'<br>';
	elseif($arr_photo['crag_id']) {
		$arr_crag = simple_select('crags', 'crag_id', $arr_photo['crag_id'], array('cname', 'crag_id_url'));
		echo 'Escuela: <a href="'. $conf_main_page .'?mod=routes&view=detail_crag&detail='. $arr_photo['crag_id'] .'&id='. $arr_crag['crag_id_url'] .'">'. $arr_crag['cname'] .'</a><br>';
	}
?></td>
        <td align="right" valign="top"><?php
	$rand_value = rand(1, 1000);
	echo '<div id="rate_container_'. $_GET['detail'] .'-'. $rand_value .'" class="default_text" align="right">'; 
	draw_rate_box($_GET['detail'], $rand_value ); 
	echo '</div>';
	
		# if user_id is owner_id display tools
	echo '<div align="right">';
	if($arr_photo['owner_id'] == $_SESSION['Login']['UserID']) {
		echo '<a href="JavaScript:delete_photo(\''. $_GET['detail'] .'\', \''. $arr_photo['control_code'] .'\');"><img border="0" src="'. $conf_images_path .'delete.gif" width="16" height="16" title="Borrar esta foto"></a>';
		echo '&nbsp;<a href="JavaScript:email_photo(\''. $_GET['detail'] .'\');"><img border="0" src="'. $conf_images_path .'email.gif" width="16" height="16" title="Enviar esta foto"></a>';
	}
	elseif($_SESSION['Login']['UserID'] != $conf_generic_user_id) {	# display other tools
		echo '&nbsp;<a href="JavaScript:email_photo(\''. $_GET['detail'] .'\');"><img border="0" src="'. $conf_images_path .'email.gif" width="16" height="16" title="Enviar esta foto"></a>';
		echo '&nbsp;<a href="'. $conf_main_page .'?mod=home&view=report_inapp&object_id='. $_GET['detail'] .'&object_type=photo&code='. $arr_photo['control_code'] .'"><img border="0" src="'. $conf_images_path .'alert.png" width="16" height="16" title="Reportar contenido inapropiado"></a>';
	}
	echo '</div>';

?></td>
      </tr>
    </table>
<?php 	if($arr_gallery['gallery_id']) {	# ------------------------------------- CARROUSEL  ----------------------------------------			?>
<div id="carrousel" style="width:100%;"><?php

$in = array('photo_id' => $_GET['detail'], 'gallery_id' => $arr_gallery['gallery_id'], 'sort' => $_SESSION['misc']['sort_photos_by']);
include 'inc/photos_carrousel.php';

?></div>
<?php 	
	}	//if($arr_gallery['gallery_id']) {	
	
	# Show the galleries where this photo appears
	$sql = 'SELECT g.description, g.gallery_id FROM photo_gallery pg INNER JOIN galleries g ON g.gallery_id = pg.gallery_id WHERE pg.photo_id = '. $_GET['detail'] .' AND g.is_public = \'1\'';
	$select_galleries = my_query($sql, $conex);
	if(my_num_rows($select_galleries)) {
		echo 'Galerías en las que aparece esta foto:<br>';
		while($record = my_fetch_array($select_galleries)) {
			echo '<a href="'. $conf_main_page .'?mod=photos&view=gallery&detail='. $record['gallery_id'] .'">'. $record['description'] .'</a><br />';
		}
	}
	echo '<br>';
?>

<div class="title_3">Comentarios</div>
<?php

$photo_obj = new photo($_GET['detail']);
$photo_obj->print_photo_comments();

if($_SESSION['Login']['UserID'] != $conf_generic_user_id)
	$photo_obj->print_photo_comment_box();
else
	echo '<a href="'. $conf_main_page .'?mod=home&view=new_user">Regístrate</a> para escribir comentarios<br />';

?>
<script language="javascript" src="inc/ajax.js"></script>
<script language="javascript" src="inc/photos.js"></script>
<?php

if($arr_gallery['gallery_id']) 
	$forward_url = $conf_main_page . '?mod=photos&view=gallery&detail='. $arr_gallery['gallery_id'];
else
	$forward_url = $conf_main_page . '?mod=photos';
?>
<script language="javascript">
function delete_photo(photo_id, control_code) {
	if(confirm('¿Estás seguro que quieres borrar esta foto?')) {
		url = 'inc/ajax.php?content=delete_photo_gallery&photo_id='+ photo_id +'&code='+ control_code;
		getData(url, 'carrousel');
		window.setTimeout(move_2_main, 1500);
	}
}

function move_2_main() {
	document.location = '<?php echo $arr_gallery['gallery_id']? $conf_main_page . '?mod=photos&view=gallery&detail='. $arr_gallery['gallery_id'] : $conf_main_page . '?mod=photos'; ?>';
}
</script>