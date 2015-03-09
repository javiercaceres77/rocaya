<?php

$is_user_admin = simple_select('users', 'user_id', $_SESSION['Login']['UserID'], 'isadmin');

if($is_user_admin) {
?>
<script language="javascript">
function add_img2db() {
	var option = '';
	if(document.getElementById('empty_table').checked)
		option = 'trunc';
	document.location = '<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=add_imgs2db&option='; ?>'+ option;
}
</script>
<div class="standard_container default_text"> <span class="standard_cont_title">Módulo de administración</span><br />
  <form action="" method="post" name="admin_form" id="admin_form">
    <ul>
<?php if($_SESSION['Login']['UserID'] == '12') {  // Javi ?>
      <li><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=upd_sectors_url_ids'; ?>">Actualizar url_ids en tabla de sectores</a></li>
      <li><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=upd_crags_url_ids'; ?>">Actualizar url_ids en tabla de escuelas</a></li>
<?php } ?>      
      <li><a href="JavaScript:add_img2db();">Añadir imágenes en el directorio &ldquo;
        <?= $conf_images_path ?>
        &rdquo; a la base de datos;&nbsp;&nbsp;&nbsp;</a>
        <label>
        <input type="checkbox" name="empty_table" id="empty_table" />
        Vaciar tabla imágenes antes</label>
      </li>
      <li><a href="<?php echo $conf_main_page .'?mod='. $_GET['mod'] .'&view=manage_routes'; ?>">Gestionar rutas</a></li>
    </ul>
  </form>
</div>
<?php
}	// is user admin
?>
