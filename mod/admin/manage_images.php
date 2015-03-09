<?php

$is_user_admin = simple_select('users', 'user_id', $_SESSION['Login']['UserID'], 'isadmin');

if($is_user_admin['isadmin']) {

?>
<script language="javascript">

function add_img2db() {
	var option = '';
	if(document.getElementById('empty_table').checked)
		option = 'trunc';
	document.location = '?func=add2db&option='+ option;
}

function asign_img2routes() {
	document.location = '?func=assign2routes';
}

</script>
<p class="default_text"><?php

/*	if($_GET['func'] == 'add2db') {		// add contents of images folder to images table
		if($_GET['option'] == 'trunc') {	// emty table before loading
			print('<br> Truncating images table ... <br>');
			$del_imag = my_query('DELETE FROM images', $conex);
			if($del_imag)
				print('Images have been deleted<br>');
		}
		
		$array_extensions = array('jpg', 'gif', 'png', 'jpeg', 'jpe');
		$array_images = dump_table('images', 'file_name', 'img_id');

		$dir = '../images/pics_routes/';
		$dh = opendir($dir);
		
		$sql = 'INSERT INTO images (file_name, width, height, ttype) VALUES ';
		$is_first = true;
		
		while(false !== ($file = readdir($dh))) {
			$ext = substr($file, strlen($file) - 3);
			if(in_array($ext, $array_extensions)) {
				if(!$array_images[$file]) {
					switch($ext) {
						case 'jpg': 
						case 'jpeg':
						case 'jpe':
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
				print('</p><p class="default_text">All images inserted correctly</p>');
		}
		else
			print('</p><p class="default_text">There were no new images to insert</p>');
	}
//-------------------------------------------------------------------------------------------
	if($_GET['func'] == 'assign2routes') {		// asign images to routes
	// falta el identificador de provincia y modificar los substrings después de haber puesto la provincia en las imágenes.
		$sql = 'SELECT r.route_id, i.file_name
FROM images i, routes r
WHERE ROUND( SUBSTRING( i.file_name, 5, 2 ) ) = r.crag_province_id
AND ROUND( SUBSTRING( i.file_name, 8, 3 ) ) = r.crag_id
AND ROUND( SUBSTRING( i.file_name, 11, 2 ) ) = r.sector_id
AND ROUND( SUBSTRING( i.file_name, 14, 2 ) ) = r.number';
  		$select_img = my_query($sql, $conex);
		
		while($record = my_fetch_array($select_img)) {
			$sql = 'UPDATE routes SET img_via = "'. $record['file_name'] .'" WHERE route_id = '. $record['route_id'];
  
			$sql_update = my_query($sql, $conex);
			if($sql_update) {
				print('<br> Imagen '. $record['file_name'] .'asignada a la ruta');
			}
			else
				print('<br> Error al asignar la imagen '. $record['file_mane']);
			
			my_free_result($sql_udpate);
		} // while($record = my_fetch_array($select_img)) {	

		$sql = 'SELECT r.route_id, i.file_name
FROM images i, routes r
WHERE ROUND( SUBSTRING( i.file_name, 5, 2 ) ) = r.crag_province_id
AND ROUND( SUBSTRING( i.file_name, 8, 3 ) ) = r.crag_id
AND ROUND( SUBSTRING( i.file_name, 11, 2 ) ) = r.sector_id
AND ROUND( SUBSTRING( i.file_name, 14, 2 ) ) =  \'00\'
AND SUBSTRING( i.file_name, 13, 1 ) = SUBSTRING( r.img_via, 13, 1 ) ';
		
		$select_img = my_query($sql, $conex); // $select_img used again here

		while($record = my_fetch_array($select_img)) {
			$sql = 'UPDATE routes SET img_bck = "'. $record['file_name'] .'" WHERE route_id = '. $record['route_id'];
  
			$sql_update = my_query($sql, $conex);
			if($sql_update) {
				print('<br> Imagen '. $record['file_name'] .' asignada a la ruta');
			}
			else
				print('<br> Error al asignar la imagen '. $record['file_mane']);
			
			my_free_result($sql_udpate);
		} // while($record = my_fetch_array($select_img)) {	

		
	}	//if($_GET['func'] == 'assign2routes') {*/
}	//if($is_user_admin['isadmin']) {
else {
	session_unset();
}

?>
</p>
<form id="form1" name="form1" method="post" action="">
  <ul>
    <li class="default_text"><a href="JavaScript:add_img2db()">Añadir imágenes en el directorio &quot;<?= $conf_images_path ?>&quot; a la base de datos</a> <br />
      <label>
      <input type="checkbox" name="empty_table" id="empty_table" />
      Vaciar tabla imágenes antes</label>
    </li>
    <li class="default_text"><a href="JavaScript:asign_img2routes()">Asignar imágenes en la tabla a sus rutas correspondientes</a></li>
  </ul>
</form>
