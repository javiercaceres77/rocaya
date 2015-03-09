<?php

if($_POST) {
	$arr_insert = array(//'author_id' => $_POST['user_id']
					    'author_name' => $_POST['name']		 //$ob_user->get_user_name()
					   ,'comment_text' => cleanup_text($_POST['comment'])
					   ,'comment_date' => date('Y-m-d H:i:s')
					   ,'object_type' => 'blog' 			//$_POST['object_type']
					   ,'object_id' => $_GET['detail']
					   ,'control_code' => md5(date('YmdHis')));

	$comment_id = insert_array_db ('comments', $arr_insert, true);

	if($comment_id) {
//		$comment = new comment($comment_id);
		echo '<span class="title_3">Tu comentario se ha registrado correctamente:</span>';
//		$comment->print_comment();
		echo '<div align="center"><a href="'. $_POST['url'] .'">&lt; Volver</a></div>';
	}
	
	unset($_POST);

}

if($_GET['publish']) {
	$blog_id = blog::is_user_editing_blog($_SESSION['Login']['UserID']);
	if($blog_id == $_GET['detail']) {
		$sql = 'UPDATE blog_head SET flag_master = \'0\', status = \'published\' WHERE blog_id = '. $_GET['detail'];
		$upd_blog = my_query($sql, $conex);
		if(!$upd_blog)
			echo '<div class="error_message">Ha habido un error al publicar el blog</div>';
		else {
			# udpate the gallery for this blog and set title and description
			$blog = new blog($blog_id);
			$gallery_id = $blog->get_gallery_id();
			$blog_props = $blog->get_properties();
			
			$update_arr = array('gname' => $blog_props['title']
							  , 'description' => 'Galería de fotos de blog "'. $blog_props['title'] .'"'
							  , 'url_object' => $conf_main_page .'?mod=report&view=det_blog&detail='. $blog->blog_id);
			
			update_array_db('galleries', 'gallery_id', $gallery_id, $update_arr);
		}
	}
}

if(!isset($blog))		$blog = new blog($_GET['detail']);
if(!isset($blog_props))	$blog_props = $blog->get_properties();

?>

<div class="default_text whereami"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>">Blog</a>&nbsp;&gt;&nbsp;
  <?= $blog_props['title']; ?>
</div>
<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top" width="30%"><div class="standard_container default_text"><span class="standard_cont_title">Blog</span><br />
        <?php
if($_GET['preview']) {
	$blog_id = blog::is_user_editing_blog($_SESSION['Login']['UserID']);
	if($blog_id == $_GET['detail']) {
		$preview = true;
		echo '<div align="right"><a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=new_report"><img src="'. $conf_images_path .'edit.gif" border="0" align="absmiddle"> Editar entrada</a></div>';
	}
}

?>
        <div class="bg_standard indented">
          <?= '<strong>'. $blog_props['title'] .'</strong><br>'. $blog_props['summary']; ?>
        </div>
        <br />
        <div class="title_3">Otras entradas</div>
        <div class="standard_bullet_list">
          <ul>
            <?php
	$blog->print_other_blogs_titles();
?>
          </ul>
        </div>
      </div>
      <div class="standard_container default_text"><span class="standard_cont_title">Fotos</span><br />
        <?php
	if(!isset($gallery_id))
		$gallery_id = $blog->get_gallery_id();
	
	$gallery = new gallery($gallery_id);
	$gallery->print_9x9_thumbs();
	
?>
        <span class="title_3"><a href="<?= $conf_main_page .'?mod=photos&view=gallery&detail='. $galler_id; ?>">Ver todas las fotos</a></span></div></td>
    <td valign="top"><div class="standard_container default_text"><span class="standard_cont_title">
        <?= $blog_props['title']; ?>
        <?= $preview ? ' - Vista previa' : ''; ?>
        </span>
        <div class="blog_text">
          <div align="right" style="font-weight:bold;">
            <?php
	echo ucfirst(by) .' ';
	if($conf_exist_user_detail)
		echo '<a href="'. $conf_main_page .'?mod=users&view=det_user&detail='. $blog_props['author_id'] .'">';
	echo $blog_props['author_name'];
	if($conf_exist_user_detail)
		echo '</a>';
	$odate = new date_time($blog_props['date_from']);
	echo ' &ndash; '. $odate->format_date('long');
?>
          </div>
          <!-- AddThis Button BEGIN -->
          <div class="addthis_toolbox addthis_default_style "> <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a> <a class="addthis_button_tweet"></a> <a class="addthis_counter addthis_pill_style"></a> </div>
          <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4d8a4fb1438bc4f2"></script>
          <!-- AddThis Button END -->
          <?php

	include $conf_mods_path . $_GET['mod'] .'/'. $blog_props['source_file'];
//	$blog->print_blog_elements();         

		 ?>
        </div>
        <?php    if($preview) {	?>
        <div class="border_top_dotted indented title_3" align="center">
          <?= '<a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=new_report"><img src="'. $conf_images_path .'edit.gif" border="0" align="absmiddle"> Editar entrada</a>'; ?>
          <br />
          <br />
          <input type="button" onclick="JavaScript:publish();" value="Publicar" class="bottonlarge"/>
        </div>
        <script language="javascript">
	function publish() {
		if(confirm('Una vez publicado el blog no se puede cambiar, pulsa "Aceptar" para publicarlo'))
			document.location = document.location + '&publish=1';
	}
</script>

        <?php	}
		else {	
/*
		?>

        <div class="title_3">Comentarios</div>
        <?php
			$blog->print_blog_comments();
			
			if($_SESSION['Login']['UserID'] != $conf_generic_user_id)
				$blog->print_blog_comment_box();
			else
				echo '<a href="'. $conf_main_page .'?mod=home&view=new_user">Regístrate</a> para escribir comentarios<br />';

?>
        <div class="standard_container">
          <div class="title_3">Escribir un comentario:</div>
          <form name="comm_blog" id="comm_blog" action="" method="post">
            <table border="0" cellpadding="2" cellspacing="2">
              <tr>
                <td colspan="2"> Nombre:
                  <input type="text" class="inputlarge" maxlength="100" name="name" id="name" /></td>
              </tr>
              <tr>
                <td colspan="2">Escribe el resultado de la siguiente operaci&oacute;n en n&uacute;mero:</td>
              </tr>
              <tr>
                <td><div id="captcha_container" align="right"></div></td>
                <td><input type="text" class="inputlarge" name="reg_captcha" id="reg_captcha" style="width:80px;" maxlength="4" />
                  <a href="JavaScript:reload_captcha();"><img src="<?= $conf_images_path; ?>reload.png" alt="Recargar" title="Recargar" width="16" height="16" border="0" align="absmiddle" /></a></td>
              </tr>
              <tr>
                <td colspan="2"><textarea name="comment" id="comment" cols="45" class="inputlarge" style="width:450px;"></textarea>
                  <br />
                  <span class="small_text">No se permite HTML u otras etiquetas</span></td>
              </tr>
              <tr>
                <td colspan="2" align="center"><br />
                  <input type="button" name="send" id="send" value="   Enviar   "  class="inputnewnowidth" onclick="JavaScript:send_comm();"/>
                </td>
              </tr>
            </table>
          </form>
        </div>
        <?php
	*/	}
?>
        <br />
      </div></td>
  </tr>
</table>
<script language="javascript" src="inc/ajax.js"></script>
<script language="javascript">

document.onload = reload_captcha();

function reload_captcha() {
	url = 'inc/ajax.php?content=captcha';
	getData(url, 'captcha_container');
}

function send_comm() {
	with(document.comm_blog) {
		error = '';
		if(name.value == '')
			error = 'Escribe tu nombre\n';
		if(reg_captcha.value == '')
			error+= 'Escribe el resultado de la operación\n';
		if(comment.value == '') 
			error+= 'Escribe un comentario\n';
			
		if(error == '')
			submit();
		else
			alert(error);
	}
}

</script>
