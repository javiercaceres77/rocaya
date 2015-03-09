<script language="javascript" src="inc/ajax.js"></script>
<script language="javascript">

function jump_prov () {
	url = 'inc/ajax.php?content=crags_combo_photo&detail='+ document.upl_photos_form.provinces.value;
	getData(url, 'crags_combo_container');
}

function jump_crag () {
	url = 'inc/ajax.php?content=sectors_combo_photo&detail='+ document.upl_photos_form.crags_combo.value;
	getData(url, 'sectors_combo_container');
}

function jump_sector() {
	url = 'inc/ajax.php?content=routes_combo&detail='+ document.upl_photos_form.sectors_combo.value;
	getData(url, 'routes_combo_container');
}

function show_date(date, field) {
	url = 'inc/ajax.php?content=calendar&detail='+ date +'&element='+ field;
	getData(url, field);
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

this_year = '<?php echo date('Y'); ?>';
this_month = '<?php echo date('m'); ?>';

function set_date(date, field) {
	field_hid = document.getElementById('photo_date');
	field_txt = document.getElementById('edit_photo_date');

	// change yyyymmdd by yyyy-mm-dd
	date_dash = date.substr(0,4) + '-' + date.substr(4,2) + '-' + date.substr(6,2);

	field_hid.value = date_dash;
	field_txt.innerHTML = get_desc_date(date);
}

function empty_date(field) {
	field_hid = document.getElementById('photo_date');
	field_txt = document.getElementById('edit_photo_date');

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
	my_new_date = year +'-'+ my_month +'-01';

	url = 'inc/ajax.php?content=calendar&detail='+ my_new_date +'&element='+ field;
	getData(url, field);

	this_year = year;
}


</script>
<style type="text/css">
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
<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><div class="standard_container default_text"> <span class="standard_cont_title">Subir fotos a Rocaya.com</span><br />
<?php

# check that the user hasn't reached the limit of photos
$sql = 'SELECT count(*) AS num_photos
FROM rocaya2.photo_gallery pg INNER JOIN rocaya2.galleries g ON g.gallery_id = pg.gallery_id
WHERE object_id = \''. $_SESSION['Login']['UserID'] .'\' AND object_type = \'user\'';

$select_num = my_query($sql, $conex);
$arr_num_photos = my_fetch_array($select_num);

if($arr_num_photos['num_photos'] <= $conf_max_photos_user) {
?>    
        <form name="upl_photos_form" id="upl_photos_form" enctype="multipart/form-data" method="post" action="<?php echo $conf_main_page .'?mod='. $_GET['mod']; ?>&view=upload_handler">
<?php 	if($_GET['blog']) {		?>
		<input type="hidden" name="blog" value="<?= $_GET['blog']; ?>" />
<?php 	}	?>
          <table align="center" border="0" cellspacing="5" cellpadding="5" width="66%">
            <tr>
              <td align="right" valign="middle" class="bg_standard"><?= $_GET['blog'] ? 'Pie de foto' : 'Título de la foto'; ?> *</td>
              <td><input type="text" name="ph_title" id="ph_title" maxlength="250" class="inputlarge" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle" class="bg_standard">Descripci&oacute;n</td>
              <td><input type="text" name="ph_description" id="ph_description" maxlength="400" class="inputlarge" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle" class="bg_standard">Autor</td>
              <td><input type="text" name="ph_author" id="ph_author" maxlength="250" class="inputlarge" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle" class="bg_standard">Escalador</td>
              <td><input type="text" name="ph_climber" id="ph_climber" maxlength="250" class="inputlarge" /></td>
            </tr>
            <tr>
              <td align="right" valign="middle" class="bg_standard"> Fecha</td>
              <td><input type="hidden" name="photo_date" id="photo_date" />
                <span class="inputlarge" style="width:85px;" id="edit_photo_date"></span>
                <div id="calendar"></div></td>
            </tr>
            <tr>
              <td align="right" valign="middle" class="bg_standard">Provincia</td>
              <td><?php 
			  	  $parameters = array('table' => 'provinces', 'code_field' => 'prov_id', 'desc_field' => 'pname'
				  					 ,'name' => 'provinces', 'on_change' => 'jump_prov()', 'class' => 'inputlarge'
									 ,'order' => ' pname ASC', 'selected' => $_SESSION['last_search']['provinces']
									 ,'empty' => '1');
				  print_combo_db($parameters);
 ?></td>
            </tr>
            <tr>
              <td align="right" valign="middle" class="bg_standard">Escuela</td>
              <td><div id="crags_combo_container"><select name="crags_combo" class="inputlarge" id="crags_combo"></select></div></td>
            </tr>
            <tr>
              <td align="right" valign="middle" class="bg_standard">Sector</td>
              <td><div id="sectors_combo_container"><select name="sectors_combo" class="inputlarge" id="sectors_combo"></select></div></td>
            </tr>
            <tr>
              <td align="right" valign="middle" class="bg_standard">V&iacute;a</td>
              <td><div id="routes_combo_container"><select name="routes_combo" class="inputlarge" id="routes_combo"></select></div></td>
            </tr>
            <tr>
              <td align="right" valign="middle" class="bg_standard"> Subir archivo *</td>
              <td><input type="file" id="ph_file" name="ph_file" class="inputlarge" style="width:225px;" /><br />
Tamáño máximo permitido por imágen: 8Mb</td>
            </tr>
            <tr>
              <td colspan="2" valign="middle">* Campos obligatorios</td>
            </tr>
            <tr>
              <td colspan="2" align="center" valign="middle"><input type="button" onclick="chec_values();" value="   Subir foto   " style="font-size:18px;" class="bottonlarge"/></td>
            </tr>
          </table>
        </form>
<?php
}        //if($arr_num_photos['num_photos'] <= $conf_max_photos_user) {
else {
?>
Has alcanzado el número máximo de fotos permitidas (<?= $conf_max_photos_user; ?>).<br />
Para subir fotos nuevas, borra alguna.
Gracias.
<?php
}
?>
      </div></td>
  </tr>
</table>
<script language="javascript">
document.onload = show_date('<?php echo date('Y-m-d'); ?>', 'calendar');

function chec_values() {
	var error = false;
	if(document.upl_photos_form.ph_title.value == '') {
		alert('Debes escribir un título');
		error = true;
		return;
	}
	
	if(document.upl_photos_form.ph_file.value == '') {
		alert('Debes seleccionar un fichero');
		error = true;
		return;
	}
	
	if(!error)
		document.upl_photos_form.submit();
}
</script>