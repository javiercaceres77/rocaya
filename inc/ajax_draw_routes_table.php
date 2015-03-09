<form method="post" name="form_chungo" id="form_chungo" action="">
<table width="100%" border="0" cellspacing="2" cellpadding="2" class="default_text">
  <tr align="center" bgcolor="#CCCCCC">
<?php if($_SESSION['misc']['images'][$_SESSION['misc']['images']['current_bck']]) { ?>
    <td><strong>Ver</strong></td>
<?php } ?>
    <td><strong>N&ordm;</strong></td>
    <td><strong>Nombre vía</strong></td>
    <td><strong>Grado</strong></td>
    <td><strong>Equip.</strong></td>
<?php
if($_SESSION['Login']['UserID'] != $conf_generic_user_id) {
?>
    <td><strong>AV</strong></td>
    <td><strong>AF</strong></td>
    <td><strong>E</strong></td>
    <td><strong>Pegues</strong></td>
    <td><strong>P2</strong></td>
    <td><strong>Fecha</strong></td>
    <td><strong>Repet.</strong></td>
    <td><strong>Comentarios</strong></td>
    <td><strong>Edit</strong></td>
<?php
}
?>
  </tr>
<?php

# Get routes and details for current user for this sector
$sql = 'SELECT r.rname, r.number, r.grade, r.equipment, ur.climb_date, ur.climb_type, ur.num_tries, r.route_id, r.img_bck, r.img_via, ur.comments, ur.retry_date, ur.retry 
FROM routes r 
LEFT JOIN (
    SELECT route_id, climb_date, climb_type, num_tries, comments, retry_date, retry
    FROM users_routes
    WHERE user_id = '. $_SESSION['Login']['UserID'] .'
) ur 
ON ur.route_id = r.route_id 
WHERE r.sector_id = \''. $_GET['detail'] .'\' 
ORDER BY r.route_id';

$select_sector_routes = my_query($sql, $conex);

# use a secodary array to store all the routes
$array_routes = array();
while($record = my_fetch_array($select_sector_routes)) {
	$array_routes[$record['number']] = $record;
}

#Sort the record by num_route
ksort($array_routes);

#Asign to $record again so that i don't have to rewrite everythign
foreach($array_routes as $key => $record) {
//while($record = my_fetch_array($select_sector_routes)) {

	$id_checkbox = $record['route_id'];

	if($record['climb_date'] == '0000-00-00') $record['climb_date'] = '';
	if($record['climb_date']) {
		$date_no_dash = str_replace('-', '', $record['climb_date']);
		$climb_date_txt = date2lan($date_no_dash, 'med');
	}
	else
		$climb_date_txt = '&nbsp;';

	if($record['retry_date'] == '0000-00-00') $record['retry_date'] = '';
	if($record['retry_date']) {
		$date_no_dash = str_replace('-', '', $record['retry_date']);
		$climb_date2_txt = date2lan($date_no_dash, 'med');
	}
	else
		$climb_date2_txt = '&nbsp;';

	if(strlen($record['comments']) > 100)
		$record['comments'] = substr($record['comments'], 0, 100) . '...';

?>
  <tr bgcolor="<?php echo $record['climb_date']?'#D6E4D8':'#F0F0F0'; ?>">
<?php if($_SESSION['misc']['images'][$_SESSION['misc']['images']['current_bck']]) { ?>
	<td align="center"><?php if($record['img_via']) { ?><a href="JavaScript:show_image(<?= $record['route_id']; ?>);"><img src="<?php echo $conf_images_path; ?>image.gif" alt="Mostrar Imágen" border="0" /></a><?php } ?></td>
<?php } ?>
    <td align="center"><?= $record['number'] ?>
<?php
	if($_SESSION['Login']['UserID'] != $conf_generic_user_id) {
?>
    <input type="hidden" name="route_id_<?php echo $record['route_id']; ?>" id="route_id_<?php echo $record['route_id']; ?>" value="<?= htmlentities($record['route_id']) ?>" />
    <input type="hidden" name="route_name_<?php echo $record['route_id']; ?>" id="route_name_<?php echo $record['route_id']; ?>" value="<?= htmlentities($record['rname']) ?>" />
    <input type="hidden" name="route_grade_<?php echo $record['route_id']; ?>" id="route_grade_<?php echo $record['route_id']; ?>" value="<?= $record['grade'] ?>" />
    <input type="hidden" name="route_type_<?php echo $record['route_id']; ?>" id="route_type_<?php echo $record['route_id']; ?>" value="<?= $record['climb_type'] ?>" />
    <input type="hidden" name="route_tries_<?php echo $record['route_id']; ?>" id="route_tries_<?php echo $record['route_id']; ?>" value="<?= $record['num_tries'] ?>" />
    <input type="hidden" name="route_retry_<?php echo $record['route_id']; ?>" id="route_retry_<?php echo $record['route_id']; ?>" value="<?= $record['retry'] ?>" />
    <input type="hidden" name="route_date1_<?php echo $record['route_id']; ?>" id="route_date1_<?php echo $record['route_id']; ?>" value="<?= $record['climb_date'] ?>" />
    <input type="hidden" name="route_date2_<?php echo $record['route_id']; ?>" id="route_date2_<?php echo $record['route_id']; ?>" value="<?= $record['retry_date'] ?>" />
    <input type="hidden" name="route_comms_<?php echo $record['route_id']; ?>" id="route_comms_<?php echo $record['route_id']; ?>" value="<?= htmlentities($record['comments']) ?>" />
<?php
	} 
?>
</td>
    <td><!--<img src="images/mosqueton20x20.gif" width="10" height="10" align="absmiddle" />--><?= htmlentities($record['rname']) ?></td>
    <td><?= htmlentities($record['grade']) ?></td>
    <td style="font-size:12px;"><?= htmlentities($record['equipment']) ?></td>
<?php
	if($_SESSION['Login']['UserID'] != $conf_generic_user_id) {
?>
    <td><input disabled="disabled" type="checkbox" <?php if($record['climb_type'] == '1') print(' checked="checked" '); ?>/></td>
    <td><input disabled="disabled" type="checkbox" <?php if($record['climb_type'] == '2') print(' checked="checked" '); ?>/></td>
    <td><input disabled="disabled" type="checkbox" <?php if($record['climb_type'] == '3') print(' checked="checked" '); ?>/></td>
    <td align="right"><?= $record['num_tries']?$record['num_tries']:'&nbsp;'; ?></td>
    <td><input disabled="disabled" type="checkbox" <?php if($record['retry'] == '1') print(' checked="checked" '); ?>/></td>
    <td align="right"><?= $climb_date_txt; ?></td>
    <td align="right"><?= $climb_date2_txt; ?></td>
    <td style="font-size:12px;"><?= htmlentities($record['comments']) ?></td>
    <td align="center"><a id="edit_func_<?= $record['route_id'] ?>" href="JavaScript:edit_route(<?= $record['route_id'] ?>)"><img id="edit_icon_<?= $id_checkbox ?>" src="<?php echo $conf_images_path; ?>edit.gif" alt="Insertar datos" width="16" height="16" border="0" /></a></td>
<?php
	} 
?>
  </tr>
<?php
} //while
?>
</table></form>