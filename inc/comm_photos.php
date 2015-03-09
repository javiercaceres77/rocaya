<?php
function add_photo_2_gallery($photo_id, $gallery_id = 'x') {
	# Adds a photo to a gallery, 
	# if the gallery id is not received (=='x') it assumes that it is the users's logged gallery
	# if the users's gallery doesn't exist it is created althoug this should not happen.
	global $conex;
	
	if($gallery_id == 'x') {	# assume its logged in user's gallery
		# ----- get the gallery id, wether it exists or it is a new one
		$user_gallery = simple_select('galleries', 'object_id', $_SESSION['Login']['UserID'], 'gallery_id', ' AND object_type = \'user\'');
		if($user_gallery['gallery_id']) {
			$gallery_id = $user_gallery['gallery_id'];		
		}
		else {	# The gallery doesn't exist, create a new one
			$parameters = array('gname' => 'Fotos de '. $_SESSION['Login']['User_Name']
							   ,'description' => 'Galería de fotos de '. $_SESSION['Login']['User_Name']
							   ,'filter_sql' => 'object_id = \\\''. $_SESSION['Login']['UserID'] .'\\\' AND object_type = \\\'user\\\''
							   ,'object_type' => 'user'
							   ,'object_id' => $_SESSION['Login']['UserID']
							   ,'cover_photo_id' => $photo_id);

			$gallery_id = create_new_gallery($parameters);
		}
	}	
	# ----- insert the photo into the gallery
	return insert_array_db('photo_gallery', array('photo_id' => $photo_id, 'gallery_id' => $gallery_id));
}

function create_new_gallery($parameters) {
	# $parameters is an array with all the required columns
	# The function returns the id of the newly created gallery
	# or false if it already exists or if ther is an error
	return insert_array_db('galleries', $parameters, true);
}

function draw_rate_box($photo_id, $rand_value, $rating = false) {
	global $conf_generic_user_id, $conex, $conf_images_path, $conf_main_page;
		
	if(!$rate)	# get photo's rating from table: 
		$arr_rating = simple_select('photos', 'photo_id', $photo_id, 'rating');
	else
		$arr_rating['rating'] = $rating;
		
	# get user's rating for this photo
	if($_SESSION['Login']['UserID'] != $conf_generic_user_id)
		$arr_user_rating = simple_select('users_photo_rating', 'user_id', $_SESSION['Login']['UserID'], 'rating', ' AND photo_id = \''. $photo_id .'\'');
	# get number of votes
	$select_num_votes = my_query('SELECT count(*) as n_votes FROM users_photo_rating WHERE photo_id = \''. $photo_id .'\'', $conex);

	$arr_num_votes = my_fetch_array($select_num_votes);	
	if($arr_num_votes['n_votes']) {
		if($arr_num_votes['n_votes'] == 1)
			$num_votes = '('. $arr_num_votes['n_votes'] .' voto)';
		else
			$num_votes = '('. $arr_num_votes['n_votes'] .' votos)';
	}

	$rate = $arr_rating['rating'];
	$user_rating = $arr_user_rating['rating'];
	
	$aprox_rate = round($rate * 2) / 2;
	$flat_rate = floor($aprox_rate);
	$half_rate = $aprox_rate - $flat_rate;	
?>
          <table border="0" cellpadding="3" cellspacing="1" class="bg_standard">
            <tr>
              <!--<td rowspan="2" class="bg_standard" valign="top">Valoración: </td>-->
              <td><span id="stars_container_<?= $photo_id; ?>" style="position:static;">
                <table border="0" cellpadding="0" cellspacing="0">
                  <tr>
<?php
		for($i = 1; $i <= 5; $i++) {
			if($i <= $flat_rate)	#yellow star
				$img = 'y_star.png';
			else {
				if($half_rate) {
					$half_rate = false;		# half yellow star
					$img = 'y_star1-2.png';
				}
				else					#white star
					$img = 'w_star.png';
			}
			
			if($_SESSION['Login']['UserID'] != $conf_generic_user_id) {
?>
                    <td width="16" onclick="JavsScript:record_rating(<?= '\''. $photo_id .'\',\''. $i .'\',\'rate_container_'. $photo_id .'-'. $rand_value .'\''; ?>);" onmouseover="JavaScript:set_stars('<?= $photo_id . $rand_value .'x'. $i; ?>');" onmouseout="reset_stars('<?= $photo_id . $rand_value; ?>');"><img src="<?= $conf_images_path . $img; ?>" width="16" height="16" id="y<?= $photo_id . $rand_value .'x'. $i; ?>" class="star" style="z-index:3" /><img src="<?= $conf_images_path; ?>b_star.png" width="16" height="16" id="b<?= $photo_id . $rand_value .'x'. $i; ?>" class="star" style="z-index:2" /><img src="<?= $conf_images_path; ?>w_star.png" width="16" height="16" id="w<?= $photo_id . $rand_value .'x'. $i; ?>" class="star" style="z-index:1" /></td>
<?php
			}	//if($_SESSION['Login']['UserID'] != $conf_generic_user_id)
			else	{
?>
                    <td width="16"><img src="<?= $conf_images_path . $img; ?>" width="16" height="16" style="position:absolute;" /></td>
<?php
			}
		}	//   for($i = 2; $i <= 5; $i++) {         
?>
                  </tr>
                  <tr>
                    <td colspan="5" height="14">&nbsp;</td>
                  </tr>
                </table>
                </span></td>
              <td align="right"><?= round($rate,1); ?>&nbsp;/&nbsp;5</td>
            </tr>
            <tr>
              <td colspan="2" class="small_text" align="left"><?php echo $num_votes. ' '; echo $user_rating?'&nbsp;&nbsp;tu voto: '. $user_rating .'/5':''; ?></td>
            </tr>
          </table>
<?php
}

function asign_rate($photo_id, $rate, $user_id, $div_id) {
	global $conex;
	# get photo_id current rate
	$arr_rating = simple_select('photos', 'photo_id', $photo_id, 'rating');
	# check if user has already voted
	$arr_user_rate = simple_select('users_photo_rating', 'photo_id', $photo_id, 'rating', ' AND user_id = '. $user_id);
	
	if($arr_user_rate['rating'])
		$sql = 'UPDATE users_photo_rating SET rating = '. $rate .', date_rated = \''. date('Y-m-d') .'\' WHERE photo_id = '. $photo_id .' AND user_id = '. $user_id;
	else
		$sql = 'INSERT INTO users_photo_rating (photo_id, user_id, rating, date_rated) VALUES ('. $photo_id .', '. $user_id .', '. $rate .', \''. date('Y-m-d') .'\')';
	
	$ins_upd_rating = my_query($sql, $conex);
	
	if($ins_upd_rating) {
		# get rand value from $div_id		//$rand_value = rand(1, 1000);
		$rand_value = substr($div_id, strpos($div_id, '-') + 1);
		update_photo_rating($photo_id, $user_id);
		draw_rate_box($photo_id, $rand_value);
	}
}

function update_photo_rating($photo_id, $user_id) {
	global $conex;
	$sql = 'SELECT avg(rating) as rate FROM users_photo_rating WHERE photo_id = '. $photo_id;
	$select_rate = my_query($sql, $conex);
	$arr_rate = my_fetch_array($select_rate);
	
	$sql = 'UPDATE photos SET rating = '. $arr_rate['rate'] .' WHERE photo_id = '. $photo_id;
	$upd_rate = my_query($sql, $conex);
		
	return $select_rate['rate'];
}

function write_photo_long_desc($parameters, $photo_id = 'x') {
	global $conex;
	if($photo_id == 'x') {	# The description comes in the parameters
		$record = $parameters;
		$photo_id = $parameters['photo_id'];
	}
	else {
		$arr_details = array('title', 'author_name', 'date_taken', 'rname', 'cname', 'crag_id', 'sector_id');
		$extra_conditions = ' AND p.flag_inappropriate = \'0\' AND p.flag_deleted = \'0\' AND p.flag_corrupt_unfound_file = \'0\' AND p.flag_is_thumbnail = \'0\' AND p.is_public = \'1\'';

		$record = simple_select('photos', 'photo_id', $photo_id, $arr_details, $extra_conditions);
	}

	# Get the number of comments
	$sql = 'SELECT count(*) as num_comments FROM comments WHERE object_id = \''. $record['photo_id'] .'\' AND object_type = \'photo\'';
	$select_comments = my_query($sql, $conex);
	$num_comments = my_fetch_array($select_comments);

	# Write the photo description				
	echo $record['title']? $record['title'] .'. ':'';
	echo $record['author_name']?'Foto tomada por '. $record['author_name'] .'<br>':'';
	//echo $record['date_taken']!='0000-00-00'?'Tomada el '. $record['date_taken']:'';
	echo $record['date_taken']!='0000-00-00'?date2lan($record['date_taken'], 'long'):'';
	echo $record['rname']?'<br>Vía: <a href="'. $conf_main_page .'?mod=routes&view=detail_sector&detail='. $record['sector_id'] .'">'. $record['rname'] .'</a>':'';
	echo $record['cname']?' (<a href="'. $conf_main_page .'?mod=routes&view=detail_crag&detail='. $record['crag_id'] .'">'. $record['cname'] .'</a>)':'';
	$plural = $num_comments['num_comments']>1?'s':'';
	echo $num_comments['num_comments']?'<br><a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_photo&detail='. $photo_id .'">'. $num_comments['num_comments'] .' comentario'. $plural .'</a>':'';
	echo '<br>';

}

function write_comments_summary($object_id, $object_type = 'photo') {
	# writes summary of last three comments of the object
	global $conex, $conf_main_page;
	$max_num_comments = 3;
	$message_length = 100;
	
	# extract the last comments:
	$sql = 'SELECT comment_text, author_name, comment_date FROM comments 
	WHERE object_type = \''. $object_type .'\' AND object_id = \''. $object_id .'\'
	  AND flag_inappropriate = \'0\' AND flag_user_removed = \'0\' AND flag_admin_removed = \'0\'
	ORDER BY comment_date DESC
	LIMIT 0, '. $max_num_comments;
	  
	$select_comm = my_query($sql, $conex);
	$num_comments = my_num_rows($select_comm);
	
	echo '<div class="small_text">';
	while($record = my_fetch_array($select_comm)) {
		echo '<strong>'. $record['author_name'] .': </strong>';
		echo substr($record['comment_text'], 0, $message_length); 
		echo strlen($record['comment_text']) > $message_length?'...':''; 
		echo '.&nbsp;&nbsp;'. date2lan($record['comment_date'], 'med');
		echo '<br />';
	}
	
	if($num_comments)
	echo '<a href="'. $conf_main_page .'?mod='. $_GET['mod']. '&view=detail_photo&detail='. $object_id .'">Ver todos los comentarios</a></div>';
	else
	echo 'Ningún comentario sobre esta foto</div>';
}

function write_rate_groups($rate) {
	global $conf_images_path;
	$white_st = '<img src="'. $conf_images_path .'w_star.png" width="16" height="16">';
	$yellow_st = '<img src="'. $conf_images_path .'y_star.png" width="16" height="16">';
	
	switch($rate) {
		case '0': return 'no valoradas'; break;
		case '1': return $yellow_st . str_repeat($white_st, 4) .' 1 - 2'; break;
		case '2': return str_repeat($yellow_st, 2) . str_repeat($white_st, 3) .' 2 - 3'; break;
		case '3': return str_repeat($yellow_st, 3) . str_repeat($white_st, 2) .' 3 - 4'; break;
		case '4': return str_repeat($yellow_st, 4) . str_repeat($white_st, 1) .' 4 - 5'; break;
		case '5': return str_repeat($yellow_st, 5) .' 5'; break;
	}
}

?>