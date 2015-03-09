<?php

# Copy $_GET into $_POST as before we used post and now we use get.
$_POST = $_GET;

if($_POST['retry'] == 'true') $_POST['retry'] = '1';
else $_POST['retry'] = '0';

// check dates
//if(is_numeric(substr($_GET['date'], 0, 4))
	
	$sql = 'SELECT count(*) AS num_records FROM users_routes WHERE user_id = '. $_SESSION['Login']['UserID'] .' AND route_id = '. $_POST['route_id'];
	$sql_select = my_query($sql, $conex);
	$exist = @my_result($sql_select, 0, 'num_records');
	
	if($exist) { // is an update
		$sql = 'UPDATE users_routes SET 
  climb_type = \''. $_POST['climb_type'] .'\'
, climb_date = \''. $_POST['climb_date'] .'\'
, num_tries = \''. $_POST['num_tries'] .'\' 
, comments = \''. $_POST['comments'] .'\' 
, retry_date = \''. $_POST['retry_date'] .'\' 
, retry = \''. $_POST['retry'] .'\' 
 WHERE user_id = '. $_SESSION['Login']['UserID'] .' AND route_id = '. $_POST['route_id'];
		$sql_update = my_query($sql, $conex);
		
		if($sql_update)
			print('<img src="'. $conf_images_path .'/processing.gif" alt="Saving" border="0" />');
		else
			print('X');
	}
	else {
		
		$sql = 'INSERT INTO users_routes (user_id, route_id, climb_date, climb_type, num_tries, comments, retry_date, retry)
		VALUES (
		\''. $_SESSION['Login']['UserID'] .'\',  \''. $_POST['route_id'] .'\',  \''. $_POST['climb_date'] .'\',  \''. $_POST['climb_type'] .'\',  
		\''. $_POST['num_tries'] .'\',  \''. $_POST['comments'] .'\',  \''. $_POST['retry_date'] .'\',  \''. $_POST['retry'] .'\')';
		
		$sql_insert = my_query($sql, $conex);
		
		if($sql_insert)
			print('<img src="'. $conf_images_path .'/processing.gif" alt="Saving" border="0" />');
		else
			print('X');
	}
?>