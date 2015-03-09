<div class="standard_container default_text">
<span class="standard_cont_title"> Reportar contenido inadecuado </span>
<table border="0" width="66%" align="center">
  <tr>
    <td><?php

if($_SESSION['Login']['UserID'] != $conf_generic_user_id) {
		
	
	if($_POST['rep_reason']) {
			# obtain object's author
		$author_id = get_object_owner($_GET['object_id'], $_GET['object_type']);
			
		# record the report in the DB
		$ins_array = array('object_id' => $_GET['object_id'], 'object_type' => $_GET['object_type'], 'author_id' => $author_id
						  ,'reporter_id' => $_SESSION['Login']['UserID'], 'report_date' => date('Y-m-d'), 'report_reason' => $_POST['rep_reason']);
		
		insert_array_db ('inappropriate_reports', $ins_array);

		# select number of times the object has been reported by other users
		$sql = 'SELECT count(*) as num_reports FROM inappropriate_reports WHERE object_id = \''. $_GET['object_id'] .'\' AND object_type = \''. $_GET['object_type'] .'\' AND reporter_id <> \''. $_SESSION['Login']['UserID'] .'\'';
		$select_num_reports = my_query($sql, $conex);
		$num_reports = my_result($select_num_reports, 0, 'num_reports');
		
		$num_reposts++;	# to account for the current report
	
		# the number of reports on an object has reached the maximum, delete the object
		if($num_reports >= $conf_min_inapp_reports)	
			flag_object_inapp($_GET['object_id'], $_GET['object_type'], $_GET['code']);
			//delete_object($_GET['object_id'], $_GET['object_type'], $_GET['code']);
		
?>
      <div class="title_3">Gracias, el reporte de contenido inadecuado ha sido recogido, lo estudiaremos y tomaremos medidas<br />
        <a href="<?= $conf_main_page; ?>">Volver a la página principal</a></div>
      <?php

		exit();
	}	//if($_POST['rep_reason']) {
	
	?>
      Por favor, selecciona el motivo por el que consideras que este contenido es inadecuado:
      <div class="indented">
        <form method="post" action="" name="form_innap" id="form_innap">
          <label>
          <input name="rep_reason" type="radio" id="rep_reason_3" value="spam, adv" checked="checked" />
          Spam o publicidad<br />
          </label>
          <label>
          <input type="radio" name="rep_reason" value="nude, porn" id="rep_reason_0" />
          Contenido erótico, sexual o pornográfico</label>
          <br />
          <label>
          <input type="radio" name="rep_reason" value="violent, ilegal" id="rep_reason_1" />
          Contenido violento, xenófobo o ilegal</label>
          <br />
          <label>
          <input type="radio" name="rep_reason" value="copyright" id="rep_reason_2" />
          Violación de Copyright, contenido protegido</label>
          <br />
          <label>
          <input type="radio" name="rep_reason" value="non related" id="rep_reason_4" />
          Contenido no relacionado</label>
          <br />
          <br />
          <input type="submit" name="send" id="send" value="   Enviar   "  class="inputnewnowidth" />
          <br />
          <br />
          <span class="small_text">Este reporte es an&oacute;nimo.</span>
        </form>
      </div>
      <?php } 	// if($_SESSION['Login']['UserID'] != $conf_generic_user_id) { ?></td>
  </tr>
</table>
