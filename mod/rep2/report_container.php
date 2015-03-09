<?php
$arr_report = simple_select('reports', 'url_id', $_GET['rep'], array('file_name', 'author_id', 'author_name', 'date_publish', 'title'));

function draw_img_table($img, $footer, $width, $height, $img_large = '') {
	$table_width = $width + 32;
?>

	  <table width="<?php echo $table_width; ?>" border="0" align="center" cellpadding="6" cellspacing="0">
        <tr>
          <td align="center"><img src="images/pics_reports/<?php echo $img; ?>" alt="" title="<?php echo $footer; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" class="report_picture"></td>
        </tr>
        <tr>
          <td align="center" class="report_footer"><?php echo $footer; ?></td>
        </tr>
      </table>
<?php	
}
?>
<div class="report_container">
<span class="report_title"><?= htmlentities($arr_report['title']); ?></span><!-- use a span so that the element doesn't go until the end of the line -->
<div style="font-weight:bold" align="right"><?php 
	echo ucfirst(by) .' ';
	if($conf_exist_user_detail)
		echo '<a href="main.php?mod=users&user='. $arr_report['author_id'] .'">';
	echo $arr_report['author_name'];
	if($conf_exist_user_detail)
		echo '</a>';
	echo ' &ndash; '. date2lan($arr_report['date_publish'], 'long');
?></div>
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
</div>