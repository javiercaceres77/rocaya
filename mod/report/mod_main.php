<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top" width="30%"><div class="standard_container default_text"><span class="standard_cont_title">Blog</span><br />
        <?php
$user = new user($_SESSION['Login']['UserID']);
if($_SESSION['Login']['modules'][$_GET['mod']]['modify'] || $user->is_admin()) {
?>
        <div align="right"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>&view=new_report"><img src="<?php echo $conf_images_path; ?>new.gif" border="0" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;Nueva Entrada</a></div>
        <?php } ?>
        <div class="indented">Bienvenido al Blog de Rocaya<br />
          Si deseas participar, contacta con nosotros en info@rocaya.com</div>
        <div class="title_3 bg_standard">Entradas por fecha</div>
        <div class="standard_bullet_list">
          <ul>
            <?php
//  $sql = 'SELECT count(*) as num_reps, date_format(date_from, \'%Y-%m\') as y_m FROM blog_head WHERE flag_master <> \'1\' GROUP BY date_format(date_from, \'%Y-%m\') ORDER BY date_format(date_from, \'%Y-%m\') DESC';
//	$sql = 'SELECT count(*) as num_reps, date_format(date_created, \'%Y-%m\') as y_m FROM reports GROUP BY date_format(date_created, \'%Y-%m\') ORDER BY date_format(date_created, \'%Y-%m\') DESC';
$sql = 'SELECT blog_id, url_id, title, date_format(date_from, \'%Y-%m\') as y_m, date_from FROM blog_head WHERE flag_master <> \'1\' ORDER BY date_from DESC';

$select_blogs = my_query($sql, $conex);

$arr_reps = array();
while($record = my_fetch_array($select_blogs)) {
	$arr_reps[$record['y_m']][$record['blog_id']] = array('date' => $record['date_from'], 'title' => $record['title'], 'url_id' => $record['url_id']);
}
//print_array($arr_reps);
foreach($arr_reps as $y_m => $reps) {
	$om_y = new date_time($y_m .'-01');
	echo '<li>'. $om_y->format_date('year_month') .'<ul style="margin-left:-28px;">';
	foreach($reps as $blog_id => $rep) {
		echo '<li><a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=det_blog&detail='. $blog_id .'&id='. $rep['url_id'] .'">'. shorten_str($rep['title'], 30) .'</a></li>';
	}
	echo '</ul></li>';
}

?>
          </ul>
        </div>
        <div class="title_3 bg_standard">Entradas por autor</div>
        <div class="standard_bullet_list">
          <ul>
            <?php
$sql = 'SELECT count(*) as num_reps, author_name FROM blog_head WHERE flag_master <> \'1\' GROUP BY author_name ORDER BY count(*) DESC';
//	$sql = 'SELECT count(*) as num_reps, date_format(date_created, \'%Y-%m\') as y_m FROM reports GROUP BY date_format(date_created, \'%Y-%m\') ORDER BY date_format(date_created, \'%Y-%m\') DESC';

$select_blogs = my_query($sql, $conex);

while($record = my_fetch_array($select_blogs)) {
	//$odate = new date_time($record['y_m']. '-01');
	echo '<li><a href="JavaScript:set_author_filter(\''. $record['author_name'] .'\');">'. $record['author_name'] .' ('. $record['num_reps'] .')</a></li>';
}
?>
          </ul>
        </div>
      </div></td>
    <td valign="top"><?php 
if($_POST['filter_type']) {
	$_SESSION['misc']['blog_filter']['type'] = $_POST['filter_type'];
	$_SESSION['misc']['blog_filter']['value'] = $_POST['filter_value'];
}

if($_POST['filter_type'] == 'date')
	$sql_filter = ' AND date_format(date_from, \'%Y-%m\') = \''. $_POST['filter_value'] .'\'';
elseif($_POST['filter_type'] == 'author')
	$sql_filter = ' AND author_name = \''. $_POST['filter_value'] .'\'';

$sql = 'SELECT blog_id, url_id, author_id, author_name, date_from, title, summary, photo_cover_id, gallery_id 
FROM blog_head WHERE status = \'published\' AND flag_master <> \'1\''. $sql_filter .' 
 AND \''. date('Y-m-d') .'\' BETWEEN date_from AND date_to ORDER BY date_from DESC';
 
$select_blog = my_query($sql, $conex);

$count=0;
while($record = my_fetch_array($select_blog)) {
	# don't use the blog class here as it requires several db accesses per row
	$odate = new date_time($record['date_from']);
	$photo = new photo($record['photo_cover_id']);
	$oblog = new blog($record['blog_id']);
	
	if($count < 7) {
?>
      <div class="standard_container default_text" style="cursor:pointer;" onClick="JavaScript:document.location='<?= $conf_main_page .'?mod='. $_GET['mod'] .'&view=det_blog&detail='. $record['blog_id']; ?>';"><span class="standard_cont_title"><a href="<?= $conf_main_page .'?mod='. $_GET['mod'] .'&view=det_blog&detail='. $record['blog_id']; ?>">
        <?= $record['title']; ?>
        </a></span><br />
        <table width="100%" border="0" cellspacing="4" cellpadding="4">
          <tr>
            <td class="blog_text" valign="top"><?= $record['summary']; ?>
              <h5 align="right">
                <?php 
echo ucfirst(by) .' ';
if($conf_exist_user_detail)
	echo '<a href="'. $conf_main_page .'?mod=users&view=det_user&detail='. $record['author_id'] .'">';
echo $record['author_name'];
if($conf_exist_user_detail)
	echo '</a>';
echo ' &ndash; '. $odate->format_date('long');
	?>
              <?php
$num_comments = $oblog->get_num_comments();
$plural = $num_comments > 1 ? 's' : '';
if($num_comments)
	echo '<br>('. $num_comments .' comentario'. $plural .')';
			  ?></h5></td>
            <td align="right" valign="top"><?php $count < 2 ? $photo->print_small_photo(false) : $photo->print_thumb(); ?></td>
          </tr>
        </table>
      </div>
      <?php    
	}
	else {
		echo '<div class="title_3 indented"><a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=det_blog&detail='. $record['blog_id'] .'">'. $record['title'] .'</a>; '. ucfirst(by) .' '. $record['author_name'] .' &ndash; '. $odate->format_date('med') .'</div>';
	}
	$count++;
}
	
	 ?></td>
  </tr>
</table>
<form name="form_blog" id="form_bolg" action="" method="post">
  <input type="hidden" name="filter_type" />
  <input type="hidden" name="filter_value" />
</form>
<script language="javascript">

function set_date_filter(year_month) {
	document.form_blog.filter_type.value = 'date';
	document.form_blog.filter_value.value = year_month;
	document.form_blog.submit();
}

function set_author_filter(author_name) {
	document.form_blog.filter_type.value = 'author';
	document.form_blog.filter_value.value = author_name;
	document.form_blog.submit();
}

</script>
