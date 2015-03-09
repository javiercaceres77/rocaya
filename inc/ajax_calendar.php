<?php

$week_days_esp = array('L','M','X','J','V','S','D');
$week_days_eng = array('Mo','Tu','We','Th','Fr','Sa','Su');

$weeks_arr = 'week_days_'. $_SESSION['misc']['lang'];
if(!is_array($$weeks_arr))
	$weeks_arr = 'week_days_'. $conf_default_lang;

$weeks_arr = $$weeks_arr;	

if(!$_GET['detail'])
	$_GET['detail'] = date('Y-m-d');

# This below is for compatibility with old code
$_GET['year'] = substr($_GET['detail'], 0, 4);
$_GET['month'] = substr($_GET['detail'], 5, 2);
$_GET['field'] = $_GET['element'];

?>

<table width="250" border="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr class="headtable">
    <td height="25" align="right" width="60"><a href="JavaScript:show_year(<?php echo ($_GET['year'] - 1); ?>, '<?= $_GET['field']; ?>');" title="<?php echo ucfirst(go_to) .' '. ($_GET['year'] - 1) ?>"><img src="<?php echo $conf_images_path; ?>left.png" width="16" height="16" border="0" /></a></td>
    <td align="center" width="130"><?php echo $_GET['year']; ?></td>
    <td align="left" width="60"><a href="JavaScript:show_year(<?php echo ($_GET['year'] + 1); ?>, '<?= $_GET['field']; ?>');" title="<?php echo ucfirst(go_to) .' '. ($_GET['year'] + 1) ?>"><img src="<?php echo $conf_images_path; ?>right.png" width="16" height="16" border="0" /></a></td>
  </tr>
  <tr class="headtable">
    <td height="25" align="center"><input type="button" value="<?php echo ucfirst(today); ?>" onClick="JavaScript: set_date('<?= date('Ymd'); ?>', '<?= $_GET['field']; ?>')" class="smallinput" title="<?= go_to .' '. today ?>"></td>
    <td height="25" align="center"><?php 		  
		$in = array('name' => 'combo_months', 'class' => 'inputnewnowidth', 'empty' => '0', 'selected' => $_GET['month'], 'on_change' => 'JavaScript:show_month(\''. $_GET['field'] .'\');');
		print_moths_combo($in); 
	  ?></td>
    <td height="25" align="center"><input type="button" value="<?= ucfirst(empty_) ?>" onClick="JavaScript: empty_date('<?= $_GET['field']; ?>')" class="smallinput"></td>
  </tr>
  <tr>
    <td colspan="3"><table width="248" border="0" cellspacing="1">
        <tr align="center" bgcolor="#CCCCCC">
          <?php

function add_zero($number) {
	if (($number < 10) && substr($number, 0, 1) != '0')	return '0'. $number;
	else return $number;
}

function remove_zero($number) {
	if(strlen($number) == 2 && substr($number, 0, 1) == '0')
		return substr($number, 1, 1);
	else
		return $number;
}
	
		  /*------------------ weekdays row --------------------*/
for($i=0; $i < 7; $i++){
	if($i==0)
		print('<td width="14%" height="22" class="list"><strong>'. $weeks_arr[$i] .'</strong></td>');
	else
		print('<td width="14%" class="list"><strong>'. $weeks_arr[$i] .'</strong></td>');
}

print('</tr>');

			/* ---------------- here begin the month days --------------- */
			/* Which day is monday on the first week of this month? ----- */
			
$day_one = date('w', mktime(0,0,0,$_GET['month'],1,$_GET['year']));	// day of the week of the first date
if($day_one == 0) $day_one = 7;

$first_monday = date('Ymd', mktime(0,0,0,$_GET['month'], 2 - $day_one, $_GET['year']));	// first monday of this month or the previous, thiw will be used as a start point to count.
$first_mon_day = substr($first_monday, 6, 2);
$first_mon_month = substr($first_monday, 4, 2);
$first_mon_year = substr($first_monday, 0, 4);


$day_counter = 0;
for($i = 0; $i < 6; $i++) {
	print('<tr align="center">');
	for($j = 0; $j < 7; $j++) {
		$curr_date = date('Ymd', mktime(0,0,0,$first_mon_month, $first_mon_day + $day_counter, $first_mon_year));
		if(substr($curr_date, 4, 2) == $_GET['month']) 	$my_class = 'cal_days';
		else											$my_class = 'cal_days_deact';
		if($j >= 5) 									$my_class.= ' cal_days_wknd';
		if($curr_date == date('Ymd')) 					$my_class.= ' cal_days_today';
		
		$curr_day = remove_zero(substr($curr_date, 6, 2));
		$day_counter++;
		print('<td align="center" class="'. $my_class .'" onclick="JavaScript:set_date(\''. $curr_date .'\', \''. $_GET['field'] .'\');">'. $curr_day .'</td>');
	}
	print('</tr>');
}
			?>
      </table></td>
  </tr>
</table>
