<?php

# Check if the user has a blog that is being edited
$blog_id = blog::is_user_editing_blog($_SESSION['Login']['UserID']);
if($blog_id) {
	# create blog object
	$blog = new blog($blog_id);

	if($_POST) {
		# update the blog with the contents
		$arr_update = array('author_name' => $_POST['author_name'], 'title' => $_POST['title'], 'summary' => $_POST['summary'],
							'date_from' => $_POST['date_from'], 'date_to' => $_POST['date_to'], 'key_words' => $_POST['key_words'],
							'url_id' => get_url_id($_POST['title']), 'source_file' => $_POST['source_file'],
							'photo_cover_id' => $_POST['photo_cover_id']);
	
		$blog->update_header($arr_update);
		
		# udpate the rest of the elements
	/*	foreach($_POST as $key => $value) {
			if(substr($key, 0, 3) == 'txt') {
				$element_id = substr($key, 4);
				$blog->update_txt_element($element_id, $value);
			}
			# photos can be deleted or moved, if we want to edit, we must open the photo.
		}*/


		# if there is a new element, update it
/*		if($_POST['new_text_element']) {
			$blog->insert_text($_POST['new_text_element']);
		}
*/
		if(!$blog->get_gallery_id())	{
			$parameters = array('title' => $_POST['title']
							   ,'filter_sql' => 'object_id = \\\''. $blog_id .'\\\' AND object_type = \\\'blog\\\''
							   ,'object_type' => 'blog'
							   ,'object_id' => $blog_id);
	
			$gallery_id = gallery::create_gallery($parameters);
		}
				
		if($_POST['new_element_type'] == 'photos') {
			# insert photo now that the elements have been udpated, forward to a new location to insert the photo
			echo '<script language="javascript"> document.location = "'. $conf_main_page .'?mod=photos&view=upload_photos&blog='. $blog_id .'"; </script>';
			exit();
			#
		}
		
		if($_POST['new_element_type'] == 'preview') {
			# forward to view report with preview = true
			echo '<script language="javascript"> document.location = "'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=det_blog&detail='. $blog_id .'&preview=1"; </script>';
			exit();
		}
		
	
/*		if($_POST['set_photo_cover']) {
			$blog->set_photo_cover($_POST['set_photo_cover']);
		}*/
		
/*		if($_POST['remove_element']) {
			$blog->remove_element($_POST['remove_element']);
		}*/
		
/*		if($_POST['move_up']) {
			if($_POST['move_up'] > 1) 
				$blog->exchange_elements($_POST['move_up'], $_POST['move_up'] - 1);
		}*/
		
/*		if($_POST['move_down']) {
			if($_POST['move_down'] < $blog->get_num_elements())
				$blog->exchange_elements($_POST['move_down'], $_POST['move_down'] + 1);
		}*/
		
		$fields_class = 'inputdisc';
	}	//if($_POST) 
	else {
		$arr_props = $blog->get_properties();
		foreach($arr_props as $key => $value)
			$_POST[$key] = htmlspecialchars_decode($value);
			
		$fields_class = 'inputdisc';
	}
}	//if($blog_id) {
else {
	if($_POST) {
		# insert the header in the table
		$date_from = new date_time($_POST['date_from']);
		$date_to = new date_time($_POST['date_to']);
		
		$arr_insert = array('url_id' => get_url_id($_POST['title'])
						   ,'author_id' => $_POST['author_id']
						   ,'author_name' => $_POST['author_name']
						   ,'date_from' => $date_from->odate
						   ,'date_to' => $date_to->odate
						   ,'title' => $_POST['title']
						   ,'summary' => substr($_POST['summary'], 0, 1000)
						   ,'key_words' => $_POST['key_words']
						   ,'status' => 'editing'
						   ,'flag_master' => '1'
						   ,'control_code' => md5($_POST['title'])
						   ,'source_file' => $_POST['source_file']);
		
		$blog_id = insert_array_db('blog_head', $arr_insert, true);
		
		if($blog_id) {
			
			$blog = new blog($blog_id);
			
			# Generate a new gallery for this blog
			$parameters = array('filter_sql' => 'object_id = \\\''. $blog_id .'\\\' AND object_type = \\\'blog\\\''
							   ,'object_type' => 'blog'
							   ,'object_id' => $blog_id);
	
			$gallery_id = gallery::create_gallery($parameters);
			$fields_class = 'inputdisc';
		}
		else	{
			$error = 'Error al insertar en la base de datos';		
			$fields_class = 'inputlarge';
		}
	}
	else {
		$user = new user($_SESSION['Login']['UserID']);
		$_POST['date_from'] = date('Y-m-d');
		$_POST['date_to'] = '9999-12-31';
		$_POST['author_name'] = $user->get_user_name();
		$fields_class = 'inputlarge';
	}
}

if($error)
	echo '<span class="error_message">'. $error .'</span>';
?>
<table width="70%" cellpadding="0" cellspacing="0" align="center">
  <tr>
    <td valign="top"><div class="standard_container default_text"><span class="standard_cont_title">Nueva entrada de blog</span><br />
        <form name="form_blog" id="form_blog" action="" method="post">
          <table border="0" cellspacing="4" cellpadding="4" class="default_text" align="center" width="100%">
            <tr>
              <td align="right" class="bg_standard">Fichero Origen *</td>
              <td id="cont_source_file"><input type="text" name="source_file" id="source_file" maxlength="75" class="<?= $fields_class; ?>" value="<?= $_POST['source_file']; ?>" />
                <span class="small_text">75 caractéres máx</span></td>
            </tr>
            <tr>
              <td align="right" class="bg_standard">Autor *</td>
              <td id="count_author_name"><input type="text" name="author_name" id="author_name" maxlength="250" class="<?= $fields_class; ?>" value="<?= $_POST['author_name']; ?>" />
                <input type="hidden" name="author_id" id="author_id" value="<?= $user->user_id; ?>"></td>
            </tr>
            <tr>
              <td align="right" class="bg_standard">Título *</td>
              <td id="cont_title"><input type="text" name="title" id="title" maxlength="250" class="<?= $fields_class; ?>" value="<?= $_POST['title']; ?>" />
                <span class="small_text">250 caractéres máx</span></td>
            </tr>
            <tr>
              <td align="right" class="bg_standard">Resúmen *</td>
              <td id="cont_summary"><textarea name="summary" id="summary" rows="4" style="width:300px;" class="<?= $fields_class; ?>"><?= $_POST['summary']; ?>
</textarea>
                <span class="small_text">1000 caractéres máx</span></td>
            </tr>
            <tr>
              <td align="right" class="bg_standard">Fecha de publicación *</td>
              <td id="cont_date_from"><input type="text" name="date_from" id="date_from" maxlength="10" class="<?= $fields_class; ?>" value="<?= $_POST['date_from']; ?>" />
                <span class="small_text"> aaaa-mm-dd</span></td>
            </tr>
            <tr>
              <td align="right" class="bg_standard">Fecha de fin *</td>
              <td id="cont_date_to"><input type="text" name="date_to" id="date_to" maxlength="10" class="<?= $fields_class; ?>" value="<?= $_POST['date_to']; ?>" />
                <span class="small_text"> aaaa-mm-dd</span></td>
            </tr>
            <tr>
              <td align="right" class="bg_standard">Palabras Clave</td>
              <td><input type="text" name="key_words" id="key_words" maxlength="250" class="<?= $fields_class; ?>" value="<?= $_POST['key_words']; ?>" /></td>
            </tr>
<?php	if($blog_id) {	?>
            <tr>
              <td align="right" class="bg_standard">id de foto de portada *</td>
              <td><input type="text" name="photo_cover_id" id="photo_cover_id" maxlength="5" class="<?= $fields_class; ?>" value="<?= $_POST['photo_cover_id']; ?>" /></td>
            </tr>
<?php	}	?>            
            <tr>
              <td>* campos obligatorios</td>
              <td><input type="hidden" name="new_element_type" /></td>
            </tr>
            <tr>
              <td colspan="2"><?php   
	if(!$arr_props)
		if(isset($blog))
			$arr_props = $blog->get_properties();

	if($arr_props['photo_cover_id']) {
		echo 'Foto de portada: <br>';
		$photo_cover = new photo($arr_props['photo_cover_id']);
		$photo_cover->print_small_photo(false);
	}
		 ?>
              </td>
            </tr>
          </table>
          <?php
if($blog_id) {
?>
          <div class="bg_standard indented">Instrucciones:
            <ol>
              <li>Agregar Fotos y anotar los ids de cada una.</li>
              <li>Crear un fichero llamado
                <?= $arr_props['source_file']; ?>
                . Salvarlo en /mod/
                <?= $_GET['mod']; ?>
                .</li>
              <li>Por cada foto insertar una l&iacute;nea como esta:<br />
                <span style="font-family:'Courier New', Courier, monospace; font-size:12px;">&lt;?php photo::print_photo_blog(##); ?&gt;</span><br />
                donde ## es el id de la foto.</li>
            </ol>
            <div align="center">
              <input type="button" onclick="JavaScript:save_changes();" value="Guardar Cambios" class="bottonlarge" />&nbsp;&nbsp;&nbsp;
              <input type="button" onclick="JavaScript:add_photos(<?= $blog_id; ?>);" value="Añadir Fotos" class="bottonlarge" />
            </div>
          </div>
          <?php 
}
else {
?>
          <div align="center" class="bg_standard indented">
            <input type="button" onclick="JavaScript:insert_header();" value="Continuar &gt;&gt;" class="bottonlarge" />
          </div>
<?php	
}	

if($blog_id) {
	echo '<div class="title_3">Fotos de este blog</div>';
	$blog->print_gallery_thumbnails();
/*	$blog->print_blog_elements(true);
	
	if($_POST['new_element_type'] == 'text') {
		$blog->print_blog_text_box();
	}*/
}
?>
          <!--         <div class="border_top_dotted indented title_3" align="center">Insertar:&nbsp;&nbsp;

            <input type="hidden" name="remove_element" />
            <input type="hidden" name="move_up" />
            <input type="hidden" name="move_down" />
            <input type="hidden" name="set_photo_cover" />
            <input type="button" onclick="insert_text();" value="Texto / HTML" class="bottonlarge"/>
            &nbsp;&nbsp;
            <input type="button" onclick="insert_photo();" value="Foto" class="bottonlarge"/>
            <br />
            <span class="small_text">Al insertar un nuevo elemento se guardan todos lo cambios de los elementos anteriores</span> </div>-->
          <?php if($blog_id) { ?>
          <div class="border_top_dotted indented title_2 bg_standard" align="center"><a href="JavaScript:preview();"><img src="<?= $conf_images_path; ?>preview.png" border="0" align="absmiddle" width="16" height="16"  /> Vista previa</a></div>
          <?php } ?>
        </form>
      </div></td>
  </tr>
</table>
<a name="end" id="end"></a>
<script language="javascript">
/*
function insert_text() {
	document.form_blog.new_element_type.value = 'text';
	document.form_blog.submit();
}
*/
/*
function insert_photo() {
	document.form_blog.new_element_type.value = 'photo';
	document.form_blog.submit();
}
*/
function add_photos(blog_id) {
	document.form_blog.new_element_type.value = 'photos';
	document.form_blog.submit();
}

function save_changes() {
	document.form_blog.submit();
}

function preview() {
	document.form_blog.new_element_type.value = 'preview';
	document.form_blog.submit();
}

function insert_header() {
	$error = false;
	if(document.form_blog.source_file.value == '') {
		$error = true;
		document.getElementById('cont_source_file').className = 'error_container';
	}
	
	if(document.form_blog.author_name.value == '') {
		$error = true;
		document.getElementById('cont_author_name').className = 'error_container';
	}

	if(document.form_blog.title.value == '') {
		$error = true;
		document.getElementById('cont_title').className = 'error_container';
	}

	if(document.form_blog.summary.value == '') {
		$error = true;
		document.getElementById('cont_summary').className = 'error_container';
	}

	if(document.form_blog.date_from.value == '') {
		$error = true;
		document.getElementById('cont_date_from').className = 'error_container';
	}

	if(document.form_blog.date_to.value == '') {
		$error = true;
		document.getElementById('cont_date_to').className = 'error_container';
	}

	if($error)
		alert('revisa los campos marcados en rojo');
	else
		document.form_blog.submit();
}
/*
function move_up(element_id) {
	document.form_blog.move_up.value = element_id;
	document.form_blog.submit();
}
*/
/*
function move_down(element_id) {
	document.form_blog.move_down.value = element_id;
	document.form_blog.submit();
}
*/
/*
function delete_element(element_id) {
	document.form_blog.remove_element.value = element_id;
	document.form_blog.submit();
}
*/
/*
function set_cover(photo_id) {
	document.form_blog.set_photo_cover.value = photo_id;
	document.form_blog.submit();
}
*/

</script>
