<?php

class blog {
	public $blog_id;

	private $properties;
	private $control_code;
	private $num_elements;
	private $gallery_id;
	
	public function __construct($blog_id) {
		$this->blog_id = $blog_id;
		$this->set_blog_code();
	}

////////////////////////////////////////////////

	public function get_blog_code() {
		return $this->control_code;
	}
	
	public function get_properties() {
		if(!count($this->properties))
			$this->set_properties();

		return $this->properties;
	}
	
/*	public function get_num_elements() {
		if(!isset($this->num_elements))
			$this->set_num_elements();
		return $this->num_elements;
	}*/
	
	public function get_gallery_id() {
		if(!isset($this->gallery_id))
			$this->set_gallery_id();
		
		return $this->gallery_id;
	}

////////////////////////////////////////////////

	private function set_gallery_id() {
		$this->gallery_id = gallery::get_gallery_id($this->blog_id, 'blog');
	}

	private function set_properties() {
		$arr_props = simple_select('blog_head', 'blog_id', $this->blog_id, array('author_id', 'author_name', 'date_from', 'date_to', 'title', 'summary', 'key_words', 'photo_cover_id', 'source_file'));
		$this->properties = $arr_props;
	}
	
/*	private function set_num_elements() {
		global $conex;
		$sql = 'SELECT count(*) AS num_elements FROM blog_element WHERE blog_id = \''. $this->blog_id .'\'';
		$select_num = my_query($sql, $conex);
		$arr_res = my_fetch_array($select_num);
		$this->num_elements = $arr_res['num_elements'];
	}*/
	
	private function set_blog_code($code = '') {
		if($code == '') {
			$arr_code = simple_select('blog_head', 'blog_id', $this->blog_id, 'control_code'); //, ' AND flag_master <> \'1\'');
			$this->control_code = $arr_code['control_code'];
		}
		else
			$this->control_code = $code;
	}

	public function set_photo_cover($photo_id) {
		$this->update_header(array('photo_cover_id' => $photo_id));
	}

////////////////////////////////////////////////	

	public function print_blog_comments() {
		comment::print_object_comments($this->blog_id, 'blog');
	}

	public function print_blog_comment_box() {
		if(!isset($this->control_code))
			$this->set_blog_code();
			
		comment::print_object_comment_box($this->blog_id, 'blog', $this->control_code);
	}
	
	public function get_num_comments() {
		return comment::get_object_num_comments($this->blog_id, 'blog');
	}
	
	public function print_blog_elements($editable = false) {
		# include the source file
/*		global $conex;
		$sql = 'SELECT element_id, element_order, element_type, content, photo_id FROM blog_element WHERE blog_id = \''. $this->blog_id .'\' ORDER BY element_order';
//		echo $sql;
		$select_elements = my_query($sql, $conex);
		while($record = my_fetch_array($select_elements)) {
			if($record['element_type'] == 'text') {
				$text_element = new blog_text($record);
				$text_element->print_element($editable);
			}
			
			if($record['element_type'] == 'photo') {
				$photo_element = new blog_photo($record);
				$photo_element->print_element($editable);
			}
		}*/
	}
	
/*	public function print_blog_text_box() {
		$this->set_num_elements();
?>
<div class="bg_standard title_3"><?= $this->num_elements + 1; ?> - Texto</div>
<table border="0" cellpadding="2" cellspacing="2">
  <tr>
    <td valign="top"><textarea name="new_text_element" class="inputlarge" style="width:500px;" rows="4"></textarea></td>
  </tr>
</table>
<?php	
	}*/
	
	public function print_gallery_thumbnails() {
		$gallery = new gallery($this->get_gallery_id());
		
		$gallery->print_idd_thumbnails();
	}
	
	public function print_other_blogs_titles() {
		global $conex, $conf_main_page;
		$sql = 'SELECT url_id, blog_id, title, date_from FROM blog_head WHERE flag_master <> \'1\' ORDER BY date_from DESC limit 20';
		$select_blogs = my_query($sql, $conex);
		while($record = my_fetch_array($select_blogs)){
			if($record['blog_id'] == $this->blog_id)
				echo '<li><strong>'. $record['title'] .'</strong></li>';
			else {
				echo '<li><a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view='. $_GET['view'] .'&detail='. $record['blog_id'] .'id='. $record['url_id'] .'">'. $record['title'] .'</a></li>';
			}
		}
	}	

////////////////////////////////////////////////	

	public function update_header($arr_contents) {
		if(!update_array_db('blog_head', 'blog_id', $this->blog_id, $arr_contents))
			echo 'error al actualizar la tabla';
	}
	
/*	public function update_txt_element($element_id, $content) {
		$element = new blog_text($element_id);
		$element->update_content($content);
	}*/
	
/*	public function insert_text($text) {
		$this->set_num_elements();

		$ins_array = array('element_order' => $this->num_elements + 1
						  ,'blog_id' => $this->blog_id
						  ,'element_type' => 'text'
						  ,'content' => $text);
		
		insert_array_db('blog_element', $ins_array);
	}*/
	
/*	public function insert_photo($photo_id, $description = '') {
		$this->set_num_elements();
		
		$ins_array = array('element_order' => $this->num_elements + 1
						  ,'blog_id' => $this->blog_id
						  ,'element_type' => 'photo'
						  ,'content' => $description
						  ,'photo_id' => $photo_id);
		
		insert_array_db('blog_element', $ins_array);
	}*/
	
/*	public function remove_element($element_id) {
		# breaking encapsulation here ... too bad.
		return exists_record('blog_element', 'element_id', $element_id, true);		
	}*/
	
/*	public function exchange_elements($el_id1, $el_id2) {
		# breaking encapsulation here ... too bad.
		update_array_db('blog_element', 'element_id', $el_id2, array('element_order' => $el_id1));
		update_array_db('blog_element', 'element_id', $el_id1, array('element_order' => $el_id2));
	}*/

////////////////////////////////////////////////	

	static function is_user_editing_blog($user_id) {
		$arr_blog = simple_select('blog_head', 'author_id', $user_id, 'blog_id', ' AND status = \'editing\'');
		return $arr_blog['blog_id'];
	}
}

/*class blog_text extends blog {
	public $element_id;
	private $order;
	private $content;
	
	public function __construct($properties) {
		if(!is_array($properties)) $properties = array('element_id' => $properties);
		$this->element_id = $properties['element_id'];
		
		if($properties['element_order'])
			$this->order = $properties['element_order'];
		else
			$this->set_element_order();
		
		if($properties['content'])
			$this->content = $properties['content'];
		else
			$this->set_element_content();
	}	
	
	private function set_element_order() {
		$arr_order = simple_select('blog_element', 'element_id', $this->element_id, 'element_order');
		$this->order = $arr_order['element_order'];
	}
	
	private function set_element_content() {
		$arr_content = simple_select('blog_element', 'element_id', $this->element_id, 'content');
		$this->content = $arr_content['content'];
	}
	
	public function print_element($editable = false) {
		global $conf_images_path;
		if($editable) {
?>
<div class="bg_standard title_3"><?= $this->order; ?> - Texto</div>
<table border="0" cellpadding="2" cellspacing="2">
  <tr>
    <td valign="top"><textarea name="txt_<?= $this->element_id; ?>" class="inputdisc blog_text" style="width:500px;" rows="3"><?= $this->content; ?>
</textarea></td>
    <td valign="top"><?php		if($this->order > 1) {		?><a href="JavaScript:move_up('<?= $this->element_id; ?>');" title="subir elemento"><img src="<?= $conf_images_path; ?>up.png" border="0" width="16" height="16"></a><br><?php	}	?>
      <a href="JavaScript:delete_element('<?= $this->element_id; ?>');" title="eliminar elemento"><img src="<?= $conf_images_path; ?>delete.png" border="0" width="16" height="16"></a><br>
      <?php		//if($this->get_num_elments() != $this->order) {	?><a href="JavaScript:move_down('<?= $this->element_id; ?>');" title="bajar elemento"><img src="<?= $conf_images_path; ?>down.png" border="0" width="16" height="16"></a></td><?php	//}	?>
  </tr>
</table>
<?php		
		}
		else {
			echo '<div id="'. $this->element_id .'" class="blog_text">'. htmlspecialchars_decode($this->content) .'</div>';
		}	
	}
	
	public function update_content($content) {
		# check if it has changed
		if($content != $this->content) {
			$upd_array = array('content' => $content);
			update_array_db('blog_element', 'element_id', $this->element_id, $upd_array);
		}
	}
	
}*/

/*class blog_photo extends blog {
	public $element_id;
	private $order;
	private $photo;
	
	public function __construct($properties) {
		if(!is_array($properties)) $properties = array('element_id' => $properties);
		$this->element_id = $properties['element_id'];
		
		if($properties['element_order'])
			$this->order = $properties['element_order'];
		else
			$this->set_element_order();
		
		if($properties['photo_id'])
			$this->photo = new photo($properties['photo_id']);
		else
			$this->set_element_photo();
	}	
	
	private function set_element_order() {
		$arr_order = simple_select('blog_element', 'element_id', $this->element_id, 'element_order');
		$this->order = $arr_order['element_order'];
	}
	
	private function set_element_photo() {
		$arr_photo_id = simple_select('blog_element', 'element_id', $this->element_id, 'photo_id');
		$this->photo = new photo($arr_photo_id['photo_id']);
	}
		
	public function print_element($editable = false) {
		global $conf_images_path;
		if($editable) {
?>
<div class="bg_standard title_3"><?= $this->order; ?> - Foto</div>
<table border="0" cellpadding="2" cellspacing="2">
  <tr>
    <td valign="top"><?php $this->photo->print_photo_blog();  ?></td>
    <td valign="top"><a href="JavaScript:move_up('<?= $this->element_id; ?>');" title="subir elemento"><img src="<?= $conf_images_path; ?>up.png" border="0" width="16" height="16"></a><br>
      <a href="JavaScript:delete_element('<?= $this->element_id; ?>');" title="eliminar elemento"><img src="<?= $conf_images_path; ?>delete.png" border="0" width="16" height="16"></a><br>
      <a href="JavaScript:move_down('<?= $this->element_id; ?>');" title="bajar elemento"><img src="<?= $conf_images_path; ?>down.png" border="0" width="16" height="16"></a><br />
      <a href="JavaScript:set_cover('<?= $this->photo->photo_id; ?>');" title="Establecer como foto de portada"><img src="<?= $conf_images_path; ?>image.gif" width="16" height="16" /></a></td>
  </tr>
</table>
<?php		
		}
		else {
			$this->photo->print_photo_blog();
		}	
	}
}*/

?>
