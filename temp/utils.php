<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Utilidades</title>
</head>
<?php
include '../inc/config.php';
include '../inc/comm.php';
?>
<body>
<form id="form1" name="form1" method="post" action="">
  <p>encode
    <input name="encode" type="text" id="encode" value="<?php echo $_POST['encode'] ?>" /> <?php if($_POST['encode']) print(encode($_POST['encode'])); ?>
</p>
  <p>decode
    <input name="decode" type="text" id="decode" value="<?php echo $_POST['decode'] ?>" /> <?php if($_POST['decode']) print(decode($_POST['decode'])); ?>
</p>
  <p>encrypt
    <input name="encrypt" type="text" id="encrypt" value="<?php echo $_POST['encrypt'] ?>" /> 
  <?php if($_POST['encrypt']) print(digest($_POST['encrypt'])); ?>
  </p>
  <p>
    <input type="submit" name="Submit" value="Enviar" />
  </p>
</form>
<hr width="90%" align="center">
<form action="utils.php" method="post" name="form1">
<p>Introduce el n￿ de DNI: <input type="text" name="dni" id="dni"> 
  <label>
  <input type="submit" name="Submit" value="Calcular NIF">
  </label>
</p>
<?php 
$my_nif = array('T','R','W','A','G','M','Y','F','P','D','X','B','N','J','Z','S','Q','V','H','L','C','K','E','O');

if($_POST['dni'])
	print('<p>'. $_POST['dni'] .' - '. $my_nif[$_POST['dni'] % 23]);
?>
</form>
<hr width="90%" align="center">
<form name="form2" action="utils.php" method="post">
<p>Introduce un n￿ de cuenta para obtener el d￿to de control:</p>
<p>
  Entidad: 
  <input name="ent" type="text" id="ent" size="5" maxlength="4" value="<?php echo $_POST['ent']; ?>">
  Oficina:
  <input name="ofi" type="text" id="ofi" size="5" maxlength="4" value="<?php echo $_POST['ofi']; ?>">
  N&uacute;mero de cuenta:  
  <input name="cuenta" type="text" id="cuenta" size="12" maxlength="10" value="<?php echo $_POST['cuenta']; ?>">
  <input type="submit" name="Submit2" value="Calcular DC">
<?php

function digito_control($ent_ofi, $num_cuenta) {
	$arr_pesos = array(1,2,4,8,5,10,9,7,3,6);
	$dc1 = 0;	// sale del c￿o de entidad y oficina
	$dc2 = 0;	// sale del n￿ de cuenta
	$i = 8;
	
	while($i > 0) {
		$digito = $ent_ofi[$i - 1];
		$dc1+= $arr_pesos[$i + 1] * $digito;
		$i--;
	}
	
	$resto = $dc1 % 11;
	$dc1 = 11 - $resto;
	if($dc1 == 10) $dc1 = 1;
	if($dc1 == 11) $dc1 = 0;
	
	$i = 10;
	
	while($i > 0) {
		$digito = $num_cuenta[$i - 1];
		$dc2+= $arr_pesos[$i - 1] * $digito;
		$i--;
	}
	
	$resto = $dc2 % 11;
	$dc2 = 11 - $resto;
	if($dc2 == 10) $dc2 = 1;
	if($dc2 == 11) $dc2 = 0;
	
	return $dc1 . $dc2;
}


//Ahora ya tenemos la funci￿echa.
//Para que funcione simplemente la llamaremos de la siguiente forma pasandole los parametros del formulario: 
if($_POST['ent'] && $_POST['ofi'] && $_POST['cuenta']) {
	$parte1 = $_POST['ent'] . $_POST['ofi'];
	$parte2 = $_POST['cuenta'];
	$ccc='';
	
	$dc = digito_control($parte1, $parte2);
	
	print('<br>'. $_POST['ent'] .'/'. $_POST['ofi'] .'/<b>'. $dc .'</b>/'. $_POST['cuenta']);
}
?>
</p>
</form>
</body>
</html>
