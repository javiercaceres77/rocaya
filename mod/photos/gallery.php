<?php

$get_out = false;
if($_GET['detail']) 	# detail is the gallery_id
	$where = ' WHERE gallery_id = \''. $_GET['detail'] .'\'';
elseif($_GET['object_id'] && $_GET['object_type'])
	$where = ' WHERE object_type = \''. $_GET['object_type'] .'\' AND object_id = \''. $_GET['object_id'] .'\'';
else
	$get_out = true;

if($get_out) {
	echo  '<script language="javascript"> document.location = "'. $conf_main_page .'?mod='. $_GET['mod'] .'"; </script>'; exit();
}

# get gallery details
$sql = 'SELECT * FROM galleries'. $where;
$select_details = my_query($sql, $conex);

$gallery_details = my_fetch_array($select_details);

# check if user's gallery is public and user is not logged in
if($gallery_details['object_type'] == 'user' && !$get_out)
	$get_out = $gallery_details['is_public'] != '1';

if($get_out) {
	echo  '<script language="javascript"> document.location = "'. $conf_main_page .'?mod='. $_GET['mod'] .'"; </script>'; exit();
}

# get object details
switch($gallery_details['object_type']) {
	case 'user':
		$object_details = simple_select('users', 'user_id', $gallery_details['object_id'], 'uname');
	break;
	case 'crag':
		$object_details = simple_select('crags', 'crag_id', $gallery_details['object_id'], 'cname');
	break;
	case 'report':
		$object_details = simple_select('reports', 'report_id', $gallery_details['object_id'], 'title');
	break;
}

?>
<div class="default_text whereami"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>">FOTOS</a>&nbsp;&gt;&nbsp;<?= $gallery_details['gname']; ?></div>
<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top" width="66%"><div class="standard_container default_text">
      <span class="standard_cont_title">
      <?= $gallery_details['gname']; ?>
      </span><br />
      <?php
	  
if(!isset($_GET['sort']))
	$_GET['sort'] = $_SESSION['misc']['sort_photos_by'];
else 
	$_SESSION['misc']['sort_photos_by'] = $_GET['sort'];
	
# get object's photos
switch($_GET['sort']) {
	case 'route': $sort_by = ' p.sector_id DESC'; break;
	case 'rate': $sort_by = ' p.rating DESC'; break;
	default: $sort_by = ' p.date_taken DESC';
}



$sql = 'SELECT p.photo_id, p.thumb_file_name, p.thumb_w, p.thumb_h, p.title, p.date_taken, p.rating, p.crag_id, p.sector_id
FROM photos p
INNER JOIN photo_gallery pg ON p.photo_id = pg.photo_id
WHERE pg.gallery_id = \''. $gallery_details['gallery_id'] .'\'
  AND p.flag_inappropriate = \'0\' AND p.flag_deleted = \'0\' AND p.flag_corrupt_unfound_file = \'0\' AND p.flag_is_thumbnail = \'0\' AND p.is_public = \'1\'
ORDER BY '. $sort_by;

$select_photos = my_query($sql, $conex);
$num_photos = my_num_rows($select_photos);

?>
      <form action="" method="post" name="gallery_form" id="gallery_form">
        <table border="0" cellpadding="2" cellspacing="0" width="100%">
          <tr>
            <td align="left" class="border_bottom_dotted small_text"><?= $num_photos; ?>
              fotos</td>
            <td align="right" class="border_bottom_dotted small_text">ordenar por
              <?php
$values = array('date' => 'Fecha', 'rate' => 'Valoración', 'route' => 'Escuela / sector');
$parameters = array('array' => $values, 'name' => 'sort_by', 'id' => 'sort_by', 'selected' => $_GET['sort'],
					'class' => 'small_text', 'on_change' => 'jump_sort();');
					
print_combo_array($parameters) 
			  ?>
            </td>
          </tr>
        </table>
      </form>
      <?php

$last_group = ''; $count = 0; $first = true;
while($record = my_fetch_array($select_photos)) {
	switch($_GET['sort']) {
		case 'route': $this_group = get_crag_sector_names($record['sector_id']); break;
		case 'rate': $this_group = write_rate_groups(floor($record['rating'])); break;
		default: $this_group = date2lan(substr($record['date_taken'], 0, 7), 'med');
	}
	
	
	if($this_group != $last_group) {
		if($first)
			$first = false;
		else
			echo '</tr></table>';
			
		# draw a group header
		echo '<div style="width:100%;" class="bg_standard title_3">'. $this_group .'</div>';
		$last_group = $this_group;

		echo '<table border="0" cellpadding="2" cellspacing="2"><tr>';
		$count = 0;
	}
	if($count % 6 == 0)
		echo '</tr><tr>';
	$count++;

?>
    <td align="center" valign="middle"><span class="thumbnail"><a href="JavaScript: show_photo_detail('<?= $record['photo_id']; ?>');"><img class="thin_border_picture" src="<?= $conf_images_path . $conf_photos_subpath . $record['thumb_file_name']; ?>" width="<?= $record['thumb_w']; ?>" height="<?= $record['thumb_h']; ?>" title="<?= $record['title']; ?>" /></a></span></td>
    <?php
}	//while($record = my_fetch_array($select_photos)) {


        ?>
  </tr>
</table>
</div>
</td>
<td valign="top"><div class="standard_container default_text"> <span class="standard_cont_title">Detalle de foto</span>
    <div id="photo_detail_container"></div>
  </div></td>
</tr>
</table>
<script language="javascript" src="inc/ajax.js"></script>
<script language="javascript" src="inc/photos.js"></script>
<script language="javascript">
function jump_sort() {
	my_sort = document.gallery_form.sort_by.value;
	document.location = '<?php
		echo $conf_main_page .'?';
		foreach($_GET as $key => $value)
			if($key != 'sort')
				echo $key .'='. $value .'&';
	?>sort=' + my_sort;
}

function show_photo_detail(photo_id) {
	url = 'inc/ajax.php?content=photo_detail&detail='+ photo_id +'&g_id=<?= $gallery_details['gallery_id']; ?>';
	getData(url, 'photo_detail_container');
}
</script>
