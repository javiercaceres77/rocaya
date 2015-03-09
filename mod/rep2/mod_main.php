<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top" width="30%"><div class="standard_container default_text"><span class="standard_cont_title"> <?php echo ucfirst(reports); ?> </span><br />
        Bienvenidos al area de reportajes de ROCAYA.<br />
        Te animamos a participar con tu propio reportaje sobre escalada. Inf&oacute;rmate en info@rocaya.com <br /><br />
        Reportajes por fecha: <br />
        <div class="filter" id="year_filter">2011 (4)</div>
        Reportajes por usuario: <br />
        <div class="filter" id="user_filter">Quique (3)</div>
        <div class="filter" id="user_filter">Coke (1)</div>
        </div><!-- set the above divs as filters, then with JS asign value to the below form and submit it to this own file -->
<!--      <form action="" method="post" name="report_form" id="search_form">
        <input type="hidden" name="year" value="<?php echo $_SESSION['mod'][$_GET['mod']]['filter_date']; ?>" />
        <input type="hidden" name="user" value="<?php echo $_SESSION['mod'][$_GET['mod']]['filter_user']; ?>" />
      </form>--></td>
    <td valign="top"><?php
// this file shows a list of all active reports
 
include 'css/'. $_GET['mod'] .'_css.php';
// here limit the number of reports to show; create some kind of paging
$sql = 'SELECT * FROM reports WHERE '. $db_getdate .' BETWEEN date_publish AND date_end ORDER BY date_publish DESC';

$select_reports = my_query($sql, $conex);

while($record = my_fetch_array($select_reports)) {
?>
      <div class="report_summary_container" id="<?= $record['url_id']; ?>" onClick="document.location='<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=report_det&detail='. $record['url_id']; ?>'"> <span class="report_summary_title">
        <?= htmlentities($record['title']); ?>
        </span>
        <table width="100%" border="0" cellspacing="4" cellpadding="4">
          <tr>
            <td class="report_text" valign="top"><?= htmlentities($record['summary']); ?>
              <h5 align="right">
                <?php 
	echo ucfirst(by) .' ';
	if($conf_exist_user_detail)
		echo '<a href="'. $conf_main_page .'?mod=users&view=det_user&detail='. $record['author_id'] .'">';
	echo $record['author_name'];
	if($conf_exist_user_detail)
		echo '</a>';
	echo ' &ndash; '. date2lan($record['date_publish'], 'long');
	?>
              </h5></td>
            <td align="right" valign="top"><img src="<?php echo $conf_images_path . $conf_images_reports_subpath . $record['picture']; ?>" width="120" height="90" class="report_picture"></td>
          </tr>
        </table>
      </div>
      <?php 
}	//while($record = my_fetch_array($select_sector_routes)) {
?></td>
  </tr>
</table>
