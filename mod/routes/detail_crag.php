<?php
$desc_column = 'desc_'. substr($_SESSION['misc']['lang'], 0, 2);

# get crag details
$sql = 'SELECT c.cname, c.'. $desc_column .' as description, p.pname
FROM crags c
INNER JOIN provinces p 
ON p.prov_id = c.prov_id
WHERE c.crag_id = \''. $_GET['detail'] .'\'';

$select_crag_details = my_query($sql, $conex);

$crag_details = my_fetch_array($select_crag_details);

?>

<div class="default_text whereami"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>">CROQUIS</a> &gt; <?php echo $crag_details['cname']; ?></div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" width="33%" class="default_text"><div class="standard_container"><span class="standard_cont_title"> <?php echo $crag_details['cname']; ?> </span><br>
        <?php echo $crag_details['description']; ?><br>
        <br>
        <div class="title_3">Sectores en esta escuela:</div>
        <?php

# get sectors and number of routes on each sector for this crag
$sql = 'SELECT s.sname, s.sector_id, s.sector_id_url, count(s.sector_id) as num_routes 
FROM sectors s
INNER JOIN routes r
ON s.sector_id = r.sector_id
WHERE s.crag_id = \''. $_GET['detail'] .'\'
GROUP BY s.sname, s.sector_id, s.sector_id_url
ORDER BY s.sector_id';

$select_sectors = my_query($sql, $conex);
?>
        <ul class="standard_bullet_list">
          <?php  
while($record = my_fetch_array($select_sectors)) {
?>
          <li><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_sector&detail='. $record['sector_id'] .'&id='. $record['sector_id_url']; ?>">
            <?= $record['sname'] ?>
            </a> (<?php echo $record['num_routes']; ?> vías)</li>
          <?php
}  
?>
        </ul>
      </div>
    </td>
    <td valign="top" class="default_text">
    </td>
  </tr>
</table>
