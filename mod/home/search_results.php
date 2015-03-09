<?php

if($_POST) $_SESSION['last_search'] = $_POST;

function draw_pages_navigator($parameters) {
	global $initial_row, $final_row, $conf_main_page;

/*	$parameters = array('page' => $_SESSION['mod'][$_GET['mod']]['nav_page']
					   ,'num_rows' => 20 ,'num_rows_page' => 25, 'class' => 'border_bottom_dotted')
*/
	# input parameters: width (default 100%); page (default 1); num_rows; num_rows_page; class
	# output values: initial_row; final_row
	# deppending on the page width, display 3/4 results or 2/3
	# the number of pages to be displayed, also deppending on the page width, 3, 5 or 7
	# $list_pages:   < | 1 | 2 | 3 | 4 | 5 | >
	# $list_rows_per_page:    25 | 100 | all
	if($parameters['num_rows'] > 0) {
		$num_rows_limit1 = 25;
		$num_rows_limit2 = 100;
		
		if(!$parameters['width'])	$parameters['width'] = '100%';
		
		if(!$parameters['page']) {
			if($_GET['pag']) $parameters['page'] = $_GET['pag'];
			else $parameters['page'] = 1;
		}
		
		if(!$parameters['num_pages_list']) $parameters['num_pages_list'] = 5;	# Num of pages to display in the list
		
		$num_pages = ceil($parameters['num_rows'] / $parameters['num_rows_page']);		# total number of pages
		
		if($parameters['page'] > $num_pages || !is_numeric($parameters['page'])) $parameters['page'] = $num_pages;	# Shouldn't happen, but just in case
	
		$initial_row = ($parameters['num_rows_page'] * ($parameters['page'] - 1));			# initial row
		$final_row = $initial_row + $parameters['num_rows_page'] - 1;						# final row
		if($final_row > $parameters['num_rows'])
			$final_row = $parameters['num_rows'] - 1;										# adjust final row
		
		$init_page = $parameters['page'] - floor($parameters['num_pages_list'] / 2);		# first page shown is 1 or 2 lower than the current
		if($init_page < 1)																	# unless it's lower than 0
			$init_page = 1;
		
		$fin_page = $init_page + $parameters['num_pages_list'] - 1;							# same for the final page
		if($fin_page > $num_pages) {														# if we are at the end...
			$fin_page = $num_pages;
			$init_page = $num_pages - $parameters['num_pages_list'] + 1;
			if($init_page < 1)
				$init_page = 1;
		}
		
		$file = $conf_main_page .'?';
		foreach($_GET as $key => $value) {
			if($key != 'pag') $file.= $key .'='. $value .'&';
		}
		
		if($init_page > 1) $list_pages = '<a href="'. $file .'pag=1" title="'. first_page .'">&lt; </a>';
		
		for($i = $init_page; $i <= $fin_page; $i++) {
			if($i != $init_page) $list_pages .= '|';
			if($i == $parameters['page'])
				$list_pages .= '<span class="currpage">&nbsp;'. $i .'&nbsp;</span>';
			else
				$list_pages .= '<a href="'. $file .'pag='. $i .'" title="'. go_to .' '. $i .'">&nbsp;'. $i .'&nbsp;</a>';
		}
		
		if($fin_page < $num_pages) $list_pages.= '|<a href="'. $file .'pag='. $num_pages .'" title="'. go_to .' '. last_page .'"> &gt;</a>';
	
		$file = $conf_main_page .'?';	# reuse the same $file variable
		foreach($_GET as $key => $value) {
			if($key != 'nrows') $file.= $key .'='. $value .'&';
		}
	
		# This is for the number of pages to display
		if($parameters['num_rows'] > $num_rows_limit1 + 10) {
			if($parameters['num_rows'] > $num_rows_limit2) {
				switch($parameters['num_rows_page']) {
					case $num_rows_limit1:
						$list_num_rows = '<span class="currpage">&nbsp;'. $num_rows_limit1 .'&nbsp;</span>|<a href="'. $file .'nrows='. $num_rows_limit2 .'" title="'. ucfirst(view) .' '. $num_rows_limit2 .' '. rows_per_page .'">&nbsp;'. $num_rows_limit2 .'&nbsp;</a>|<a href="'. $file .'nrows=all" title="'. ucfirst(view) .' '. all_results_1_page .'">&nbsp;'. all .'&nbsp;</a>';
					break;
					case $num_rows_limit2:
						$list_num_rows = '<a href="'. $file .'nrows='. $num_rows_limit1 .'" title="'. ucfirst(view) .' '. $num_rows_limit1 .' '. rows_per_page .'">&nbsp;'. $num_rows_limit1 .'&nbsp;</a>|<span class="currpage">&nbsp;'. $num_rows_limit2 .'&nbsp;</span>|<a href="'. $file .'nrows=all" title="'. ucfirst(view) .' '. all_results_1_page .'">&nbsp;'. all .'&nbsp;</a>';
					break;
					default:
						$list_num_rows = '<a href="'. $file .'nrows='. $num_rows_limit1 .'" title="'. ucfirst(view) .' '. $num_rows_limit1 .' '. rows_per_page .'">&nbsp;'. $num_rows_limit1 .'&nbsp;</a>|<a href="'. $file .'nrows='. $num_rows_limit2 .'" title="'. ucfirst(view) .' '. $num_rows_limit2 .' '. rows_per_page .'">&nbsp;'. $num_rows_limit2 .'&nbsp;</a>|<span class="currpage">&nbsp;'. all .'&nbsp;</span>';
				}
			}
			else {
				switch($parameters['num_rows_page']) {
					case $num_rows_limit1:
						$list_num_rows = '<span class="currpage">&nbsp;'. $num_rows_limit1 .'&nbsp;</span>|<a href="'. $file .'nrows=all" title="'. ucfirst(view) .' '. all_results_1_page .'">&nbsp;'. all .'&nbsp;</a>';
					break;
					default:
						$list_num_rows = '<a href="'. $file .'nrows='. $num_rows_limit1 .'" title="'. ucfirst(view) .' '. $num_rows_limit1 .' '. rows_per_page .'">&nbsp;'. $num_rows_limit1 .'&nbsp;</a>|<span class="currpage">&nbsp;'. all .'&nbsp;</span>';
				}
			}												# ^^^^^^^^^^ between first limit and second limit
		}
		else
			$list_num_rows = $parameters['num_rows'];		# ---------- between first limit and first limit + 10
	?>

<table border="0" cellpadding="3" cellspacing="0" width="100%" class="<?php echo $parameters['class']; ?>">
  <tr>
    <td class="small_text"><strong><?php echo $parameters['num_rows']; ?></strong> <?php echo results; ?>&nbsp;&nbsp;&nbsp;<?php echo ucfirst(shown); ?>: <?php echo ($initial_row + 1) .' '. to .' '. ($final_row + 1); ?></td>
    <td class="small_text"><?php echo ucfirst(view); ?>&nbsp;&nbsp;<?php echo $list_num_rows; ?></td>
    <!--<td class="small_text">list | grid</td>-->
    <td align="right" class="small_text"><?php echo ucfirst(page) .':&nbsp;'. $list_pages; ?></td>
  </tr>
</table>
<?php  
	}	//	if($parameters['num_rows'] > 0) {
}	//function draw_pages_navigator($parameters) {


$conditions = array();
if($_SESSION['last_search']['provinces'])		$conditions[] = 'p.prov_id = '. $_SESSION['last_search']['provinces'];
if($_SESSION['last_search']['crags_combo']) 	$conditions[] = 'c.crag_id = \''. $_SESSION['last_search']['crags_combo'] .'\'';
if($_SESSION['last_search']['sectors_combo'])	$conditions[] = 's.sector_id = \''. $_SESSION['last_search']['sectors_combo'] .'\'';
if($_SESSION['last_search']['grades']) 			$conditions[] = 'r.grade = \''. $_SESSION['last_search']['grades'] .'\'';
if($_SESSION['last_search']['route']) 			$conditions[] = 'r.rname like \'%'. $_SESSION['last_search']['route'] .'%\'';

$sql = 'SELECT r.route_id, r.sector_id, r.crag_id, r.rname, r.grade, r.img_bck, c.cname, s.sname, p.pname, c.crag_id_url, s.sector_id_url
FROM routes r
INNER JOIN crags c ON r.crag_id = c.crag_id
INNER JOIN sectors s ON r.sector_id = s.sector_id
INNER JOIN provinces p ON c.prov_id = p.prov_id
WHERE '. implode(' AND ', $conditions) .' LIMIT 1000';

$select_routes = my_query($sql, $conex);

$num_results = my_num_rows($select_routes);

?>
<style type="text/css">
<!--
.search_result {
	background-color: #CCCCCC;
	margin: 5px;
	padding: 5px;
}
.currpage {
	font-weight: bold;
	color: #FFFFFF;
	background-color: #333333;
}
-->
</style>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="125" valign="top" class="default_text"><div class="standard_container"><span class="standard_cont_title"> <?php echo ucfirst(your_search); ?> </span>
        <form action="" method="post" name="search_form" id="search_form">
          <?php echo ucfirst(province); ?><br>
          <?php 
	$parameters = array('table' => 'provinces', 'code_field' => 'prov_id', 'desc_field' => 'pname'
					 ,'name' => 'provinces', 'on_change' => 'jump_prov()', 'class' => 'inputnormal'
					 ,'order' => ' pname ASC', 'selected' => $_SESSION['last_search']['provinces']);
	print_combo_db($parameters);
 ?>
          <br>
          <br>
          <?php echo ucfirst(crag); ?><br>
          <div id="crags_combo_container">
            <?php 
	$parameters = array('table' => 'crags', 'code_field' => 'crag_id', 'desc_field' => 'cname'
				   , 'name' => 'crags_combo', 'on_change' => 'jump_crag()', 'class' => 'inputnormal'
				   , 'order' => ' cname', 'empty' => 1
				   , 'extra_condition' => ' prov_id = \''. $_SESSION['last_search']['provinces'] .'\''
				   , 'substr' => 35, 'selected' => $_SESSION['last_search']['crags_combo']);

	print_combo_db($parameters); 
		?>
          </div>
          <br>
          <?php echo ucfirst(sector); ?><br>
          <div id="sectors_combo_container">
            <?php 
	if($_SESSION['last_search']['crags_combo']) {
		$parameters = array('table' => 'sectors', 'code_field' => 'sector_id', 'desc_field' => 'sname'
						   , 'name' => 'sectors_combo', 'class' => 'inputnormal', 'order' => ' sname', 'empty' => 1
						   , 'extra_condition' => ' crag_id = \''. $_SESSION['last_search']['crags_combo'] .'\''
						   , 'substr' => 35, 'selected' => $_SESSION['last_search']['sectors_combo']);

		print_combo_db($parameters);
	}
	else
		echo '<select name="sectors_combo" class="inputnormal" id="sectors_combo"></select>';
?>
          </div>
          <br>
          <?php echo ucfirst(dificulty); ?><br>
          <?php
	  
   $parameters = array('table' => 'grades_weight', 'code_field' => 'grade', 'desc_field' => 'grade'
   					   , 'name' => 'grades' ,'class' => 'inputnormal' ,'order' => ' grade', 'empty' => 1
					   , 'selected' => $_SESSION['last_search']['grades']);
	print_combo_db($parameters);
   ?>
          <br>
          <br>
          <?php echo ucfirst(route_name); ?><br>
          <input type="text" name="route" class="inputnormal" id="route" style="width:100px" value="<?php echo $_SESSION['last_search']['route']; ?>">
          &nbsp;<img title="<?php echo help_search_route; ?>" src="<?php echo $conf_images_path; ?>help2.gif" align="absmiddle" /><br>
          <br>
          <div align="center">
            <input type="button" value="      <?php echo ucfirst(to_search); ?>      " onClick="JavaScript:jump2search();" />
          </div>
          <br>
        </form>
      </div></td>
    <td valign="top"><?php 
	$initial_row = 0;
	$final_row = 0;

	if($_GET['pag']) $_SESSION['mod'][$_GET['mod']]['nav_page'] = $_GET['pag'];
	if(!$_SESSION['mod'][$_GET['mod']]['nav_page']) $_SESSION['mod'][$_GET['mod']]['nav_page'] = 1;

	if($_GET['nrows']) $_SESSION['mod'][$_GET['mod']]['nrows'] = $_GET['nrows'];
	if(!$_SESSION['mod'][$_GET['mod']]['nrows']) $_SESSION['mod'][$_GET['mod']]['nrows'] = 25;
	if(!is_numeric($_SESSION['mod'][$_GET['mod']]['nrows']) || $_SESSION['mod'][$_GET['mod']]['nrows'] < 0 || $_SESSION['mod'][$_GET['mod']]['nrows'] > 1000) {
		if($_SESSION['mod'][$_GET['mod']]['nrows'] == 'all') {
			$_SESSION['mod'][$_GET['mod']]['nrows'] = 1000;
		}
		else {
			$_SESSION['mod'][$_GET['mod']]['nrows'] = 25;
		}
	}
	
	$parameters = array('page' => $_SESSION['mod'][$_GET['mod']]['nav_page']
					   ,'num_rows' => $num_results ,'num_rows_page' => $_SESSION['mod'][$_GET['mod']]['nrows'], 'class' => 'border_bottom_dotted');

	draw_pages_navigator($parameters);

?>
      <table border="0" cellpadding="3" cellspacing="3" width="100%">
        <?php
if($num_results > 0) {
	$row = 0;
	while($record = my_fetch_array($select_routes)) {
		if($row >= $initial_row && $row <= $final_row) {
?>
        <tr>
          <td class="default_text search_result"><?php echo ucfirst(route); ?>: <?php echo htmlentities($record['rname']); ?> (<?php echo htmlentities($record['grade']); ?>)</td>
          <td class="default_text search_result">Sector: <a href="<?php echo $conf_main_page .'?mod=routes&view=detail_sector&detail='. $record['sector_id'] .'&id='. $record['sector_id_url']; ?>"><?php echo htmlentities($record['sname']); ?></a> </td>
          <td class="default_text search_result"><a href="<?php echo $conf_main_page .'?mod=routes&view=detail_crag&detail='. $record['crag_id'] .'&id='. $record['crag_id_url']; ?>"><?php echo htmlentities($record['cname']); ?></a> (<?php echo htmlentities($record['pname']); ?>)</td>
        </tr>
        <?php
	}
	$row++;
	}	//while($record = my_fetch_array($select_routes)) {
}	//if($num_results > 0) {
else { 
?>
        <tr>
          <td class="default_text search_result"><strong>No hay ninguna v&iacute;a que coincida con los criterios de b&uacute;squeda</strong></td>
        </tr>
        <?php
}
?>
      </table>
      <?php
      $parameters['class'] = 'border_top_dotted';
	  draw_pages_navigator($parameters);
      ?>
    </td>
  </tr>
</table>
<script language="javascript" src="inc/ajax.js"></script>
<script language="javascript">

function jump_prov () {
	url = 'inc/ajax.php?content=crags_combo&detail='+ document.search_form.provinces.value;
	getData(url, 'crags_combo_container');
}

function jump_crag () {
	url = 'inc/ajax.php?content=sectors_combo&detail='+ document.search_form.crags_combo.value;
	getData(url, 'sectors_combo_container');
}

function jump2search() {
	document.search_form.action = '<?php echo $conf_main_page, '?mod='. $_GET['mod'] .'&view=search_results'; ?>';
	document.search_form.submit();
}

</script>
