<div class="standard_container default_text">
<span class="standard_cont_title"> Comentario grabado </span>
<table border="0" width="66%" align="center">
  <tr>
    <td><?php

if($_SESSION['Login']['UserID'] != $conf_generic_user_id) {
	switch($_POST['object_type']) {
		case 'photo':
			$photo = new photo($_POST['object_id']);
			$object_code = $photo->get_photo_code();
		break;
		case 'blog':
			$blog = new blog($_POST['object_id']);
			$object_code = $blog->get_blog_code();
		break;
		case 'crag':
			$crag = new crag($_POST['object_id']);
			$object_code = $crag->get_crag_code();
		break;
		case 'sector':
			$sector = new sector($_POST['object_id']);
			$object_code = $sector->get_sector_code();
		break;
	}
	
		
	$error = false;
	if($_SESSION['Login']['UserID'] != $_POST['user_id'] || $object_code != $_POST['control_code'])
		$error = true;
	
	if(!$error) {
		$ob_user = new user($_POST['user_id']);
			
		$arr_insert = array('author_id' => $_POST['user_id']
						   ,'author_name' => $ob_user->get_user_name()
						   ,'comment_text' => cleanup_text($_POST['comment'])
						   ,'comment_date' => date('Y-m-d H:i:s')
						   ,'object_type' => $_POST['object_type']
						   ,'object_id' => $_POST['object_id']
						   ,'control_code' => md5(date('YmdHis')));

		$comment_id = insert_array_db ('comments', $arr_insert, true);

		if($comment_id) {
			$comment = new comment($comment_id);
			echo '<span class="title_3">Tu comentario se ha registrado correctamente:</span>';
			$comment->print_comment();
			echo '<div align="center"><a href="'. $_POST['url'] .'">&lt; Volver</a></div>';
		}
	}
	else
		echo 'ha habido un error al insertar el comentario';
		
} 	// if($_SESSION['Login']['UserID'] != $conf_generic_user_id) { ?></td>
  </tr>
</table>
