<style type="text/css">
.star {
	position: absolute;
}
.star:hover {
	cursor:pointer;
}
</style>
<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top" width="33%"><div class="standard_container default_text"> <span class="standard_cont_title">Tus Fotos</span><br />
<?php 

if($_SESSION['Login']['UserID'] != $conf_generic_user_id) {

	#Get list of last 10 users' photos
	$sql = 'SELECT p.photo_id, p.small_file_name, p.author_name, p.author_id, p.small_w, p.small_h, p.thumb_file_name, p.thumb_w, p.thumb_h, p.title, p.date_taken, p.date_uploaded, r.rname, p.route_id, c.cname, c.crag_id, r.sector_id
	FROM photos p
	LEFT JOIN routes r ON r.route_id = p.route_id
	LEFT JOIN crags c ON c.crag_id = p.crag_id
	WHERE p.owner_id = \''. $_SESSION['Login']['UserID'] .'\'
	AND p.flag_inappropriate = \'0\' AND p.flag_deleted = \'0\' AND p.flag_corrupt_unfound_file = \'0\' AND p.flag_is_thumbnail = \'0\' AND p.is_public = \'1\'
	ORDER BY p.date_uploaded DESC, p.photo_id DESC LIMIT 0, 10';
	
	$select_photos = my_query($sql, $conex);
	
	$num_photos = my_num_rows($select_photos);
	
	# Check if current user has a gallery
	$user_has_gallery = exists_record('galleries', array('object_id', 'object_type'), array($_SESSION['Login']['UserID'], 'user'));
	if($user_has_gallery)
		$link = $conf_main_page .'?mod='. $_GET['mod'] .'&view=upload_photos';
	else
		$link = $conf_main_page .'?mod='. $_GET['mod'] .'&view=photos_first';
?>
        <div align="right"><a href="<?php echo $link; ?>"><img src="<?php echo $conf_images_path; ?>upload.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;Subir foto</a></div>
        <br />
        <?php		
	
	if($num_photos > 0) {
		$first = true; $second = false; $count=0;
		echo '<div class="title_3">Últimas</div>';
		while($record = my_fetch_array($select_photos)) {
			if($first) {
				# The first picture is dispayed small, not thumb.
				# Get the number of comments
				$sql = 'SELECT count(*) as num_comments FROM comments WHERE object_id = \''. $record['photo_id'] .'\' AND object_type = \'photo\'';
				$select_comments = my_query($sql, $conex);
				$num_comments = my_fetch_array($select_comments);
?>
        <a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>&view=detail_photo&detail=<?php echo $record['photo_id']; ?>&g_o_id=<?= $_SESSION['Login']['UserID']; ?>&g_o_ty=user"><img class="thin_border_picture" border="0" src="<?php echo $conf_images_path . $conf_photos_subpath . $record['small_file_name']; ?>" width="<?php echo $record['small_w']; ?>" height="<?php echo $record['small_h']; ?>" title="<?= $record['title']; ?>" /></a><br />
        <?php 
				$rand_value = rand(1, 1000);
				echo '<div id="rate_container_'. $record['photo_id'] .'-'. $rand_value .'" class="default_text" align="right">'; 
				draw_rate_box($record['photo_id'], $rand_value);
				echo '</div>';

				$parameters = array('title' => $record['title'], 'author_name' => $record['author_name'], 'date_taken' => $record['date_taken'], 'rname' => $record['rname']
								   ,'cname' => $record['cname'], 'crag_id' => $record['crag_id'], 'sector_id' => $record['sector_id'], 'photo_id' => $record['photo_id']);
		
				write_photo_long_desc($parameters);

				echo '<br>';

				$first = false; $second = true;
			}	//if($first) {
			else {
				if($second) {
					echo '<table width="100%" border="0" align="center" cellpadding="3" cellspacing="2"><tr>';
					$second = false;
				}
				$count++;
				echo '<td align="center"><a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_photo&detail='. $record['photo_id'] .'&g_o_id='. $_SESSION['Login']['UserID'] .'&g_o_ty=user"><img class="thin_border_picture" border="0" src="'. $conf_images_path . $conf_photos_subpath . $record['thumb_file_name'] .'" width="'. $record['thumb_w'] .'" height="'. $record['thumb_h'] .'" title="'. $record['title'] .'" /></a></td>';
				if($count%3 == 0)
					echo '</tr><tr>';
			}
		}	//while($record = my_fetch_array($select_photos)) {
		if($count >= 1)
			echo '</tr></table>';
?>
        <br />
        <span class="title_3"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>&view=gallery&object_id=<?= $_SESSION['Login']['UserID']; ?>&object_type=user">Ver todas tus fotos</a></span><br />
        <br />
        <?php
	}	//	if($num_photos > 0) {
	else {
?>
        No has subido ninguna foto en Rocaya.<br />
        Puedes <a href="<?php echo $link; ?>">colgar fotos</a> de tus escaladas para que todo el mundo las pueda ver.<br />
        <br />
<?php 
	}
}	//if($_SESSION['Login']['UserID'] != $conf_generic_user_id) { 
else
	echo '<a href="'. $conf_main_page .'?mod=home&view=new_user">Regístrate</a> para subir fotos a Rocaya.com<br />';
?>
      </div>
      <div class="standard_container default_text"> <span class="standard_cont_title">Últimas fotos</span><br />
<?php 

#Get list of last 10 photos
$sql = 'SELECT p.photo_id, p.small_file_name, p.owner_id, p.author_name, p.author_id, p.small_w, p.small_h, p.thumb_file_name, p.thumb_w, p.thumb_h, p.title, p.date_taken, p.date_uploaded, r.rname, p.route_id, c.cname, c.crag_id, r.sector_id
FROM photos p
LEFT JOIN routes r ON r.route_id = p.route_id
LEFT JOIN crags c ON c.crag_id = p.crag_id
WHERE p.flag_inappropriate = \'0\' AND p.flag_deleted = \'0\' AND p.flag_corrupt_unfound_file = \'0\' AND p.flag_is_thumbnail = \'0\' AND p.is_public = \'1\'
ORDER BY p.date_uploaded DESC, p.photo_id DESC LIMIT 0, 10';
	
$select_photos = my_query($sql, $conex);
	
$num_photos = my_num_rows($select_photos);
	
	
$first = true; $second = false; $count=0;
while($record = my_fetch_array($select_photos)) {
	if($first) {
		# The first picture is dispayed small, not thumb.
		# Get the number of comments
		$sql = 'SELECT count(*) as num_comments FROM comments WHERE object_id = \''. $record['photo_id'] .'\' AND object_type = \'photo\'';
		$select_comments = my_query($sql, $conex);
		$num_comments = my_fetch_array($select_comments);
?>
        <a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>&view=detail_photo&detail=<?php echo $record['photo_id']; ?>&g_o_id=<?= $record['owner_id']; ?>&g_o_ty=user"><img class="thin_border_picture" border="0" src="<?php echo $conf_images_path . $conf_photos_subpath . $record['small_file_name']; ?>" width="<?php echo $record['small_w']; ?>" height="<?php echo $record['small_h']; ?>" title="<?= $record['title']; ?>" /></a><br />
        <?php 
		$rand_value = rand(1, 1000);
		echo '<div id="rate_container_'. $record['photo_id'] .'-'. $rand_value .'" class="default_text" align="right">'; 
		draw_rate_box($record['photo_id'], $rand_value);
		echo '</div>';

		$parameters = array('title' => $record['title'], 'author_name' => $record['author_name'], 'date_taken' => $record['date_taken'], 'rname' => $record['rname']
						   ,'cname' => $record['cname'], 'crag_id' => $record['crag_id'], 'sector_id' => $record['sector_id'], 'photo_id' => $record['photo_id']);

		write_photo_long_desc($parameters);
		echo '<br>';

		$first = false; $second = true;
	}	//if($first) {
	else {
		if($second) {
			echo '<table width="100%" border="0" align="center" cellpadding="3" cellspacing="2"><tr>';
			$second = false;
		}
		$count++;
		echo '<td align="center"><a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_photo&detail='. $record['photo_id'] .'&g_o_id='. $record['owner_id'] .'&g_o_ty=user"><img class="thin_border_picture" border="0" src="'. $conf_images_path . $conf_photos_subpath . $record['thumb_file_name'] .'" width="'. $record['thumb_w'] .'" height="'. $record['thumb_h'] .'" title="'. $record['title'] .'" /></a></td>';
		if($count%3 == 0)
			echo '</tr><tr>';
	}
}	//while($record = my_fetch_array($select_photos)) {

if($count >= 1)
	echo '</tr></table>';
?>
      </div></td>
    <td valign="top"><div class="standard_container default_text"> <span class="standard_cont_title">Fotos destacadas</span><br />
        <table width="100%" border="0" cellspacing="5" cellpadding="0">
          <tr>
            <td width="50%" valign="top"><span class="title_3">Mejor Valoradas</span>
              <table border="0" cellpadding="0" cellspacing="4" width="100%">
                <?php 

	#Get list of best 5 rated photos
	$sql = 'SELECT p.photo_id, p.small_file_name, p.author_name, p.author_id, p.small_w, p.small_h, p.thumb_file_name, p.thumb_w, p.thumb_h, p.title, p.date_taken, p.date_uploaded, r.rname, p.route_id, c.cname, c.crag_id, r.sector_id, p.rating
	FROM photos p
	LEFT JOIN routes r ON r.route_id = p.route_id
	LEFT JOIN crags c ON c.crag_id = p.crag_id
	WHERE p.flag_inappropriate = \'0\' AND p.flag_deleted = \'0\' AND p.flag_corrupt_unfound_file = \'0\' AND p.flag_is_thumbnail = \'0\' AND p.is_public = \'1\'
	ORDER BY p.rating DESC, p.date_uploaded DESC, p.photo_id DESC LIMIT 0, 5';
	
	$select_photos = my_query($sql, $conex);
	
	$num_photos = my_num_rows($select_photos);

	$first = true; $count=1;
	while($record = my_fetch_array($select_photos)) {
		echo '<tr><td colspan="2" class="bg_standard"><span class="title_3">'. $count .'.</span></td></tr>'; $count++;
	
		$parameters = array('title' => $record['title'], 'author_name' => $record['author_name'], 'date_taken' => $record['date_taken'], 'rname' => $record['rname']
						   ,'cname' => $record['cname'], 'crag_id' => $record['crag_id'], 'sector_id' => $record['sector_id'], 'photo_id' => $record['photo_id']);

		if($first) {
			# The first picture is dispayed small, not thumb.
?>
                <tr>
                  <td colspan="2"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>&view=detail_photo&detail=<?php echo $record['photo_id']; ?>"><img class="thin_border_picture" border="0" src="<?php echo $conf_images_path . $conf_photos_subpath . $record['small_file_name']; ?>" width="<?php echo $record['small_w']; ?>" height="<?php echo $record['small_h']; ?>" title="<?= $record['title']; ?>" /></a></td>
                </tr>
                <?php 
			$rand_value = rand(1, 1000);
			echo '<tr><td colspan="2"><div id="rate_container_'. $record['photo_id'] .'-'. $rand_value .'" class="default_text" align="right">'; 
			draw_rate_box($record['photo_id'], $rand_value);
			echo '</div></td></tr>';
		
			echo '<tr><td colspan="2">'; write_photo_long_desc($parameters); echo '</td></tr>';
	
			$first = false;
		}	//if($first) {
		else {
			echo '<tr><td valign="top"><a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_photo&detail='. $record['photo_id'] .'"><img class="thin_border_picture" border="0" src="'. $conf_images_path . $conf_photos_subpath . $record['thumb_file_name'] .'" width="'. $record['thumb_w'] .'" height="'. $record['thumb_h'] .'" title="'. $record['title'] .'" /></a></td>';
			$rand_value = rand(1, 1000);
			echo '<td valign="top"><div id="rate_container_'. $record['photo_id'] .'-'. $rand_value .'" class="default_text" align="right">'; 
				draw_rate_box($record['photo_id'], $rand_value); echo '</div>';
				write_photo_long_desc($parameters);
			echo '</td></tr>';
		
		}


//		echo '</tr><tr>';
	}	//while($record = my_fetch_array($select_photos)) {

?>
              </table></td>
            <td width="1" class="bg_standard">&nbsp;</td>
            <td width="50%" valign="top"><span class="title_3">Más comentadas</span>
              <table border="0" cellpadding="0" cellspacing="4" width="100%">
                <?php            

	#Get list of 5 most commented photos
	$sql = 'SELECT p.photo_id, p.small_file_name, p.author_name, p.author_id, p.small_w, p.small_h, p.thumb_file_name, p.thumb_w, p.thumb_h, p.title, p.date_taken, p.date_uploaded, r.rname, p.route_id, c.cname, c.crag_id, r.sector_id, p.rating, count(co.comment_id) as n_comments
	FROM photos p
	INNER JOIN comments co ON co.object_id = p.photo_id
	LEFT JOIN routes r ON r.route_id = p.route_id
	LEFT JOIN crags c ON c.crag_id = p.crag_id
	WHERE p.flag_inappropriate = \'0\' AND p.flag_deleted = \'0\' AND p.flag_corrupt_unfound_file = \'0\' AND p.flag_is_thumbnail = \'0\' AND p.is_public = \'1\'
	  AND co.object_type = \'photo\' AND co.flag_inappropriate = \'0\' AND flag_user_removed = \'0\' AND flag_admin_removed = \'0\'
	GROUP BY p.photo_id, p.small_file_name, p.author_name, p.author_id, p.small_w, p.small_h, p.thumb_file_name, p.thumb_w, p.thumb_h, p.title, p.date_taken, p.date_uploaded, r.rname, p.route_id, c.cname, c.crag_id, r.sector_id, p.rating
	ORDER BY n_comments DESC, p.date_uploaded DESC, p.photo_id DESC LIMIT 0, 5';
	
	$select_photos = my_query($sql, $conex);
	
	$num_photos = my_num_rows($select_photos);

	$first = true; $count=1;
	while($record = my_fetch_array($select_photos)) {
		echo '<tr><td colspan="2" class="bg_standard"><span class="title_3">'. $count .'.</span></td></tr>'; $count++;
	
		$parameters = array('title' => $record['title'], 'author_name' => $record['author_name'], 'date_taken' => $record['date_taken'], 'rname' => $record['rname']
						   ,'cname' => $record['cname'], 'crag_id' => $record['crag_id'], 'sector_id' => $record['sector_id'], 'photo_id' => $record['photo_id']);

		if($first) {
			# The first picture is dispayed small, not thumb.
?>
                <tr>
                  <td colspan="2"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>&view=detail_photo&detail=<?php echo $record['photo_id']; ?>"><img class="thin_border_picture" border="0" src="<?php echo $conf_images_path . $conf_photos_subpath . $record['small_file_name']; ?>" width="<?php echo $record['small_w']; ?>" height="<?php echo $record['small_h']; ?>" title="<?= $record['title']; ?>" /></a></td>
                </tr>
                <?php 
			echo '<tr><td colspan="2">';
			write_comments_summary($record['photo_id']);
			echo '</td></tr>';
		
			echo '<tr><td colspan="2">'; write_photo_long_desc($parameters); echo '</td></tr>';
	
			$first = false;
		}	//if($first) {
		else {
			echo '<tr><td valign="top"><a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_photo&detail='. $record['photo_id'] .'"><img class="thin_border_picture" border="0" src="'. $conf_images_path . $conf_photos_subpath . $record['thumb_file_name'] .'" width="'. $record['thumb_w'] .'" height="'. $record['thumb_h'] .'" title="'. $record['title'] .'" /></a></td>';
			echo '<td valign="top">';
			write_comments_summary($record['photo_id']);
			echo '<br>';
			write_photo_long_desc($parameters);
			echo '</td></tr>';
		
		}


//		echo '</tr><tr>';
	}	//while($record = my_fetch_array($select_photos)) {

?>
              </table></td>
          </tr>
        </table>
      </div></td>
  </tr>
</table>
<script language="javascript" src="inc/ajax.js"></script>
<script language="javascript" src="inc/photos.js"></script>
