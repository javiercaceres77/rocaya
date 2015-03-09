<div class="standard_container default_text"> <span class="standard_cont_title">Agregando imágenes a la tabla de imágenes</span><br />
<a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] ?>">&lt; Return</a>
<?php

$is_user_admin = simple_select('users', 'user_id', $_SESSION['Login']['UserID'], 'isadmin');

if($is_user_admin['isadmin']) {

	if($_GET['option'] == 'trunc') {	// emty table before loading
		print('<br> Truncating images table ... <br>');
		$del_imag = my_query('DELETE FROM images', $conex);
		if($del_imag)
			print('Images have been deleted<br>');
	}
		
	$array_extensions = array('jpg', 'gif', 'png', 'jpeg', 'jpe');
	$array_images = dump_table('images', 'file_name', 'img_id');

	$dir = $conf_images_path . $conf_images_routes_subpath;
	$dh = opendir($dir);
		
	$sql = 'INSERT INTO images (file_name, width, height, ttype) VALUES ';
	$is_first = true;
		
	while(false !== ($file = readdir($dh))) {
		$ext = substr($file, strlen($file) - 3);
		if(in_array($ext, $array_extensions)) {
			if(!$array_images[$file]) {
				switch($ext) {
					case 'jpg': case 'jpeg': case 'jpe':
						$im = imagecreatefromjpeg($dir . $file);
						break;
					case 'gif':
						$im = imagecreatefromgif($dir . $file);
						break;
					case 'png':
						$im = imagecreatefrompng($dir . $file);
						break;
				}
	
				$width = imagesx($im);
				$height = imagesy($im);
				$size = filesize($dir . $file);
				@imagedestroy($im);
					
				if($is_first) 
					$is_first = false;
				else
					$sql .=',';
	
				if(substr($file, 10, 2) === '00') 	// it is a background image === means it is equal and of the same type
					$my_type = 'bkg';
				else
					$my_type = 'via';
					
				$sql .= '(\''. $file .'\', '. $width .', '. $height .', \''. $my_type .'\')';
					
				print('Added image: '. $file .' '. $width .'x'. $height .' ('. $size .' bytes)<br>');
			}	//if(!$array_images[$file]) {
		} // if(in_array($ext, $array_images)) {
	}	// while(false !== ($file = readdir($dh))) {
		
	if(!$is_first) {		// at least one image was inserted
		$ins_imag = my_query($sql, $conex);
		if($ins_imag)
			print('All images inserted correctly');
	}
	else
		print('There were no new images to insert');
}
?><br><br>
<a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] ?>">&lt; Return</a></div>