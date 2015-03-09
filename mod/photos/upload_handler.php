<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><div class="standard_container default_text"> <span class="standard_cont_title">Subiendo Foto</span><br />
        <?php

// http://www.webdeveloper.com/forum/showthread.php?t=101466

$upload_dir = $conf_images_path . $conf_photos_subpath; 
$fieldname = 'ph_file';
$max_allowed_size = 10485760;//8388608;

$img_max_width = 1600;
$img_max_height = 1600;

$med_img_max_width = 600;
$med_img_max_height = 600;

$small_img_max_width = 270;
$small_img_max_height = 500;

$thumb_img_max_width = 76;
$thumb_img_max_height = 76;

$array_extensions = array('jpg', 'gif', 'png', 'jpeg', 'jpe');

$error = '';

// possible PHP upload errors 
$errors = array(1 => 'php.ini max file size exceeded', 
                2 => 'max file size exceeded', 
                3 => 'file upload was only partial', 
                4 => 'no file was attached',
				5 => 'incorrect file',
				6 => 'incorrect file type'
				); 

# Check that the file is actually the one uploaded
if(!is_uploaded_file($_FILES[$fieldname]['tmp_name']))
	$error = $errors[5];


$allowed_formats = array('image/jpeg', 'image/png', 'image/gif' ,'image/pjpeg');	#pjpeg is for ie
# Check that it is an image
if(!in_array($_FILES[$fieldname]['type'], $allowed_formats))
	$error = $errors[6];

# Check max allowed size
if($_FILES[$fieldname]['size'] > $max_allowed_size)
	$error = $errors[2];

// make a unique filename for the uploaded file and check it is not already 
// taken... if it is already taken keep trying until we find a vacant one 
// sample filename: 1140732936-filename.jpg 
$now = time(); 
while(file_exists($uploadFilename = $upload_dir . $now .'-'. $_FILES[$fieldname]['name'])) 
    $now++; 
 
if($error) {
	echo $error;
	exit();
}

#Move the file to its destination
if(!move_uploaded_file($_FILES[$fieldname]['tmp_name'], $uploadFilename)) {
	$error = $errors[3];
}

# Extract EXIF details from image
if(function_exists('exif_read_data'))
	$arr_exif = exif_read_data($uploadFilename, 0, true);

$exif_date_time = str_replace(':', '-', substr($arr_exif['IFD0']['DateTime'], 0, 10));
unset($arr_exif);

# Resize image and create thumbnails
$filename = stripslashes($uploadFilename);
$filename_no_dir = substr($uploadFilename, strrpos($uploadFilename, '/') + 1);
//get_file_only($uploadFilename);
$extension = getExtension($filename);

if(in_array($extension, $array_extensions)) {
	switch($extension) {
		case 'jpg': case 'jpeg': case 'jpe':
			$im = imagecreatefromjpeg($uploadFilename);
			break;
		case 'gif':
			$im = imagecreatefromgif($uploadFilename);
			break;
		case 'png':
			$im = imagecreatefrompng($uploadFilename);
			break;
	}

	$width = imagesx($im);
	$height = imagesy($im);

	$arr_new_size = get_new_size($width, $height, $img_max_width, $img_max_height);
	$arr_small_size = get_new_size($width, $height, $small_img_max_width, $small_img_max_height);
	$arr_thumb_size = get_new_size($width, $height, $thumb_img_max_width, $thumb_img_max_height);
	if($_POST['blog']) $arr_med_size = get_new_size($width, $height, $med_img_max_width, $med_img_max_height);

	$tmp_new = imagecreatetruecolor($arr_new_size['w'], $arr_new_size['h']);
	$tmp_small = imagecreatetruecolor($arr_small_size['w'], $arr_small_size['h']);
	$tmp_thumb = imagecreatetruecolor($arr_thumb_size['w'], $arr_thumb_size['h']);
	if($_POST['blog']) $tmp_med = imagecreatetruecolor($arr_med_size['w'], $arr_med_size['h']);
	
	imagecopyresampled($tmp_new, $im, 0, 0, 0, 0, $arr_new_size['w'], $arr_new_size['h'], $width, $height);
	imagecopyresampled($tmp_small, $im, 0, 0, 0, 0, $arr_small_size['w'], $arr_small_size['h'], $width, $height);
	imagecopyresampled($tmp_thumb, $im, 0, 0, 0, 0, $arr_thumb_size['w'], $arr_thumb_size['h'], $width, $height);
	if($_POST['blog']) imagecopyresampled($tmp_med, $im, 0, 0, 0, 0, $arr_med_size['w'], $arr_med_size['h'], $width, $height);

	$file_name_new = $upload_dir . 'new_' . $filename_no_dir;
	$file_name_small = $upload_dir . 'small_' . $filename_no_dir;
	$file_name_thumb = $upload_dir . 'thumb_' . $filename_no_dir;
	if($_POST['blog']) $file_name_med = $upload_dir . 'med_' . $filename_no_dir;
	
	imagejpeg($tmp_new, $file_name_new, 100);
	imagejpeg($tmp_small, $file_name_small);
	imagejpeg($tmp_thumb, $file_name_thumb);
	if($_POST['blog']) imagejpeg($tmp_med, $file_name_med);
	
	imagedestroy($im);
	imagedestroy($tmp_new);
	imagedestroy($tmp_small);
	imagedestroy($tmp_thumb);
	if($_POST['blog']) imagedestroy($tmp_med);
	
	#Delete original file
	unlink($uploadFilename);
	
	#Photos must allways have a date
	if(!$_POST['photo_date']) {
		if(!$exif_date_time)
			$_POST['photo_date'] = date('Y-m-d');
		else
			$_POST['photo_date'] = $exif_date_time;
	}
	#Insert photos data into database:
	$arr_picture_insert = array('file_name' => 'new_'. $filename_no_dir
							   ,'width' => $arr_new_size['w']
							   ,'height' => $arr_new_size['h']
							   ,'small_file_name' => 'small_'. $filename_no_dir
							   ,'small_w' => $arr_small_size['w']
							   ,'small_h' => $arr_small_size['h']
							   ,'thumb_file_name' => 'thumb_'. $filename_no_dir
							   ,'thumb_w' => $arr_thumb_size['w']
							   ,'thumb_h' => $arr_thumb_size['h']
							   ,'type' => $extension
							   ,'title' => $_POST['ph_title']
							   ,'description' => $_POST['ph_description']
							   ,'owner_id' => $_SESSION['Login']['UserID']
							   ,'owner_name' => $_SESSION['Login']['User_Name']
							   ,'climber_name' => $_POST['ph_climber']
							   ,'author_name' => $_POST['ph_author']
							   ,'date_taken' => $_POST['photo_date']
							   ,'date_uploaded' => date('Y-m-d')
							   ,'object_id' => $_SESSION['Login']['UserID']
							   ,'object_type' => 'user'
							   ,'route_id' => $_POST['routes_combo']
							   ,'sector_id' => $_POST['sectors_combo']
							   ,'crag_id' => $_POST['crags_combo']
							   ,'is_public' => '1'
							   ,'control_code' => md5(date('YmdGis')));

	if($_POST['blog']) {
		$arr_picture_insert['med_file_name'] = 'med_'. $filename_no_dir;
		$arr_picture_insert['med_w'] = $arr_med_size['w'];
		$arr_picture_insert['med_h'] = $arr_med_size['h'];
	}
	
	$my_photo_id = insert_array_db ('photos', $arr_picture_insert, true);
	if(!$my_photo_id) {
		echo '<span class="error_message title_3">Ha habido un error al insertar la imágen en la base de datos</span>';
		write_log('error_photo', $arr_picture_insert['file_name'] .';; Insert DB');
		exit();
	}
	else {
		if($_POST['blog']) {
			# add the photo to the blog's gallery
			$blog = new blog($_POST['blog']);
			$gallery_id = $blog->get_gallery_id();//			$gallery_id = gallery::get_gallery_id($_POST['blog'], 'blog']);

			$ob_photo = new photo($my_photo_id);
			$ob_photo->add_photo_2_gallery($gallery_id);
			
			# add the photo as a new element in the blog
//			$blog->insert_photo($my_photo_id, $_POST['ph_description']);
		}
		else {
			# insert into user's gallery
			if(!add_photo_2_gallery($my_photo_id)){
				echo '<span class="error_message title_3">Ha habido un error al insertar la imágen en la galería del usuario</span>';
				write_log('error_photo', $arr_picture_insert['file_name'] .';; Insert Gallery');
				exit();
			}
		}
				
		# if photo has a crag, insert the picture into the crag's gallery
		if($_POST['crags_combo']) {
			# Check if the crag's gallery exists
			$crag_gallery = simple_select('galleries', 'object_id', $_POST['crags_combo'], 'gallery_id', ' AND object_type = \'crag\'');
			if($crag_gallery['gallery_id']) 
				$gallery_id = $crag_gallery['gallery_id'];
			else {
				# extract details about the crag
				$crag_details = simple_select('crags', 'crag_id', $_POST['crags_combo'], array('cname', 'crag_id_url'));
				if($crag_details['cname']) {
					$parameters = array('gname' => 'Fotos de '. $crag_details['cname']
									   ,'description' => 'Galería de fotos de '. $crag_details['cname']
									   ,'filter_sql' => 'object_id = \\\''. $_POST['crags_combo'] .'\\\' AND object_type = \\\'crag\\\''
									   ,'object_type' => 'crag'
									   ,'object_id' => $_POST['crags_combo']
									   ,'url_object' => $conf_main_page .'?mod=routes&view=detail_crag&detail='. $_POST['crags_combo'] .'&id='. $crag_details['crag_id_url']
									   ,'cover_photo_id' => $my_photo_id);

					$gallery_id = create_new_gallery($parameters);
				}	//if($crag_details['cname']) {
			}	//else { // if($crag_gallery['gallery_id']) 
			
			add_photo_2_gallery($my_photo_id, $gallery_id); 	# don't extract the error here, not necessary
			
		}	//if($_POST['crags_combo']) {
		
	}	//else {	//if(!$my_photo_id) {

	# Everything went fine
?>
        <table align="center" border="0" cellspacing="5" cellpadding="5" width="66%">
          <tr>
            <td colspan="2" class="title_3">Foto subida correctamente</td>
          </tr>
          <tr>
            <td><img src="<?php echo $file_name_small; ?>" border="0" width="<?php echo $arr_small_size['w']; ?>" height="<?php echo $arr_small_size['h']; ?>" title="<?php echo $_POST['ph_title'] ?>" /></td>
            <td valign="top"><ul class="title_4">
                <?php echo $_POST['ph_title']?'<li>'. $_POST['ph_title'] .'</li>':''; ?> <?php echo $_POST['ph_description']?'<li>'. $_POST['ph_description'] .'</li>':''; ?> <?php echo $_POST['photo_date']?'<li>'. date2lan($_POST['photo_date'], 'long') .'</li>':''; ?>
              </ul></td>
          </tr>
          <tr>
            <td colspan="2" class="title_3"><?php

if($_POST['blog']) {
?>
				<a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>&view=upload_photos&blog=<?= $_POST['blog']; ?>">Subir otra foto</a><br />
              <a href="<?php echo $conf_main_page .'?mod=report&view=new_report'; ?>">Volver al blog</a>
              <?php
}
else {
	# get user's gallery
	$arr_gallery = simple_select('galleries', 'object_id', $_SESSION['Login']['UserID'], 'gallery_id', ' AND object_type = \'user\'');
?> 				<a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>&view=upload_photos">Subir otra foto</a><br />
              <a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>&view=gallery&detail=<?= $arr_gallery['gallery_id']; ?>">Ver tus fotos</a>
              <?php 	}	?>
             
            </td>
          </tr>
        </table>
        <?php	
}	//if(in_array($extension, $array_extensions)) {
else
	echo 'error al subir la foto';

// --------------- AUXILIARY FUNCTIONS -----------------------
function get_new_size($w, $h, $max_w, $max_h) {
	if($w > $max_w) {
		$new_w = $max_w;
		$new_h = $new_w*($h/$w);
		# if new height is still larger than allowed, reduce furhter
		if($new_h > $max_h) {
			$new_h = $max_h;
			$new_w = $new_h*($w/$h);
		}
	}
	else {
		$new_w = $w;
		if($h > $max_h) {
			$new_h = $max_h;
			$new_w = $new_h*($w/$h); //$new_w*($max_h/$h)
		}
		else
			$new_h = $h;
	}

	return array('w' => $new_w, 'h' => $new_h);
}

function getExtension($str) {
	$i = strrpos($str,".");
	if (!$i) { return ""; } 
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return strtolower($ext);
}
?>
      </div></td>
  </tr>
</table>
