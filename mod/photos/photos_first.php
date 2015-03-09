<?php

$error_create_gallery = false;

if($_GET['func'] == 'accept') {
	# Check if a gallery already exists for the user:
	$exists_gallery = exists_record('galleries', array(object_id, object_type), array($_SESSION['Login']['UserID'], 'user'));
	
	if(!$exists_gallery) {
		$gname = 'Galería del usuario '. $_SESSION['Login']['User_Name'];
		$description = 'Galería de fotos de '. $_SESSION['Login']['User_Name'];
		$filter_sql = 'object_id = \\\''. $_SESSION['Login']['UserID'] .'\\\' AND object_type = \\\'user\\\'';
		$object_type = 'user';
		$object_id = $_SESSION['Login']['UserID'];
//			$url_object = '?mod=photos&view=view_gallery&detail=
		$sql = 'INSERT INTO galleries (gname, description, filter_sql, object_type, object_id)
		VALUES (\''. $gname .'\', \''. $description .'\', \''. $filter_sql .'\', \''. $object_type .'\', \''. $object_id .'\')';
		
		$insert_gallery = my_query($sql, $conex);
		if($insert_gallery) {
?>
<script language="javascript">
document.location = '<?php echo $conf_main_page; ?>?mod=photos&view=upload_photos';
</script>
<?php			
			exit();				
		}
		else {
			$error_create_gallery = true;
		}
	}
	else {
?>
<script language="javascript">
document.location = '<?php echo $conf_main_page; ?>?mod=photos&view=upload_photos';
</script>
<?php			
			exit();				

	}
}

?>
<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><div class="standard_container default_text"> <span class="standard_cont_title">Subir fotos a Rocaya.com</span><br />
<?php
if($error_create_gallery) {
?>
		<span class="error_message title_3">Ha habido un error al crear la galería de usuario, contacta con info@rocaya.com para resolver el problema<br />
Gracias</span><br />
<?php } ?>
        <span class="title_3">Para subir fotos a Rocaya.com debes leer y aceptar las condiciones siguientes:</span><br /><br />

        <table width="66%" align="center" border="1" cellpadding="10" cellspacing="0">
          <tr>
            <td>Términos y Condiciones par subir fotos a Rocaya.com:<br /><br />
            <ul class="standard_bullet_list">
<li>La sección de fotos de Rocaya.com está pensada para que los usuarios puedan compartir sus fotos.</li>
<li>Las fotos serán visibles por cualquier visitante de Rocaya.com tanto por usuarios registrados como por cualquier otro visitante.</li>
            <li> Cualquier los usuarios registrados podrán escribir comentarios sobre las fotos y valorarlas según su gusto.</li>
            <li> Al subir fotos a Rocaya.com el usuario retiene los derechos de autoría sobre estas.</li>
            <li> No se permite subir fotos con derechos protegidos o cuyos autores no permitan que estas se compartan.</li>
            <li> El equipo de Rocaya.com se reserva el derecho de eliminar cualquier fotografía que considere inapropiada de nuestro servidor.</li>
            </ul>
            <p align="center">
              <input type="button" name="accept" id="accept" value="Aceptar Términos y Condiciones" onclick="JavaScript:accept_terms();" />
            </p></td>
          </tr>
        </table><br /><br />
      </div></td>
  </tr>
</table>
<script language="javascript">
function accept_terms() {
	document.location = document.location + '&func=accept';
}
</script>