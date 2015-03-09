<?php

switch($in['sort']) {
	case 'route': $sort_by = ' p.sector_id DESC'; break;
	case 'rate': $sort_by = ' rating DESC'; break;
	default: $sort_by = ' date_taken DESC';
}

$sql = 'SELECT p.photo_id, p.thumb_file_name, p.thumb_w, p.thumb_h, p.title
FROM photos p
INNER JOIN photo_gallery pg ON p.photo_id = pg.photo_id
WHERE pg.gallery_id = \''. $in['gallery_id'] .'\'
  AND p.flag_inappropriate = \'0\' AND p.flag_deleted = \'0\' AND p.flag_corrupt_unfound_file = \'0\' AND p.flag_is_thumbnail = \'0\' AND p.is_public = \'1\'
ORDER BY '. $sort_by;

$select_photos = my_query($sql, $conex);
$num_photos = my_num_rows($select_photos);

$arr_photos = array();	$current_photo_pos = 0;	$count=1;
while($record = my_fetch_array($select_photos)) {
	$arr_photos[$count] = $record;
	if($record['photo_id'] == $in['photo_id'])
		$current_photo_pos = $count;
	$count++;
}

$start = $current_photo_pos - 4 < 1 ? 1 : $current_photo_pos - 4;
$end = $current_photo_pos + 4 > $num_photos ? $num_photos : $current_photo_pos + 4;


?>
<table align="center" border="0" cellpadding="2" cellspacing="2">
  <tr>
    <?php
foreach($arr_photos as $key => $value) {
//print_array($value, $key);
	if($key >= $start && $key <= $end) {
?>
    <td align="center" valign="middle"><span class="thumbnail"><a href="<?php
		echo $conf_main_page .'?';
		foreach($_GET as $k => $v)
			if($k != 'detail')	echo $k .'='. $v .'&';
	 	echo 'detail='. $value['photo_id'];
		
		if($key == $current_photo_pos)
			$class = 'thick_border_picture';
		else
			$class = 'thin_border_picture';
	?>"><img class="<?= $class; ?>" src="<?= $conf_images_path . $conf_photos_subpath . $value['thumb_file_name']; ?>" width="<?= $value['thumb_w']; ?>" height="<?= $value['thumb_h']; ?>" title="<?= $value['title']; ?>" /></a></span></td>	 
    <?php

	}
}
?>
  </tr>
</table>
