<?php

# Extract info about the picture
$sql = 'SELECT p.small_file_name, p.small_w, p.small_h, p.title, p.description, p.author_name, p.date_taken, p.route_id, p.sector_id, p.crag_id, p.owner_id, p.control_code, p.climber_name
FROM photos p
WHERE p.photo_id = '. $_GET['detail'] .'
AND p.flag_inappropriate = \'0\' AND p.flag_deleted = \'0\' AND p.flag_corrupt_unfound_file = \'0\' AND p.flag_is_thumbnail = \'0\' AND p.is_public = \'1\'';

$select_photo = my_query($sql, $conex);
$arr_photo = my_fetch_array($select_photo);

echo '<div class="title_3">'. $arr_photo['title'] .'</div>';
echo '<a href="'. $conf_main_page .'?mod=photos&view=detail_photo&detail='. $_GET['detail'] .'&g_id='. $_GET['g_id'] .'"><img class="thin_border_picture" border="0" src="'. $conf_images_path . $conf_photos_subpath . $arr_photo['small_file_name'] .'" width="'. $arr_photo['small_w'] .'" height="'. $arr_photo['small_h'] .'" title="'. $arr_photo['title'] .'" /></a><br />';

$rand_value = rand(1, 1000);
echo '<div id="rate_container_'. $_GET['detail'] .'-'. $rand_value .'" class="default_text" align="right">'; 
draw_rate_box($_GET['detail'], $rand_value); 
echo '</div>';

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

# get photo comments summary
echo '<br />Comentarios: ';
write_comments_summary($_GET['detail'], 'photo');

echo '<br>';
# if user_id is owner_id display tools
if($arr_photo['owner_id'] == $_SESSION['Login']['UserID']) {
	echo '<a href="JavaScript:delete_photo_gallery(\''. $_GET['detail'] .'\', \''. $arr_photo['control_code'] .'\');"><img src="'. $conf_images_path .'delete.gif" width="16" height="16" title="Borrar esta foto"></a>';
	echo '&nbsp;<a href="'. $conf_main_page .'?mod=photos&view=detail_photo&detail='. $_GET['detail'] .'"><img src="'. $conf_images_path .'zoom.png" width="16" height="16" border="0" title="Agrandar"></a>';
//	echo '&nbsp;<a href="'. $conf_main_page .'?mod=photos&view=detail_photo&detail='. $_GET['detail'] .'"><img src="'. $conf_images_path .'edit.gif" width="16" height="16" border="0" title="Editar información de la foto"></a>';
	echo '&nbsp;<a href="JavaScript:email_photo(\''. $_GET['detail'] .'\');"><img src="'. $conf_images_path .'email.gif" width="16" height="16" title="Enviar esta foto"></a>';
//		echo '&nbsp;<a href="'. $conf_main_page .'?mod=home&view=report_inapp&object_id='. $_GET['detail'] .'&object_type=photo"><img src="'. $conf_images_path .'alert.png" width="16" height="16" border="0" title="Reportar contenido inapropiado"></a>';
}
else {	# display other tools
//		echo '<a href="JavaScript:delete_photo(\''. $_GET['detail'] .'\');"><img src="'. $conf_images_path .'delete.gif" width="16" height="16" border="0" title="Borrar esta foto"></a>';
	echo '&nbsp;<a href="'. $conf_main_page .'?mod=photos&view=detail_photo&detail='. $_GET['detail'] .'"><img src="'. $conf_images_path .'zoom.png" width="16" height="16" border="0" title="Agrandar"></a>';
//		echo '&nbsp;<a href="'. $conf_main_page .'?mod=photos&view=detail_photo&detail='. $_GET['detail'] .'"><img src="'. $conf_images_path .'edit.gif" width="16" height="16" border="0" title="Editar información de la foto"></a>';
	if($_SESSION['Login']['UserID'] != $conf_generic_user_id) {
		echo '&nbsp;<a href="JavaScript:email_photo(\''. $_GET['detail'] .'\');"><img src="'. $conf_images_path .'email.gif" width="16" height="16" title="Enviar esta foto"></a>';
		echo '&nbsp;<a href="'. $conf_main_page .'?mod=home&view=report_inapp&object_id='. $_GET['detail'] .'&object_type=photo"><img src="'. $conf_images_path .'alert.png" width="16" height="16" title="Reportar contenido inapropiado"></a>';
	}
}


?>