<?php

function disp_randome_picture() {
	global $conex, $conf_images_path, $conf_pictures_subpath;
	$sql = 'SELECT photo_id FROM photos WHERE flag_master <> \'1\' ORDER BY auto_ranking DESC limit 30';	// only works on mysql!!
	
	$select_photo = my_query($sql, $conex);
	$photo_ids = array();
	while($record = my_fetch_array($select_photo))
		$photo_ids[] = $record['photo_id'];
	
	$_SESSION['misc']['rotate']['ph_ids'] = $photo_ids;
	$_SESSION['misc']['rotate']['last'] = 0;
	
?>

<div class="standard_container"> <span class="standard_cont_title">
  <?= photos; ?>
  </span>
  <div class="default_text" id="rotating_photos_container" onmouseover="JavaScript:pause_rotation();" onmouseout="JavaScript:resume_rotation();"></div>
</div>
<?php	
}

function disp_last_news() {
	global $conex;
	$sql = 'SELECT text_'. substr($_SESSION['misc']['lang'], 0, 2) .' as new_text, date_published FROM news WHERE visible = 1 ORDER BY date_published DESC limit 5';// only works on mysql!!

	$select_new = my_query($sql, $conex);
?>
<div class="standard_container"> <span class="standard_cont_title">
  <?= news; ?>
  </span>
  <div class="default_text">
    <ul class="standard_bullet_list">
      <?php 
	while($record = my_fetch_array($select_new)) {
		echo '<li>'. $record['new_text'] .' ('. $record['date_published'] .')</li>';
	}	
?>
    </ul>
  </div>
</div>
<?php	

}

function disp_web_stats() {
	global $conex;
	$select_nroutes_reg = my_query('SELECT COUNT( route_id ) AS Nvias FROM routes', $conex);
	$select_nroutes = my_query('SELECT COUNT( route_id ) AS Nvias FROM routes WHERE img_bck IS NOT NULL', $conex);
	$select_ncrags_sk = my_query('SELECT count(distinct c.crag_id) as Ncrags FROM crags c, routes r WHERE r.crag_id = c.crag_id AND r.img_bck IS NOT NULL', $conex);
	$select_ncrags = my_query('SELECT count(*) as Ncrags FROM crags', $conex);

?>
<div class="standard_container"> <span class="standard_cont_title">
  <?= data; ?>
  </span>
  <div class="default_text">
    <ul class="standard_bullet_list">
      <li><?php echo num_routes_reg .': '. my_result($select_nroutes_reg, 0, 'Nvias'); ?></li>
      <!--<li><?php echo num_routes_sketch .': '. my_result($select_nroutes, 0, 'Nvias'); ?></li>-->
      <li><?php echo num_crags_total .': '. my_result($select_ncrags, 0, 'Ncrags'); ?></li>
      <!--<li><?php echo num_crags_sketch .': '. my_result($select_ncrags_sk, 0, 'Ncrags'); ?></li>-->
    </ul>
  </div>
</div>
<?php	

}

function disp_last_report($cols = 2) {
	global $conex, $conf_exist_user_detail, $conf_main_page, $conf_images_path, $conf_images_reports_subpath, $db_getdate;
	
	$sql = 'SELECT * FROM blog_head WHERE '. $db_getdate .' BETWEEN date_from AND date_to AND flag_master <> \'1\' ORDER BY date_from DESC LIMIT 4';
//	echo $sql;
	$select_reports = my_query($sql, $conex);
		
//	$report_data = my_fetch_array($select_reports);

?>
<div class="standard_container"><span class="standard_cont_title"> blog </span>
  <table width="100%" border="0" cellspacing="4" cellpadding="0">
  <?php
  
  	while($report_data = my_fetch_array($select_reports)) {
  
 ?> 
    <tr>
      <td class="default_text" valign="top"><a class="title_3" style="position:relative; top:-10px;" href="<?php echo $conf_main_page .'?mod=report&view=det_blog&detail='. $report_data['blog_id'] .'&id='. $report_data['url_id']; ?>"><?php echo $report_data['title']; ?></a><br />
        <?php echo $report_data['summary']; ?> <br />
        <br />
        <div class="title_4" align="right">
          <?php 
	echo ucfirst(by) .' ';
	if($conf_exist_user_detail)
		echo '<a href="'. $conf_main_page .'?mod=users&view=det_user&detail='. $report_data['author_id'] .'">';
	echo $report_data['author_name'];
	if($conf_exist_user_detail)
		echo '</a>';
	echo ' &ndash; '. date2lan($report_data['date_from'], 'long');
	
	$photo = new photo($report_data['photo_cover_id']);
	
	?>
        </div></td>
      <td align="right" valign="top"><?php $photo->print_photo('thumb', false); ?></td>
    </tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<?php 
	}
	?>
  </table>
</div>
<?php
}

function disp_routes_finder() {
	global $conf_images_path;
?>
<div class="standard_container"><span class="standard_cont_title">
  <?= search_of_routes; ?>
  </span>
  <table border="0" cellpadding="4" cellspacing="4" width="100%">
    <tr>
      <td class="default_text" align="right"><?php echo ucfirst(province); ?></td>
      <td><?php 
			  	  $parameters = array('table' => 'provinces', 'code_field' => 'prov_id', 'desc_field' => 'pname'
				  					 ,'name' => 'provinces', 'on_change' => 'jump_prov()', 'class' => 'inputnormal'
									 ,'order' => ' pname ASC', 'selected' => $_SESSION['last_search']['provinces']);
				  print_combo_db($parameters);
 ?></td>
    </tr>
    <tr>
      <td class="default_text" align="right"><?php echo ucfirst(crag); ?></td>
      <td><div id="crags_combo_container">
          <?php
      if($_SESSION['last_search']['provinces']) {
	  		$parameters = array('table' => 'crags', 'code_field' => 'crag_id', 'desc_field' => 'cname'
				   , 'name' => 'crags_combo', 'on_change' => 'jump_crag()', 'class' => 'inputnormal'
				   , 'order' => ' cname', 'empty' => 1
				   , 'extra_condition' => ' prov_id = \''. $_SESSION['last_search']['provinces'] .'\''
				   , 'substr' => 35, 'selected' => $_SESSION['last_search']['crags_combo']);

			print_combo_db($parameters); 
	  }
	  else 
	  	echo '<select name="crags_combo" class="inputnormal" id="crags_combo"></select>'; 
		?>
        </div></td>
    </tr>
    <tr>
      <td class="default_text" align="right"><?php echo ucfirst(sector); ?></td>
      <td><div id="sectors_combo_container">
          <?php
      if($_SESSION['last_search']['provinces']) {
		$parameters = array('table' => 'sectors', 'code_field' => 'sector_id', 'desc_field' => 'sname'
						   , 'name' => 'sectors_combo', 'class' => 'inputnormal', 'order' => ' sname', 'empty' => 1
						   , 'extra_condition' => ' crag_id = \''. $_SESSION['last_search']['crags_combo'] .'\''
						   , 'substr' => 35, 'selected' => $_SESSION['last_search']['sectors_combo']);

		print_combo_db($parameters);
	  }
	  else 
	  	echo '<select name="sectors_combo" class="inputnormal" id="sectors_combo"></select>'; 
		?>
        </div></td>
    </tr>
    <tr>
      <td class="default_text" align="right"><?php echo ucfirst(dificulty); ?></td>
      <td><?php
	  
   $parameters = array('table' => 'grades_weight', 'code_field' => 'grade', 'desc_field' => 'grade'
   					   , 'name' => 'grades' ,'class' => 'inputnormal' ,'order' => ' grade', 'empty' => 1
					   , 'selected' => $_SESSION['last_search']['grades']);
	print_combo_db($parameters);
   ?></td>
    </tr>
    <tr>
      <td class="default_text" align="right"><?php echo ucfirst(route_name); ?></td>
      <td><input type="text" name="route" class="inputnormal" id="route" style="width:100px" value="<?php echo $_SESSION['last_search']['route']; ?>">
        &nbsp;<img title="<?php echo help_search_route; ?>" src="<?php echo $conf_images_path; ?>help2.gif" align="absmiddle" /></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input type="button" value="      <?php echo ucfirst(to_search); ?>      " onclick="JavaScript:jump2search();" /></td>
    </tr>
  </table>
</div>
<?php
}

function disp_legend($cols = 1) {
	global $conf_images_path;
?>
<div class="standard_container">
<span class="standard_cont_title">
<?= legend; ?>
</span>
<div class="default_text">Dispones de un <b>FORO</b> para comentar lo que quieras, de una secci&oacute;n de <b>REPORTAJES</b> donde podr&aacute;s 
  leer todos los art&iacute;culos que vayamos subiendo (si quieres mandarnos tu propio relato no lo dudes y escr&iacute;benos unas l&iacute;neas con 4 fotos 
  para que las incluyamos, la maquetaci&oacute;n y el resto lo hacemos nosotros) y por &uacute;ltimo una secci&oacute;n de <b>CROQUIS</b> donde ver&aacute;s las fotos 
  de las rutas con un listado de v&iacute;as o <B>TICK LIST</B> para grabar y editar tus ascensiones. S&oacute;lo tienes que clickar en los siguientes 
  iconos cuando se despliegue la lista de v&iacute;as: </div>
<table width="100%" border="0" cellpadding="4" cellspacing="4">
  <tr>
    <td align="center"><img src="<?php echo $conf_images_path; ?>image.gif" alt="Mostrar Im&aacute;gen" border="0" /></td>
    <td class="default_text">Mostrar v&iacute;a (aparece un icono por v&iacute;a a la izquierda del nombre dela misma)</td>
  </tr>
  <tr>
    <td align="center"><img src="<?php echo $conf_images_path; ?>edit.gif" alt="Mostrar Datos" border="0" /></td>
    <td class="default_text">Editar/Grabar v&iacute;a (a la derecha del tick list, una por ruta)</td>
  </tr>
  <tr>
    <td align="center"><input name="" type="checkbox" value="" disabled="disabled" checked="checked" /></td>
    <td class="default_text"><b><u>AV</u> A Vista</b>. Escalas sin reposos artificiales, sin haber visto a nadie previamente, sin haberla probado nunca y sin conocimiento de ruta. S&oacute;lo tienes un intento.</td>
  </tr>
  <tr>
    <td align="center"><input name="" type="checkbox" value="" disabled="disabled" checked="checked" /></td>
    <td class="default_text"><b><u>AF</u> A Flash</b>. Has visto a alguien previamente, te han cantado los pasos, o te has informado del truquillo. S&oacute;lo tienes un intento.</td>
  </tr>
  <tr>
    <td align="center"><input name="" type="checkbox" value="" disabled="disabled" checked="checked" /></td>
    <td class="default_text"><b><u>E</u> Encadenada</b>. Has fallado en las anteriores y ya has probado la v&iacute;a mas veces, por tanto el n&uacute;mero de pegues ser&aacute; como m&iacute;nimo 2 o la has probado de segundo P2</td>
  </tr>
  <tr>
    <td align="center"><input name="" type="text" size="5" maxlength="0" class="inputnormal" style="width:85px;"/></td>
    <td class="default_text"><b><u>P</u> Pegues</b>. N&uacute;mero de intentos</td>
  </tr>
  <tr>
    <td align="center"><input name="" type="checkbox" value="" disabled="disabled" checked="checked" /></td>
    <td class="default_text"><b><u>P2</u> Probada de 2&ordm;</b>. Top-rope o cuerda por arriba.</td>
  </tr>
  <tr>
    <td><div class="cal_box_active" style="cursor:auto"/>
      <?php echo date2lan(date('Ymd'), 'med'); ?>
  </div>
  
  </td>
  
  <td class="default_text"><b><u>Fecha</u> Fecha de AV, AF, E o P2</b>. Muestra un calendario interactivo. Es imprescindible meter fecha para procesar posteriormente la secci&oacute;n de estad&iacute;sticas</td>
  </tr>
  <tr>
    <td><div class="cal_box_active" style="cursor:auto"/>
      <?php echo date2lan(date('Ymd'), 'med'); ?>
      </div></td>
    <td class="default_text"><b><u>Repet</u> Fecha de Repetici&oacute;n</b>. Si vuelves a hacer la v&iacute;a el mismo u otro d&iacute;a y quieres contabilizarla para tus estad&iacute;sticas personales</td>
  </tr>
  <tr>
    <td align="center"><input name="" type="text" size="5" maxlength="0" class="inputnormal" style="width:85px;"/></td>
    <td class="default_text"><b><u>Comments</u> Comentarios</b>. Tus comentarios e impresiones de la v&iacute;a.</td>
  </tr>
</table>
</div>
<?php
}

function disp_random_route() {
?>
<div class="standard_container"> <span class="standard_cont_title">
  <?= routes; ?>
  </span>
  <div class="default_text">una ruta aleatoria</div>
</div>
<?php
}

function disp_last_climb() {
	global $conex, $conf_main_page;
?>
<div class="standard_container default_text"> <span class="standard_cont_title"> <?php echo ucfirst(last_climb); ?></span><br />
  <?php 

	$desc_column = 'desc_'. substr($_SESSION['misc']['lang'], 0, 2);
	
	$sql = 'SELECT s.sname, s.sector_id, s.sector_id_url, c.crag_id, c.crag_id_url, c.cname, r.rname, r.grade, ur.climb_date, ct.'. $desc_column .' AS description
	FROM users_routes ur
	INNER JOIN routes r ON r.route_id = ur.route_id
	INNER JOIN crags c ON c.crag_id = r.crag_id
	INNER JOIN sectors s ON r.sector_id = s.sector_id
	LEFT JOIN climbs_types ct ON ur.climb_type = ct.climb_type_id
	WHERE ur.user_id = '. $_SESSION['Login']['UserID'] .'
	AND ur.climb_date = (
	SELECT max( climb_date )
	FROM users_routes WHERE user_id = '. $_SESSION['Login']['UserID'] .')';
	
	$select_last_climb = my_query($sql, $conex);
	
	$last_climb = my_fetch_array($select_last_climb);

?>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>Fecha</td>
      <td><?php echo date2lan($last_climb['climb_date'], 'long'); ?></td>
    </tr>
    <tr>
      <td>V&iacute;a</td>
      <td><?php echo htmlentities($last_climb['rname']) .' ('. $last_climb['grade'] .')'; ?></td>
    </tr>
    <tr>
      <td>Tipo</td>
      <td><?php echo htmlentities($last_climb['description']); ?></td>
    </tr>
    <tr>
      <td>Sector</td>
      <td><a href="<?php echo $conf_main_page .'?mod=routes&view=detail_sector&detail='. $last_climb['sector_id'] .'&id='. $last_climb['sector_id_url']; ?>"><?php echo htmlentities($last_climb['sname']); ?></a></td>
    </tr>
    <tr>
      <td>Escuela</td>
      <td><a href="<?php echo $conf_main_page .'?mod=routes&view=detail_crag&detail='. $last_climb['crag_id'] .'&id='. $last_climb['crag_id_url']; ?>"><?php echo htmlentities($last_climb['cname']); ?></a></td>
    </tr>
  </table>
  <br />
</div>
<?php
}

function disp_video() {
?>
<div class="standard_container default_text"> <span class="standard_cont_title">v&iacute;deo</span><br />
  <div align="center">
    <iframe width="420" height="315" src="http://www.youtube.com/embed/R_PPv8DFgxQ" frameborder="0" allowfullscreen></iframe>
    <br />
    <br />
  </div>
</div>
<?php
}

function social_networks() {
	global $conf_images_path;
?>
<div class="standard_container default_text"> <span class="standard_cont_title">s&iacute;guenos</span><br />
  <table width="100%" border="0" cellpadding="4" cellspacing="4">
    <tr>
      <td valign="top" align="center" width="33%"><a href="https://www.facebook.com/groups/211862282191350/" target="_blank"><img src="<?php echo $conf_images_path; ?>facebook.png" alt="Síguenos en Facebook" width="64" height="64" border="0" title="Síguenos en Facebook" /></a></td>
      <td valign="top" align="center" width="33%"><a href="http://twitter.com/#!/rocayapuntocom" target="_blank"><img src="<?php echo $conf_images_path; ?>twitter.png" alt="Síguenos en twitter" width="64" height="64" border="0" title="Síguenos en twitter" /></a></td>
    </tr>
  </table>
</div>
<?php
}

function mobile_apps() {
	global $conex, $conf_images_path;
?>
<div class="standard_container default_text"> <span class="standard_cont_title">aplicaciones móviles</span><br />
  <?php

	$sql = 'SELECT * FROM mobile_apps WHERE active = \'1\' ORDER BY platform, sort_order';
	$select_apps = my_query($sql, $conex);
	
	$platform = '';
	while($record = my_fetch_array($select_apps)) {
		if($platform != $record['platform']) {
			$platform = $record['platform'];
			switch($platform) {
				case 'android':
				?>
  <img src="<?php echo $conf_images_path; ?>android_market.png" alt="Android Market" border="0" width="304" height="43" title="Android Market" /><br />
  <span class="small_text">Las aplicaciones de rocaya requiren Adobe AIR <img src="<?php echo $conf_images_path; ?>Adobe_AIR.png" alt="Adobe AIR icon" align="absmiddle" border="0" width="16" height="16" title="Adobe AIR" /> Este se instala gratuitamente al abrir cualquiera de nuestras aplicaciones la primera vez.</span>
  <?php
				break;
				case 'apple':
				?>
  <img src="<?php echo $conf_images_path; ?>app_store.png" alt="App Store" border="0" width="300" height="43" title="App Store" /><br />
  <span class="small_text">La applicación de Rocaya para iPhone todavía no está disponible en el App Store. Sin embargo, puedes descargar e instalar directamente la aplicación desde aquí.</span>
  <?php
				break;
			}	
		}
?>
  <div class="standard_container" style="background-color:#ebebeb">
    <table width="100%" align="center" border="0" cellpadding="4">
      <tr>
        <td valign="top"><table width="100%" align="center" border="0" cellpadding="4">
            <tr>
              <td width="100px" rowspan="2"><img src="<?= $conf_images_path . $record['picture']; ?>" width="72" height="72" /></td>
              <td><span class="title_1">
                <?= $record['app_name']; ?>
                </span><br />
                <span class="small_text">v.
                <?= $record['version']; ?>
                </span></td>
            </tr>
            <tr>
              <td colspan="2"><?= $record['description']; ?></td>
            </tr>
            <?php	if($record['qr_code']) {	?>
            <?php	}	?>
          </table></td>
        <td width="80" align="right" valign="top"><div class="thin_border_picture" style="text-align:center; padding:10px; width:72px; height:72px; background-color:#FFFFFF"> <a href="<?= $record['link']; ?>">
            <?= $record['price']; ?>
            <br />
            <img src="<?= $conf_images_path; ?>download.png" border="0" /></a> </div></td>
        <td width="80" align="center" valign="top"><?php if($record['qr_code']) {	?>
          <img src="<?= $conf_images_path . $record['qr_code']; ?>" />
          <?php } ?></td>
      </tr>
    </table>
  </div>
  <?php
	}
?>
</div>
<?php

}

function mobile_apps_small() {
	global $conex, $conf_images_path;
?>
<div class="standard_container default_text">
<span class="standard_cont_title">aplicaciones móviles</span><br />
<table width="85%" border="0" cellpadding="2" cellspacing="2" align="center">
  <?php

	$sql = 'SELECT desc_id, platform, picture, price, app_name, version, link FROM mobile_apps WHERE active = \'1\' ORDER BY sort_order';
	$select_apps = my_query($sql, $conex);
	
	while($record = my_fetch_array($select_apps)) {
?>
  <tr>
    <td align="center" width="52"><img src="<?= $conf_images_path . $record['picture']; ?>" width="48" height="48" />
    <td>
    <td><span class="title_1"> <a href="<?= $conf_main_page .'?mod=apps&detail='. $record['desc_id']; ?>">
      <?= $record['app_name']; ?>
      </a> </span></td>
    <td><?php
    switch($record['platform']) {
		case 'android':
			$img = 'android24.png';
		break;
		case 'apple':
			$img = 'apple24.png';
		break;
	}
	?><img src="<?= $conf_images_path . $img; ?>" width="24" height="24" align="absmiddle" />&nbsp;<a href="<?= $record['link']; ?>">
      <?= $record['price']; ?></td>
  </tr>
  </div>
  
  <?php
	}	//while($record = my_fetch_array($select_apps)) {
?>
</table>
</div>
<?php

}

function last_forum_msg() {

	$conf_db_user = 'dWh2eGJweHVyaQ==';
	$conf_db_pass = 'MzQzNXxkZmRmcnU=';
	
	$user = decode($conf_db_user);
	$pass = decode($conf_db_pass);

	$conex = mysql_connect('hl117.dinaserver.com', $user, $pass);
	if (!$conex)
		die(msg_unable_db_connect .' '. mysql_error($conex));
	else {
		if(!mysql_select_db('rocayaphpbb',$conex)) {
			die(msg_unable_db_connect .' '. mysql_error($conex));
		}
	}
	
	$sql = 'SELECT post_id, t.topic_id, post_subject, post_text , topic_title, u.username, p.forum_id
		FROM posts p
		INNER JOIN topics t ON p.topic_id = t.topic_id
		INNER JOIN users u ON p.poster_id = u.user_id
		ORDER BY post_time DESC LIMIT 1';
	
	$select_post = my_query($sql, $conex);
	
	$record = my_fetch_array($select_post);
?>
<div class="standard_container default_text"> <span class="standard_cont_title">último mensaje en el foro</span><br />
  <?php
	$link = 'phpBB3/viewtopic.php?f='. $record['forum_id'] .'&t='. $record['topic_id'];
?>
  <span class="title_3"><a href="<?= $link; ?>">
  <?= $record['post_subject']; ?>
  </a></span> &ndash; por
  <?= $record['username']; ?>
  <br />
  <?= substr($record['post_text'], 0, 250); ?>
  <br />
  <br />
</div>
<?php
}


# build a submodules array and change deppending on the user being logged on.
# the array submodules has left and right columns or only right column

# each submodule is an element of an array where the index is not important and the content is the name of the function that displays the content
# also the content can be an array when more than one column is occupied.

if($_SESSION['Login']['UserID'] == $conf_generic_user_id) {		# Not logged user
	$submodules = array('left_col' => array('random_pic' => 'disp_randome_picture'
										   ,'web_stats' => 'disp_web_stats'
										   //,'latest_news' => 'disp_last_news'
										   )
					   ,'right_col' => array('last_report' => array('func' => 'disp_last_report', 'cols' => '2')
											//,'mobile_apps' => array('func' => 'mobile_apps_small', 'cols' => '2')
											,'routes_find' => 'disp_routes_finder'
											,'app' => 'social_networks'
											//,'video' => array('func' => 'disp_video', 'cols' => '2')
											//,'legend' => array('func' => 'disp_legend', 'cols' => '2')
											)
						);
}
else {		# Logged user
	$submodules = array('left_col' => array('random_pic' => 'disp_randome_picture'
										   ,'last_climb' => 'disp_last_climb'
										   ,'web_stats' => 'disp_web_stats'
										   //,'latest_news' => 'disp_last_news'
										   )
					   ,'right_col' => array('last_report' => array('func' => 'disp_last_report', 'cols' => '2')
											//,'mobile_apps' => array('func' => 'mobile_apps_small', 'cols' => '2')
											,'routes_find' => 'disp_routes_finder'
											,'app' => 'social_networks'
											//,'video' => array('func' => 'disp_video', 'cols' => '2')
											//,'legend' => array('func' => 'disp_legend', 'cols' => '2')
											)
						);
}
?>
<form name="home_form" method="post" action="">
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <?php if($submodules['left_col']) { ?>
      <td width="33%" valign="top"><?php 
	# Left column ----------------------------------
foreach($submodules['left_col'] as $id => $content) {
	if(is_array($content)) {
		$content['func']($content['cols']);
	}
	else {
		$content();
	}
}


?></td>
      <?php }  //if($submodules['left_col']) { ?>
      <td width="66%" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <?php
	# Right column ---------------------------------
$num_blocks = 0;
foreach($submodules['right_col'] as $id => $content) {
	if(is_array($content)) {
		if($content['cols'] == '2') {
			$num_blocks++;
			echo '<td colspan="2" valign="top">';
		}
		else {
			echo '<td width="50%" valign="top">';
		}
		
		$content['func']($content['cols']);
	}
	else {
		echo '<td width="50%" valign="top">';
		$content();
	}
	
	echo '</td>';

	$num_blocks++;
	if($num_blocks % 2 == 0) echo '</tr><tr>';
}
if($num_blocks % 2 != 0) echo '<td>&nbsp;</td>';
?>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
<script language="javascript" src="inc/ajax.js"></script>
<script language="javascript" src="inc/photos.js"></script>
<script language="javascript">

function jump_prov () {
	url = 'inc/ajax.php?content=crags_combo&detail='+ document.home_form.provinces.value;
	getData(url, 'crags_combo_container');
}

function jump_crag () {
	url = 'inc/ajax.php?content=sectors_combo&detail='+ document.home_form.crags_combo.value;
	getData(url, 'sectors_combo_container');
}

function jump2search() {
	document.home_form.action = '<?php echo $conf_main_page, '?mod='. $_GET['mod'] .'&view=search_results'; ?>';
	document.home_form.submit();
}

// --------------------- RANDOM PHOTOS --------------------
var paused = false;
var num_rotations = 0;
document.onload = display_random_photo();

function display_random_photo() {
	if(!paused && num_rotations < 60) {
		url = 'inc/ajax.php?content=random_photo&mod=<?= $_GET['mod']; ?>';
		getData(url, 'rotating_photos_container');
	}
	num_rotations++;	
	rotate_photo();
}

function rotate_photo() {
//	if(!paused) 
		window.setTimeout(display_random_photo, 10000);
/*	else
		return;*/
}

function pause_rotation() {
	paused = true;
}

function resume_rotation() {
	if(num_rotations < 60) {
		paused = false;
//		window.setTimeout(display_random_photo, 4000);
	}
}

// --------------------- Show QR Code --------------------
function show_qr(app_id) {
	document.getElementById('a_'+ app_id).href = 'JavaScript:hide_qr(\''+ app_id +'\')';
	document.getElementById('qr_'+ app_id).style.display = 'block';
}

function hide_qr(app_id) {
	document.getElementById('a_'+ app_id).href = 'JavaScript:show_qr(\''+ app_id +'\')';
	document.getElementById('qr_'+ app_id).style.display = 'none';
}

</script>
