<?php

class gallery{
	private $gallery_id;
	private $gname;
	private $description;
	private $object_id;
	private $object_type;
	private $cover_photo_id;

	public function __construct($gallery_id) {
		$this->gallery_id = $gallery_id;
		if(!isset($this->gname)) {
			$arr_gallery = simple_select('galleries', 'gallery_id', $this->gallery_id, array('gname', 'description', 'object_id', 'object_type', 'cover_photo_id'));
			
			$this->gname = $arr_gallery['gname'];
			$this->description = $arr_gallery['description'];
			$this->object_id = $arr_gallery['object_id'];
			$this->object_type = $arr_gallery['object_type'];
			$this->cover_photo_id = $arr_gallery['cover_photo_id'];
		}
	}
/*	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}*/
	
	public function print_9x9_thumbs($limit = 9) {
		# prints table with thumbs 9x9
		global $conex, $conf_main_page, $conf_images_path, $conf_photos_subpath;
		$sql = 'SELECT p.photo_id, p.thumb_file_name, p.thumb_w, p.thumb_h, p.title 
		FROM photos p INNER JOIN photo_gallery pg 
		ON pg.photo_id = p.photo_id 
		WHERE pg.gallery_id = '. $this->gallery_id .' AND p.flag_master <> \'1\'
		LIMIT '. $limit;

		$select_photos = my_query($sql, $conex);
		$num_photos = my_num_rows($select_photos);
		$count = 0;
		
		if($num_photos) {
			echo '<table width="100%" border="0" align="center" cellpadding="3" cellspacing="2"><tr>';

			while($record = my_fetch_array($select_photos)) {
				$count++;
				echo '<td align="center"><a href="'. $conf_main_page .'?mod=photos&view=detail_photo&detail='. $record['photo_id'] .'&g_id='. $this->gallery_id .'"><img class="thin_border_picture" border="0" src="'. $conf_images_path . $conf_photos_subpath . $record['thumb_file_name'] .'" width="'. $record['thumb_w'] .'" height="'. $record['thumb_h'] .'" title="'. $record['title'] .'" /></a></td>';
				if($count%3 == 0)
					echo '</tr><tr>';
			}
			echo '</tr></table>';
		}
	}
	
	public function print_idd_thumbnails() {
		# prints all the gallery's thumbnails displaying the ids
		global $conex, $conf_main_page, $conf_images_path, $conf_photos_subpath;
		$sql = 'SELECT p.photo_id, p.thumb_file_name, p.thumb_w, p.thumb_h, p.title 
		FROM photos p INNER JOIN photo_gallery pg 
		ON pg.photo_id = p.photo_id 
		WHERE pg.gallery_id = '. $this->gallery_id .' AND p.flag_master <> \'1\'';
		
		$select_photos = my_query($sql, $conex);
		$num_photos = my_num_rows($select_photos);
		
		if($num_photos) {
			while($record = my_fetch_array($select_photos)) {
				echo '<div style="display:inline-block; margin:10px;" class="small_text"><a href="'. $conf_main_page .'?mod=photos&view=detail_photo&detail='. $record['photo_id'] .'&g_id='. $this->gallery_id .'"><img class="thin_border_picture" border="0" src="'. $conf_images_path . $conf_photos_subpath . $record['thumb_file_name'] .'" width="'. $record['thumb_w'] .'" height="'. $record['thumb_h'] .'" title="'. $record['title'] .'" /></a><br />photo_id: '. $record['photo_id'] .'</div>';
			}
		}
	}
	
	public function get_type() {
		return $this->object_type;
	}
	
	static function create_gallery($parameters) {
		return insert_array_db('galleries', $parameters, true);
	}
	
	static function get_gallery_id($object_id, $object_type) {
		global $conex;
		$sql = 'SELECT gallery_id FROM galleries WHERE object_id = \''. $object_id .'\' AND object_type = \''. $object_type .'\' AND is_public = \'1\'';
		$select_gid = my_query($sql, $conex);
		$arr_gallery = my_fetch_array($select_gid);

		return $arr_gallery['gallery_id'];
	}
}

class photo {
	public $photo_id;
	private $control_code;
	private $properties = array();
	private $owner;
	private $galleries = array();
	
	public function __construct($photo_id) {
		$this->photo_id = $photo_id;
	}

	private function set_photo_properties() {
		$props = array('file_name', 'width', 'height', 'med_file_name', 'med_h', 'med_w', 'small_file_name', 'small_h', 'small_w', 'thumb_file_name', 'thumb_w', 'thumb_h', 'type', 'title', 'description', 'owner_id', 'owner_name', 'author_name', 'climber_name', 'date_taken', 'date_uploaded', 'is_public', 'object_id', 'object_type', 'route_id', 'sector_id', 'crag_id', 'rating');
		$arr_photo = simple_select('photos', 'photo_id', $this->photo_id, $props, ' AND flag_master <> \'1\'');
		$this->properties = $arr_photo;
	}
	
	private function set_photo_galleries() {
		global $conex;
		$sql = 'SELECT g.gallery_id	FROM galleries g INNER JOIN photo_gallery pg ON pg.gallery_id = g.gallery_id WHERE pg.photo_id = \''. $this->photo_id .'\' AND g.is_public = \'1\'';
		$select_galleries = my_query($sql, $conex);
		
		while($record = my_fetch_array($select_galleries)) {
			$this->galleries[$record['gallery_id']] = new gallery($record['gallery_id']);
/*			$this->galleries[$record['gallery_id']]->gname = $record['gname'];
			$this->galleries[$record['gallery_id']]->description = $record['description'];
			$this->galleries[$record['gallery_id']]->object_id = $record['object_id'];
			$this->galleries[$record['gallery_id']]->object_type = $record['object_type'];
			$this->galleries[$record['gallery_id']]->cover_photo_id = $record['cover_photo_id'];*/
		}
	}
	
	private function set_photo_code($code = '') {
		if($code == '') {
			$arr_code = simple_select('photos', 'photo_id', $this->photo_id, 'control_code', ' AND flag_master <> \'1\'');
			$this->control_code = $arr_code['control_code'];
		}
		else
			$this->control_code = $code;
	}
	
	private function set_photo_owner($user_id) {
		if(!isset($this->owner))
			$this->owner = new user($user_id);
	}

	private function get_photo_galleries_type() {
		# of all the galleries, select a type according to some criteria
		# returns the gallery_id of the proper type
		if(!count($this->galleries))
			$this->set_photo_galleries();
		
		$types = array();
		foreach($this->galleries as $g_id => $ob_gallery) {
			$types[$this->galleries[$g_id]->get_type()] = $g_id;
		}

		# if we are in the routes 
		if(count($types)) {
			if($_GET['mod']) {
				switch($_GET['mod']) {
					case 'routes':
						if($types['crag']) return $types['crag'];
					break;
					case 'report':
						if($types['blog']) return $types['blog'];
					break;
					case 'home':
						if($types['crag']) return $types['crag'];
						if($types['blog']) return $types['blog'];
					break;
					default:
						return $types['user'];
				}
			}
			else {	# return the first type whatever it is
				foreach ($types as $type => $gallery_id) {
					return $gallery_id;
				}
			}
		}
	}
	
	public function get_photo_code() {
		if(!isset($this->control_code))
			$this->set_photo_code();
		return $this->control_code;
	}
	
	public function get_photo_num_comments() {
		global $conex;
		$sql = 'SELECT count(*) as num_comments FROM comments WHERE object_id = \''. $this->photo_id .'\' AND object_type = \'photo\' AND flag_master <> \'1\'';
		$select_comments = my_query($sql, $conex);
		$num_comments = my_fetch_array($select_comments);
		return $num_comments['num_comments'];
	}
	
	public function add_photo_2_gallery($gallery_id) {
		$arr_insert = array('photo_id' => $this->photo_id, 'gallery_id' => $gallery_id);

		insert_array_db('photo_gallery', $arr_insert);
	}
	
	public function delete_object()	{
		# delete photo
	}
	
	public function print_small_photo($details = true) {
		$this->print_photo('small', $details); 
/*		global $conf_main_page, $conf_photos_subpath, $conf_images_path;
		if(!count($this->properties))		
			$this->set_photo_properties();
	
		$this->set_photo_owner($this->properties['owner_id']);
		if(!count($this->galleries))
			$this->set_photo_galleries();

		#print the photo
		echo '<a href="'. $conf_main_page .'?mod=photos&view=detail_photo&detail='. $this->photo_id .'&g_id='. $this->get_photo_galleries_type() .'">';
		echo '<img class="thin_border_picture" border="0" src="'. $conf_images_path . $conf_photos_subpath . $this->properties['small_file_name'] .'" width="'. $this->properties['small_w'] .'" height="'. $this->properties['small_h'] .'" title="'. $this->properties['title'] .'" />';
		echo '</a><br />';
		
		if($details) {
			$this->print_photo_rate_box();
			$this->print_photo_description('short');
		}*/
	}
	
	public function print_photo($format, $details = true) {
		global $conf_main_page, $conf_photos_subpath, $conf_images_path;
		if(!count($this->properties))		
			$this->set_photo_properties();
	
		$this->set_photo_owner($this->properties['owner_id']);
		if(!count($this->galleries))
			$this->set_photo_galleries();

		switch($format) {
			case 'thumb': 
				$photo_file = $this->properties['thumb_file_name']; // ? $this->properties['med_file_name'] : $this->properties['small_file_name'];
				$photo_w = $this->properties['thumb_w']; // ? $this->properties['med_w'] : $this->properties['small_w'];
				$photo_h = $this->properties['thumb_h']; // ? $this->properties['med_h'] : $this->properties['small_h'];
			break;
			case 'med':
				$photo_file = $this->properties['med_file_name'] ? $this->properties['med_file_name'] : $this->properties['small_file_name'];
				$photo_w = $this->properties['med_w'] ? $this->properties['med_w'] : $this->properties['small_w'];
				$photo_h = $this->properties['med_h'] ? $this->properties['med_h'] : $this->properties['small_h'];
			break;
			case 'small': default:
				$photo_file = $this->properties['small_file_name'];
				$photo_w = $this->properties['small_w'];
				$photo_h = $this->properties['small_h'];
			break;
		}
		#print the photo
		echo '<a href="'. $conf_main_page .'?mod=photos&view=detail_photo&detail='. $this->photo_id .'&g_id='. $this->get_photo_galleries_type() .'">';
		echo '<img class="thin_border_picture" border="0" src="'. $conf_images_path . $conf_photos_subpath . $photo_file .'" width="'. $photo_w .'" height="'. $photo_h .'" title="'. $this->properties['title'] .'" />';
		echo '</a><br />';
		
		if($details) {
			$this->print_photo_rate_box();
			$this->print_photo_description('short');
		}
	}
	
	static function print_photo_blog($photo_id) {
		$photo = new photo($photo_id);
		echo '<table border="0" align="center" cellpadding="6" cellspacing="0"><tr><td align="center">';
		$photo->print_photo('med', false);
		echo '<span class="blog_footer">'. $photo->properties['title']	.'</span>';
		echo '</td></tr></table>';
	}

	public function print_thumb() {
		global $conf_main_page, $conf_photos_subpath, $conf_images_path;
		if(!count($this->properties))		
			$this->set_photo_properties();

		echo '<a href="'. $conf_main_page .'?mod=photos&view=detail_photo&detail='. $this->photo_id .'&g_id='. $this->get_photo_galleries_type() .'">';
		echo '<img class="thin_border_picture" border="0" src="'. $conf_images_path . $conf_photos_subpath . $this->properties['thumb_file_name'] .'" width="'. $this->properties['thumb_w'] .'" height="'. $this->properties['thumb_h'] .'" title="'. $this->properties['title'] .'" />';
		echo '</a>';
	}
	
	public function print_photo_comments() {
		comment::print_object_comments($this->photo_id, 'photo');
	}
	
	public function print_photo_comment_box() {
		if(!isset($this->control_code))
			$this->set_photo_code();
			
		comment::print_object_comment_box($this->photo_id, 'photo', $this->control_code);
	}
	
	public function print_photo_route_crag() {
		global $conex, $conf_main_page;
		if(!count($this->properties))		
			$this->set_photo_properties();
			
		$rname = ''; $sname = ''; $cname = ''; $pname = '';
		if($this->properties['route_id']) {
			$sql = 'SELECT r.rname, s.sname, c.cname, p.pname
			FROM routes r
			INNER JOIN sectors s ON r.sector_id = s.sector_id
			INNER JOIN crags c ON s.crag_id = c.crag_id
			INNER JOIN provinces p ON c.prov_id = p.prov_id
			WHERE r.route_id = \''. $this->properties['route_id'] .'\'';
		}
		elseif($this->properties['sector_id']) {
			$sql = 'SELECT s.sname, c.cname, p.pname
			FROM sectors s 
			INNER JOIN crags c ON s.crag_id = c.crag_id
			INNER JOIN provinces p ON c.prov_id = p.prov_id
			WHERE s.sector_id = \''. $this->properties['sector_id'] .'\'';
		}
		elseif($this->properties['crag_id']) {
			$sql = 'SELECT c.cname, p.pname
			FROM crags c 
			INNER JOIN provinces p ON c.prov_id = p.prov_id
			WHERE c.crag_id = \''. $this->properties['crag_id'] .'\'';
		}
		else
			return '';
			
		$select_rsc = my_query($sql, $conex);
		$arr_routes = my_fetch_array($select_rsc);
		
		if($arr_routes['rname']) {
			echo 'Vía: <a href="'. $conf_main_page .'?mod=routes&view=detail_sector&detail='. $this->properties['sector_id'] .'">'. $arr_routes['rname'] .'</a>';
			echo ' (<a href="'. $conf_main_page .'?mod=routes&view=detail_crag&detail='. $this->properties['crag_id'] .'">'. $arr_routes['cname'] .'</a>)';
		}
		elseif($arr_routes['sname']) {
			echo 'Sector: <a href="'. $conf_main_page .'?mod=routes&view=detail_sector&detail='. $this->properties['sector_id'] .'">'. $arr_routes['sname'] .'</a>';
			echo ' (<a href="'. $conf_main_page .'?mod=routes&view=detail_crag&detail='. $this->properties['crag_id'] .'">'. $arr_routes['cname'] .'</a>)';
		}
		elseif($arr_routes['cname']) {
			echo 'Escuela: <a href="'. $conf_main_page .'?mod=routes&view=detail_crag&detail='. $this->properties['crag_id'] .'">'. $arr_routes['cname'] .'</a>';
			echo ' ('. $arr_routes['pname'] .')';
		}
		else
			return '';
		
		echo '<br>';
	}
	
	public function print_photo_description($mode = 'full') {
		global $conex, $conf_main_page;
		if(!count($this->properties))		
			$this->set_photo_properties();

		//$this->set_photo_owner($this->properties['owner_id']);
		$lan = substr($_SESSION['misc']['lang'], 0, 2);
		$photo_date = new date_time($this->properties['date_taken'], $lan);	
		$num_comments = $this->get_photo_num_comments();

		# mode: full, short, med?
		switch($mode) {
			case 'full':
				
			break;
			case 'short':
				echo $this->properties['title'] ? $this->properties['title'] : '';	echo '<br>';
				echo $this->properties['author_name'] ? 'Foto tomada por '. $this->properties['author_name'] : '';	echo '<br>';
				echo $this->properties['date_taken']!='0000-00-00' ? $photo_date->format_date('long') : '';	echo '<br>';
				$this->print_photo_route_crag();
				$plural = $num_comments>1?'s':'';
				echo $num_comments?'<a href="'. $conf_main_page .'?mod=photos&view=detail_photo&detail='. $this->photo_id .'">'. $num_comments .' comentario'. $plural .'</a>':'';
				echo '<br>';
			break;
		}
	}
	
	public function print_photo_rate_box() {
		if(!count($this->properties))		
			$this->set_photo_properties();

		# get a random value to identify the photo rate box univocally
		$rand_value = rand(1, 1000);

		echo '<div id="rate_container_'. $this->photo_id .'-'. $rand_value .'" class="default_text" align="right">'; 
		draw_rate_box($this->photo_id, $rand_value, $this->properties['rating']);
		echo '</div>';
	}
}		//class photo {

class comment {
	private $comment_id;
	private $text;
	private $date_time;
	private $author;
	private $control_code;

	private function set_comment_text($text = '') {
		if($text == '') {
			$arr_txt = simple_select('comments', 'comment_id', $this->comment_id, 'comment_text', ' AND flag_master <> \'1\'');
			$this->text = $arr_txt['comment_text'];
		}
		else
			$this->text = $text;
	}
	
	private function set_comment_date($date = '') {
		if($date == '') {
			$arr_date = simple_select('comments', 'comment_id', $this->comment_id, 'comment_date', ' AND flag_master <> \'1\'');
			$this->date_time = new date_time($arr_date['comment_date']); //$arr_date['comment_date'];
		}
		else
			$this->date_time = new date_time($date);
	}
	
	private function set_comment_code($code = '') {
		if($code == '') {
			$arr_code = simple_select('comments', 'comment_id', $this->comment_id, 'control_code', ' AND flag_master <> \'1\'');
			$this->control_code = $arr_code['control_code'];
		}
		else
			$this->control_code = $code;
	}

	private function set_comment_author($author_id = '') {
		if($author == '') {
			$arr_auth = simple_select('comments', 'comment_id', $this->comment_id, 'author_id', ' AND flag_master <> \'1\'');
			$this->author = new user($arr_auth['author_id']);
		}
		else
			$this->author = new user($author_id);
	}
		
	public function delete_object()	{
		# delete comment	
	}

	public function get_object_owner() {
		# comment owner's id
	}
	
	public function __construct($comment_id) {
		$this->comment_id = $comment_id;
	}
	
	public function print_comment() {
		global $conf_images_path, $conf_main_page, $conf_generic_user_id;
		if(!isset($this->text))	$this->set_comment_text();
		if(!isset($this->date_time)) $this->set_comment_date();
		if(!isset($this->author)) $this->set_comment_author();
				
		if(substr($this->date_time->get_date(), 0, 4) != date('Y'))
			$my_year = ' '. substr($temp_date, 0, 4);
	
		echo '<div style="width:66%;" class="bg_standard title_3">'. $this->author->get_user_name() .' - '. $this->date_time->format_date('month_day') . $my_year .', '. $this->date_time->format_time() .'</div>';
		echo '<div style="width:66%;" class="comment">'. $this->text .'</div>';
		if($this->author->user_id != $_SESSION['Login']['UserID'] && $_SESSION['Login']['UserID'] != $conf_generic_user_id)
			echo '<div style="width:66%; padding-bottom:15px;" align="right"><a href="'. $conf_main_page .'?mod=home&view=report_inapp&object_id='. $this->comment_id .'&object_type=comment&code='. $this->control_code .'"><img border="0" src="'. $conf_images_path .'alert.png" width="16" height="16" title="Reportar contenido inapropiado"></a></div>';
	}
	
	private function clean_text() {
		# remove not allowed characters and tags.
		
	}
	
	public function print_summary() {
	
	}

	static function get_object_num_comments($object_id, $object_type) {
		global $conex;
		$sql = 'SELECT count(comment_id) as num_comments FROM comments WHERE object_type = \''. $object_type .'\' AND object_id = \''. $object_id .'\' AND flag_master <> \'1\'';

		$sel_ncs = my_query($sql, $conex);
		return my_result($sel_ncs, 0, 'num_comments');
	}
	
	static function print_object_comments($object_id, $object_type) {
		global $conex;
		
		$sql = 'SELECT comment_id, comment_text, comment_date, author_id, author_name, control_code FROM comments WHERE object_type = \''. $object_type .'\' AND object_id = \''. $object_id .'\' AND flag_master <> \'1\'';
		$select_comments = my_query($sql, $conex);
		while($record = my_fetch_array($select_comments)) {
			$comm_obj = new comment($record['comment_id']);
			
			$comm_obj->set_comment_text($record['comment_text']);
			$comm_obj->set_comment_code($record['control_code']);

			$comm_obj->date_time = new date_time($record['comment_date']);
			$comm_obj->author = new user($record['author_id'], $record['author_name']);
			
			$comm_obj->print_comment();
		}
	}
	
	static function print_object_comment_box($object_id, $object_type, $object_code = '') {
?>
<span class="title_3">Escribe un comentario</span>
<form name="form_<?= $object_type .'_'. $object_id; ?>" id="form_<?= $object_type .'_'. $object_id; ?>" action="<?= $conf_main_page; ?>?mod=home&view=ins_comm" method="post">
  <input type="hidden" name="object_type" value="<?= $object_type; ?>" />
  <input type="hidden" name="object_id" value="<?= $object_id; ?>" />
  <input type="hidden" name="control_code" value="<?= $object_code; ?>" />
  <input type="hidden" name="user_id" value="<?= $_SESSION['Login']['UserID']; # just for check afterwards ?>" />
  <input type="hidden" name="url" value="" />
  <textarea name="comment" id="comment" cols="45" class="inputlarge" style="width:450px;"></textarea><br />
<span class="small_text">No se permite HTML u otras etiquetas</span><br />
  <br /><input type="button" name="send" id="send" value="   Enviar   "  class="inputnewnowidth" onclick="JavaScript:set_url_field();"/>
</form><br />
<script language="javascript">
function set_url_field() {
	my_form = document.form_<?= $object_type .'_'. $object_id; ?>;
	if(my_form.comment.value != '') {
		my_form.url.value = document.location;
		my_form.submit();
	}
	else
		alert('¡Escribe algo!');
}
</script>
<?php		
	}
}

class crag {
	private $crag_id;
	private $control_code;
	
	public function __construct($crag_id) {
		$this->crag_id = $crag_id;
		$this->set_crag_code();
	}
	
	public function get_crag_code() {
		return $this->control_code;
	}
	
	private function set_crag_code($code = '') {
		if($code == '') {
			$arr_code = simple_select('crags', 'crag_id', $this->crag_id, 'control_code'); //, ' AND flag_master <> \'1\'');
			$this->control_code = $arr_code['control_code'];
		}
		else
			$this->control_code = $code;
	}
	
	public function print_crag_comments() {
		comment::print_object_comments($this->crag_id, 'crag');
	}

	public function print_crag_comment_box() {
		if(!isset($this->control_code))
			$this->set_crag_code();
			
		comment::print_object_comment_box($this->crag_id, 'crag', $this->control_code);
	}
}

class sector {
	private $sector_id;
	private $control_code;
	
	public function __construct($sector_id) {
		$this->sector_id = $sector_id;
		$this->set_sector_code();
	}
	
	public function get_sector_code() {
		return $this->control_code;
	}
	
	private function set_sector_code($code = '') {
		if($code == '') {
			$arr_code = simple_select('sectors', 'sector_id', $this->sector_id, 'control_code'); //, ' AND flag_master <> \'1\'');
			$this->control_code = $arr_code['control_code'];
		}
		else
			$this->control_code = $code;
	}
	
	public function print_sector_comments() {
		comment::print_object_comments($this->sector_id, 'sector');
	}

	public function print_sector_comment_box() {
		if(!isset($this->control_code))
			$this->set_sector_code();
			
		comment::print_object_comment_box($this->sector_id, 'sector', $this->control_code);
	}
}




?>
