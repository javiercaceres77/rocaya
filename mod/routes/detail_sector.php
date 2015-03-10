<?php
	# get sector data
$sql = 'SELECT s.sector_id, s.sname, c.cname, c.crag_id, c.crag_id_url, count(s.sector_id) as num_routes
FROM sectors s 
INNER JOIN crags c ON s.crag_id = c.crag_id
INNER JOIN routes r ON r.sector_id = s.sector_id
WHERE s.sector_id = \''. $_GET['detail'] .'\'
GROUP BY s.sector_id, s.sname, c.cname, c.crag_id, c.crag_id_url';

$select_sector = my_query($sql, $conex);

$sector_details = my_fetch_array($select_sector);
?>
<script language="javascript">
function jump_sector() {
	url = '<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_sector&detail='; ?>' + document.sectors_form.sectors_combo.value;
	document.location = url;
//	alert(document.sectors_form.sectors_combo.value);
}
</script>

<div class="default_text whereami"><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>">CROQUIS</a>&nbsp;&gt;&nbsp; <a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=detail_crag&detail='. $sector_details['crag_id'] .'&id='. $sector_details['crag_id_url']; ?>"><?php echo $sector_details['cname']; ?></a>&nbsp;&gt;&nbsp; <?php echo $sector_details['sname']; ?></div>
<div class="standard_container"> <span class="standard_cont_title"><?php echo $sector_details['sname']; ?></span><br>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="default_text">
    <tr>
      <td><?php  echo $sector_details['num_routes']; ?>
        v&iacute;as en este sector</td>
      <td align="right"><form action="" method="post" name="sectors_form" id="sectors_form">
          otros sectores en <?php echo $sector_details['cname']; ?>:
          <?php
$parameters = array('table' => 'sectors', 'code_field' => 'sector_id', 'desc_field' => 'sname'
				   , 'name' => 'sectors_combo', 'class' => 'inputnormal', 'order' => ' sname', 'empty' => 0
				   , 'extra_condition' => ' crag_id = \''. $sector_details['crag_id'] .'\''
				   , 'substr' => 35, 'selected' => $_GET['detail'], 'on_change' => 'jump_sector()');

		print_combo_db($parameters);    
	?>
        </form></td>
    </tr>
  </table>
  <br>
  <?php


// ---------------------------- Select default picture to show -------------------
$sql = 'SELECT route_id FROM routes WHERE sector_id = '. $_GET['detail'] .'
AND number = (SELECT min(number) FROM routes WHERE sector_id = '. $_GET['detail'] .' AND img_bck IS NOT NULL and img_bck != \'\')';
 
$select_def_img = my_query($sql, $conex);
$default_route_id_img = my_result($select_def_img, 0, 'route_id');

my_free_result($select_def_img);

//------------------------------- Build an array with all the images for the current sector

$sql = 'SELECT r.img_via, r.img_bck, r.route_id, i.width, i.height
FROM routes r INNER JOIN images i ON i.file_name = r.img_bck
WHERE r.sector_id = '. $_GET['detail'];

$select_imgs_curr_route = my_query($sql, $conex);

$array_images = array();

//------------------------------- Build a function JS to swith the backgroudn image and build the $array_images array -------------------
?>
  <script language="javascript" src="inc/ajax.js"></script>
  <script language="javascript">
var current_bck = '<?= $default_route_id_img ?>';
function switch_bck(route) {
	switch(route) {
<?php
while($record = my_fetch_array($select_imgs_curr_route)) {
	$array_images[$record['route_id']] = array('via' => $record['img_via'], 'bck' => $record['img_bck'], 'w' => $record['width'], 'h' => $record['height']);
		print('case '. $record['route_id'] .':  return \''. $record['img_bck'] .'&'. $record['width'] .'&'. $record['height'] .'\'; break;
	');
}	//while($record = my_fetch_array($select_imgs_curr_route)) {
my_free_result($select_imgs_curr_route);

$_SESSION['misc']['images'] = $array_images;
$_SESSION['misc']['images']['current_bck'] = $default_route_id_img;

?>	default: return 'error'; break;
	}	//switch(route) {
}  //function switch_bck(route) {

function show_image(route_id) {
	var str_returned = switch_bck(route_id);	//swithc_bck returns the background image that belongs to route_id, width and height like 'img_01_00102a00.jpg&450&600'
	var pos_amp_1 = str_returned.indexOf('&');
	var pos_amp_2 = str_returned.indexOf('&', pos_amp_1 + 1);
	var my_img = str_returned.substring(0, pos_amp_1);
	var my_width = str_returned.substring(pos_amp_1 + 1, pos_amp_2);
	var my_height = str_returned.substring(pos_amp_2 + 1);
	
	if(my_img != current_bck) {
		var imgbck = document.getElementById('imgbck');
		imgbck.style.backgroundImage = 'url("<?php echo $conf_images_path . $conf_images_routes_subpath; ?>'+ my_img +'")';
		imgbck.style.width = my_width +'px';
		imgbck.style.height = my_height +'px';
		current_bck = my_img;
	}

	url = 'inc/ajax.php?content=route_image&detail='+ route_id;
	getData(url, 'imgbck'); //this displays the route image over the background
}

function edit_route(route_id) {
	document.edit_route_form.edit_route_save_route.disabled = false;
	
	my_div = document.getElementById('edit_route');
	my_div.style.visibility = 'visible';
	window.scroll(0,90);
	
	// set edit route box title
	my_route_name = document.getElementById('route_name_'+ route_id).value;
	my_route_grade = document.getElementById('route_grade_'+ route_id).value;
	document.getElementById('edit_route_title').innerHTML = my_route_name + ' (' + my_route_grade + ')';

	// set form elements to their value if exist --------------------------------------------------------------------------------
	// this is for the route id		----------------------------------------
	document.edit_route_form.edit_route_route_id.value = document.getElementById('route_id_'+ route_id).value;
	
	// this is for the climb type	 ----------------------------------------
	my_radio = document.edit_route_form.edit_route_climb_type;
	radio_value_checked = document.getElementById('route_type_'+ route_id).value;
	setCheckedValue(my_radio, radio_value_checked);
	
	// this is for the num of tries "pegues" ----------------------------------------
	if(document.getElementById('route_tries_'+ route_id).value != '0')
		document.edit_route_form.edit_route_tries.value = document.getElementById('route_tries_'+ route_id).value;
	else
		document.edit_route_form.edit_route_tries.value = '';
	if(document.getElementById('route_retry_'+ route_id).value != '0' && document.getElementById('route_retry_'+ route_id).value != '')
		document.edit_route_form.edit_route_retry.checked = true;
	else
		document.edit_route_form.edit_route_retry.checked = false;
	
	// this is for the climb date ----------------------------------------
	if(document.getElementById('route_date1_'+ route_id).value) {
		document.edit_route_form.edit_route_hidden_calendar_1.value = document.getElementById('route_date1_'+ route_id).value;	//YYYY-MM-DD
	
		this_date = document.edit_route_form.edit_route_hidden_calendar_1.value;	//YYYY-MM-DD
		this_year = this_date.substring(0,4);	// global variable
		this_month = this_date.substring(5,7);	// global variable
		
		this_date_no_dash = this_year + this_month + this_date.substring(8,10);
	
		document.getElementById('edit_route_calendar_1').innerHTML = get_desc_date(this_date_no_dash);
	}
	else {
		this_date = '';
		document.edit_route_form.edit_route_hidden_calendar_1.value = '';
		document.getElementById('edit_route_calendar_1').innerHTML = '&nbsp;';
	}

	url = 'inc/ajax.php?content=calendar&detail='+ this_date +'&element=calendar_1';
	getData(url, 'calendar_1');

	// this is for the retry date ----------------------------------------
	if(document.getElementById('route_date2_'+ route_id).value) {
		document.edit_route_form.edit_route_hidden_calendar_2.value = document.getElementById('route_date2_'+ route_id).value;	//YYYY-MM-DD
	
		this_date2 = document.edit_route_form.edit_route_hidden_calendar_2.value;	//YYYY-MM-DD
		this_year2 = this_date2.substring(0,4);	// global variable
		this_month2 = this_date2.substring(5,7);	// global variable
		
		this_date_no_dash2 = this_year2 + this_month2 + this_date2.substring(8,10);
	
		document.getElementById('edit_route_calendar_2').innerHTML = get_desc_date(this_date_no_dash2);
	}
	else {
		this_date2 = '';
		document.edit_route_form.edit_route_hidden_calendar_2.value = '';
		document.getElementById('edit_route_calendar_2').innerHTML = '&nbsp;';
	}
	
	url = 'inc/ajax.php?content=calendar&detail='+ this_date2 +'&element=calendar_2';
	getData2(url, 'calendar_2');	// this is to allow parallel requests with AJAX

	// this is for the comments box -----------------------------------------
	document.edit_route_form.edit_route_comments.innerHTML = document.getElementById('route_comms_'+ route_id).value;

}

function change_conditions(cond) {
	if(cond == 'AV' || cond == 'AF') {
		document.edit_route_form.edit_route_tries.disabled = true;
		document.edit_route_form.edit_route_tries.value = '1';
	}
	else
		document.edit_route_form.edit_route_tries.disabled = false;
}

function hide_editor() {
	my_div = document.getElementById('edit_route');
	my_div.style.visibility = 'hidden';
}

function setCheckedValue(radioObj, newValue) {
//http://www.somacon.com/p143.php
	if(!radioObj)
		return;
	var radioLength = radioObj.length;
	if(radioLength == undefined) {
		radioObj.checked = (radioObj.value == newValue.toString());
		return;
	}
	for(var i = 0; i < radioLength; i++) {
		radioObj[i].checked = false;
		if(radioObj[i].value == newValue.toString()) {
			radioObj[i].checked = true;
		}
	}
}

function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}

function save_route() {
	// disable the save button to avoid multiple clicks
	document.edit_route_form.edit_route_save_route.disabled = true;

	my_route_id = document.edit_route_form.edit_route_route_id.value;
	my_climb_type = getCheckedValue(document.edit_route_form.edit_route_climb_type);
	my_num_tries = document.edit_route_form.edit_route_tries.value;
	my_route_retry = document.edit_route_form.edit_route_retry.checked;
	my_climb_date = document.edit_route_form.edit_route_hidden_calendar_1.value;
	my_retry_date = document.edit_route_form.edit_route_hidden_calendar_2.value;
	my_comments = document.edit_route_form.edit_route_comments.value;

	//check for errors before saving
	exist_error = false;
	if(my_climb_type == '1' || my_climb_type == '2') {
		if(!my_climb_date) {
			exist_error = true;
			document.getElementById('edit_route_date1_container').className = 'error_container';
			document.getElementById('edit_route_date1_error').innerHTML = 'Debes seleccionar una fecha<br>';
			document.edit_route_form.edit_route_save_route.disabled = false;
		}
	}
	
	if(!isNaN(my_num_tries) && my_num_tries != '0') {
		if(!my_climb_date) {
			exist_error = true;
			document.getElementById('edit_route_date1_container').className = 'error_container';
			document.getElementById('edit_route_date1_error').innerHTML = 'Debes seleccionar una fecha<br>';
			document.edit_route_form.edit_route_save_route.disabled = false;
		}
	}
	else {
		my_num_tries = 0;
	}
	
	// Save the data into the DB with ajax_save_route.php
	if(!exist_error) {
		var params = 'route_id='+ my_route_id +'&climb_type='+ my_climb_type + '&num_tries=' + my_num_tries + '&retry=' + my_route_retry +'&climb_date='+ my_climb_date +'&retry_date='+ my_retry_date +'&comments='+ my_comments;
		url = 'inc/ajax.php?content=save_route&' + params;
//		getDataPOST(url, 'edit_func_'+ my_route_id, params, 'post_save');
		getData_param(url, 'edit_func_'+ my_route_id, 'post_save()') 

	}
}

function post_save() {
	hide_editor();
	window.setTimeout(draw_routes_table, 1500);
}

// ------------------------ FUNCTIONS FOR CALENDARS --------------------------- //
function get_desc_date(date) {
<?php
	switch($_SESSION['misc']['lang']) {
		case 'esp': 
?>	//esp	20061215 -> 15-dic-2006
	months = new Array('ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic');
	return date.substring(6,8) + '-' + months[parseInt(date.substring(4,6), 10) - 1] + '-' + date.substring(0,4);
<?php
		break;
		case 'eng': default:
?>	// eng	20061215 -> Dec-15-2006
	months = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
//	ordinals = new Array('st','nd','rd','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th','th');
	return months[parseInt(date.substring(4,6)) - 1] + '-' + date.substring(6,8) + '-' + date.substring(0,4);
<?php
	}
?>
}

this_year = '';
this_month = '';
this_year2 = '';
this_month2 = '';


function set_date(date, field) {
	field_hid = document.getElementById('edit_route_hidden_'+ field);
	field_txt = document.getElementById('edit_route_'+ field);

	// change yyyymmdd by yyyy-mm-dd
	date_dash = date.substr(0,4) + '-' + date.substr(4,2) + '-' + date.substr(6,2);

	field_hid.value = date_dash;
	field_txt.innerHTML = get_desc_date(date);
}

function empty_date(field) {
	field_hid = document.getElementById('edit_route_hidden_'+ field);
	field_txt = document.getElementById('edit_route_'+ field);

	field_hid.value = '';
	field_txt.innerHTML = '&nbsp;';
}

function show_month(field) {
// change calendar contents to selected month
	my_month = document.getElementById('combo_months').value;
	my_new_date = this_year +'-'+ my_month +'-01';

	url = 'inc/ajax.php?content=calendar&detail='+ my_new_date +'&element='+ field;
	getData(url, field);

	this_month = my_month;
}

function show_year(year, field) {
// change calendar contents to selected year
	my_month = document.getElementById('combo_months').value;
	my_new_date = year +'-'+ this_month +'-01';

	url = 'inc/ajax.php?content=calendar&detail='+ my_new_date +'&element='+ field;
	getData(url, field);

	this_year = year;
}

</script>
  <style type="text/css">
<!--
#imgbck {
	height: <?= $array_images[$default_route_id_img]['h'] ?>px;
	width: <?= $array_images[$default_route_id_img]['w'] ?>px;
	background-image: url("<?= $conf_images_path . $conf_images_routes_subpath . $array_images[$default_route_id_img]['bck'] ?>");
	background-position: 0px 0px;
}
#imgvia {
	position:inherit;
}
#edit_route {
	position:absolute;
	top:0px;
	left:280px;
	visibility:hidden;
	z-index:1;
	width:330px;
}
.error_container {
	border-left: 2px solid #FF0000;
	border-right: 2px solid #FF0000;
	border-bottom: 1px solid #FF0000;
	border-top:	1px solid #FF0000;
}
/* ----------- CALENDAR STYLES -------------- */
.cal_days, .cal_days_deact, .cal_days_wknd {
	font-family: Calibri, "Trebuchet MS", sans-serif;
	font-size: 18px;
	font-weight: bold;
	cursor: pointer;
}
.cal_days_deact {
	color:#CCCCCC;
}
.cal_days_wknd {
	background-color:#e9e4e0;
}
.cal_days_today {
	border-left: 2px solid #FF0000;
	border-right: 2px solid #FF0000;
	border-bottom: 1px solid #FF0000;
	border-top:	1px solid #FF0000;
}
.cal_days:hover, .cal_days_today:hover, .cal_days_wknd:hover, .cal_days_deact:hover {
	background-color:#666666;
}
-->
</style>
  <div id="edit_route">
    <div class="standard_container default_text"> <span class="standard_cont_title">Editar V&iacute;a</span><br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top"><div class="title_3" id="edit_route_title"></div></td>
    <td align="right" valign="top"><a href="JavaScript:hide_editor()" title="Cerrar editor"><img src="<?php echo $conf_images_path; ?>close.png" alt="Close Calendar" width="28" height="15" border="0" /></a></td>
  </tr>
</table>
      
      <form action="" method="post" name="edit_route_form" id="edit_route_form">
        <table align="center" border="0" cellpadding="4" cellspacing="8">
          <tr>
            <td bgcolor="#ebebeb"><input type="hidden" name="edit_route_route_id" id="edit_route_route_id" />Tipo de escalada<br>
              <label>
              <input name="edit_route_climb_type" id="edit_route_climb_type" type="radio" value="1" onclick="JavaScript:change_conditions('AV');">
              A Vista</label>&nbsp;&nbsp;<img align="absmiddle" src="<?php echo $conf_images_path; ?>help2.gif" title="Escalas sin reposos artificiales, sin haber visto a nadie previamente, sin haberla probado nunca y sin conocimiento de ruta. Sólo tienes un intento." />
              <br>
              <label>
              <input name="edit_route_climb_type" id="edit_route_climb_type" type="radio" value="2" onclick="JavaScript:change_conditions('AF');">
              A Flash</label>&nbsp;&nbsp;<img align="absmiddle" src="<?php echo $conf_images_path; ?>help2.gif" title="Has visto a alguien previamente, te han cantado los pasos, o te has informado del truquillo. Sólo tienes un intento." />
              <br>
              <label>
              <input name="edit_route_climb_type" id="edit_route_climb_type" type="radio" value="3" onclick="JavaScript:change_conditions('E');">
              Encadenada</label>&nbsp;&nbsp;<img align="absmiddle" src="<?php echo $conf_images_path; ?>help2.gif" title="Has fallado en las anteriores y ya has probado la vía mas veces, por tanto el número de pegues será como mínimo 2 o la has probado de segundo P2" />
              <br></td>
          </tr>
          <tr>
            <td bgcolor="#ebebeb"> Pegues&nbsp;&nbsp;
              <input name="edit_route_tries" id="edit_route_tries" type="text" value="" class="inputlarge" style="width:30px;">
              &nbsp;&nbsp;&nbsp;<img align="absmiddle" src="<?php echo $conf_images_path; ?>help2.gif" title="Número de intentos" />&nbsp;&nbsp;
              <label>
              <input name="edit_route_retry" id="edit_route_retry" type="checkbox">
              P2</label>&nbsp;&nbsp;<img align="absmiddle" src="<?php echo $conf_images_path; ?>help2.gif" title="Probada de 2º. Top-rope o cuerda por arriba." /></td>
          </tr>
          <tr>
            <td bgcolor="#ebebeb" id="edit_route_date1_container"><input type="hidden" name="edit_route_hidden_calendar_1" id="edit_route_hidden_calendar_1" />
            <span class="error_message" id="edit_route_date1_error"></span>Fecha escalada&nbsp;&nbsp;<span class="inputlarge" style="width:85px;" id="edit_route_calendar_1"></span>&nbsp;&nbsp;<img align="absmiddle" src="<?php echo $conf_images_path; ?>help2.gif" title="Fecha de AV, AF, E o P2. Es imprescindible meter fecha para procesar posteriormente la sección de estadísticas." />
            <div id="calendar_1"></div></td>
          </tr>
          <tr>
            <td bgcolor="#ebebeb"><input type="hidden" name="edit_route_hidden_calendar_2" id="edit_route_hidden_calendar_2" />
            Fecha repetición&nbsp;&nbsp;<span class="inputlarge" style="width:85px;" id="edit_route_calendar_2"></span>&nbsp;&nbsp;<img align="absmiddle" src="<?php echo $conf_images_path; ?>help2.gif" title="Fecha de Repetición. Si vuelves a hacer la vía el mismo u otro día y quieres contabilizarla para tus estadísticas personales." />
            <div id="calendar_2"></div></td>
          </tr>
          <tr>
            <td bgcolor="#ebebeb">Comentarios<br />
              <textarea name="edit_route_comments" id="edit_route_comments" class="inputlarge"></textarea></td>
          </tr>
          <tr>
            <td align="center" bgcolor="#ebebeb"><input name="edit_route_save_route" id="edit_route_save_route" type="button" value="Guardar" onclick="JavaScript:save_route()" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
<?php 
if($array_images) {
?>
  <table align="center" border="0" cellspacing="0" cellpadding="4" bgcolor="#333333">
    <tr>
      <td align="center"><div id="imgbck">&nbsp;</div></td>
    </tr>
  </table>
<?php
}
?>
  <br>
  <div id="routes_table">&nbsp;</div>
  <br>
</div>
<script language="javascript">

function draw_routes_table() {
	url= 'inc/ajax.php?content=routes_table&detail=<?php echo $_GET['detail']; ?>';
	getData(url, 'routes_table');
}

document.onload = draw_routes_table();

</script>
