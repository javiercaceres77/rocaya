<?php

include 'css/'. $_GET['mod'] .'_css.php';

function draw_img_table($img, $footer, $width, $height, $img_large = '') {
	global $conf_images_path, $conf_images_reports_subpath;
	
	$table_width = $width + 32;	//????
?>

<table width="<?php echo $table_width; ?>" border="0" align="center" cellpadding="6" cellspacing="0">
  <tr>
    <td align="center"><img src="<?php echo $conf_images_path . $conf_images_reports_subpath . $img; ?>" alt="" title="<?php echo $footer; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" class="report_picture"></td>
  </tr>
  <tr>
    <td align="center" class="report_footer"><?php echo $footer; ?></td>
  </tr>
</table>
<?php	
}
?>
<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top" width="30%"><div class="standard_container default_text"><span class="standard_cont_title"> <?php echo ucfirst(reports); ?> </span><br />
        Otros reportajes por Quique:<br />
        <br />
        <ul class="standard_bullet_list">
          <?php 
$sql = 'SELECT url_id, title, date_publish FROM reports WHERE '. $db_getdate .' BETWEEN date_publish AND date_end AND author_name = \'Quique\' ORDER BY date_publish DESC';

$select_reports = my_query($sql, $conex);

while($record = my_fetch_array($select_reports)) {
	echo '<li><a href="'. $conf_main_page .'?mod='. $_GET['mod'] .'&view=report_det&detail='. $record['url_id'] .'">'. htmlentities($record['title']) .'</a> ('. date2lan($record['date_publish'], 'med') .')</li>';
}

$arr_report = simple_select('reports', 'url_id', $_GET['detail'], array('file_name', 'author_id', 'author_name', 'date_publish', 'title', 'report_id'));

?>
        </ul>
      </div></td>
    <td valign="top"><div class="report_container"> <span class="report_title">
        <?= htmlentities($arr_report['title']); ?>
        </span>
        <!-- use a span so that the element doesn't go until the end of the line -->
        <div style="font-weight:bold" align="right">
          <?php 
	echo ucfirst(by) .' ';
	if($conf_exist_user_detail)
		echo '<a href="'. $conf_main_page .'?mod=users&view=det_user&detail='. $arr_report['author_id'] .'">';
	echo $arr_report['author_name'];
	if($conf_exist_user_detail)
		echo '</a>';
	echo ' &ndash; '. date2lan($arr_report['date_publish'], 'long');
?>
        </div>
        <!-- AddThis Button BEGIN -->
        <div class="addthis_toolbox addthis_default_style "> <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a> <a class="addthis_button_tweet"></a> <a class="addthis_counter addthis_pill_style"></a> </div>
        <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4d8a4fb1438bc4f2"></script>
        <!-- AddThis Button END -->
        <div class="report_text">
          <?php 
	include $conf_mods_path . $_GET['mod'] .'/'. $arr_report['file_name'];
?>
        </div>
        <!-- AddThis Button BEGIN -->
        <div class="addthis_toolbox addthis_default_style "> <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a> <a class="addthis_button_tweet"></a> <a class="addthis_counter addthis_pill_style"></a> </div>
        <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4d8a4fb1438bc4f2"></script>
        <!-- AddThis Button END -->
        <br>
        <div class="title_3">Comentarios</div>
        <?php
$blog_obj = new blog($arr_report['report_id']);
$blog_obj->print_blog_comments();

if($_SESSION['Login']['UserID'] != $conf_generic_user_id)
	$blog_obj->print_blog_comment_box();
else
	echo '<a href="'. $conf_main_page .'?mod=home&view=new_user">Regístrate</a> para escribir comentarios<br />';
?>
      </div></td>
  </tr>
</table>
