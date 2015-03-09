<?php

# Includes
	
	include 'config2.php';
	include 'comm.php';
	include $conf_oops_subpath .'robjects.php';
	include $conf_oops_subpath .'comm_objects.php';
	include 'connect.inc';

	$sql = 'SELECT p.photo_id, p.date_uploaded, p.rating, count(c.comment_id) as num_comments 
	FROM photos p 
	LEFT JOIN comments c 
	ON c.object_id = p.photo_id  AND c.object_type = \'photo\' AND c.flag_master <> \'1\'
	WHERE p.flag_master <> \'1\'
	GROUP BY p.photo_id, p.date_uploaded, p.rating';
	
	$select_photos = my_query($sql, $conex);
	
	while($record = my_fetch_array($select_photos)) {
		# calculate the months since the photo was updated.
		$current_months =  date('Y') * 12 + date('m');
		$photo_months = substr($record['date_uploaded'], 0, 4) * 12 + substr($record['date_uploaded'], 5, 2);
		$diff_months = $current_months - $photo_months;
		
		# calculate rating
		$rank = 2 * $record['rating'];
		
		# num comments
		$num_comments = $record['num_comments'] > 5 ? 5 : $record['num_comments'];
		
		$auto_ranking = $num_comments + $rank - $diff_months;
		
		echo 'photo '. $record['photo_id'] .':'. $auto_ranking;
		$sql = 'UPDATE photos SET auto_ranking = '. $auto_ranking .' WHERE photo_id = \''. $record['photo_id'] .'\'';
		
		$upd_sql = my_query($sql, $conex);
		
		if($upd_sql)
			echo ' - ok
			';
		else
			echo ' - ko!!
			';
	}
?>