<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top" width="33%"><div class="standard_container">
      <span class="standard_cont_title">Aplicaciones móviles</span>
      <?php

	$sql = 'SELECT app_id, desc_id, app_name, picture, price, platform FROM mobile_apps WHERE active = \'1\' ORDER BY platform, sort_order';
	$select_apps = my_query($sql, $conex);
	
	$platform = '';
	while($record = my_fetch_array($select_apps)) {
		if($platform != $record['platform']) {
			$platform = $record['platform'];
			switch($platform) {
				case 'android':
				?>
      <img src="<?php echo $conf_images_path; ?>android_market.png" alt="Android Market" border="0" width="247" height="34" title="Android Market" /><br />
      <span class="small_text">Las aplicaciones de rocaya requiren Adobe AIR <img src="<?php echo $conf_images_path; ?>Adobe_AIR.png" alt="Adobe AIR icon" align="absmiddle" border="0" width="16" height="16" title="Adobe AIR" /> Este se instala gratuitamente al abrir cualquiera de nuestras aplicaciones la primera vez.</span>
      <?php
				break;
				case 'apple':
				?>
      <img src="<?php echo $conf_images_path; ?>app_store.png" alt="App Store" border="0" width="247" height="33" title="App Store" /><br />
      <span class="small_text">La applicación de Rocaya para iPhone todavía no está disponible en el App Store. Sin embargo, puedes descargar e instalar directamente la aplicación desde aquí.</span>
      <?php
				break;
			}	
		}
?>
      <div class="standard_container" style="cursor:pointer; background-color:#ebebeb" onclick="JavaScript:document.location='<?= $conf_main_page .'?mod='. $_GET['mod'] .'&detail='. $record['desc_id']; ?>'">
        <table width="100%" align="center" border="0" cellpadding="2">
          <tr>
            <td width="77px"><img src="<?= $conf_images_path . $record['picture']; ?>" width="72" height="72" /></td>
            <td><span class="title_3"> <a href="<?= $conf_main_page .'?mod='. $_GET['mod'] .'&detail='. $record['desc_id']; ?>">
              <?= $record['app_name']; ?>
              </a> </span><br />
              <span class="default_text">
              <?= $record['price']; ?>
              </span> </td>
          </tr>
        </table>
      </div>
      <?php
	}
?>
    </td>
    <td valign="top"><div class="standard_container">
        <?php

if($_GET['detail']) {
	$sql = 'SELECT * FROM mobile_apps WHERE desc_id = \''. $_GET['detail'] .'\' AND active = \'1\'';
}
else {
	# select the first on the list of apps
	$sql = 'SELECT * FROM mobile_apps WHERE active = \'1\' ORDER BY sort_order';
}

$sel_app = my_query($sql, $conex);
	
$record = my_fetch_array($sel_app);

	?>
        <span class="standard_cont_title">
        <?= $record['app_name']; ?>
        </span><br />
        <table width="100%" border="0" cellpadding="2" cellspacing="2">
          <tr>
            <td valign="top"><img src="<?= $conf_images_path . $record['picture']; ?>" width="72" height="72" /></td>
            <td class="default_text" valign="top"><?= $record['description']; ?>
              <br />
              <br />
              <a href="<?= $record['link']; ?>" target="_blank">
              <?= $record['link']; ?>
              </a></td>
            <td valign="top"><img src="<?= $conf_images_path . $record['qr_code']; ?>" /></td>
          </tr>
        </table><br />
        <table width="100%" border="0" cellpadding="2" cellspacing="2">
          <tr>
            <td><img src="<?= $conf_images_path . $conf_apps_subpath . $record['screenshot01']; ?>" width="600" height="360" /><br />
              <br />
              <img src="<?= $conf_images_path . $conf_apps_subpath . $record['screenshot02']; ?>" width="600" height="360" /><br />
              <br />
              <img src="<?= $conf_images_path . $conf_apps_subpath . $record['screenshot03']; ?>" width="600" height="360" /></td>
          </tr>
        </table><br />
      </div></td>
  </tr>
</table>
