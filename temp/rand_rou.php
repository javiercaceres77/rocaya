<?php

$arr_nums = array();
$money = 10000;
$min = $money; $max = $money;
$bets = array(5, 8, 9, 22, 31);

for($i = 1; $i < 1000; $i++) {
	$num = rand(0, 36);

	if(in_array($num, $bets))
		$money+= 300;
	else
		$money-= 50;
		
	if($arr_nums[$num])
		$arr_nums[$num]++;
	else
		$arr_nums[$num] = 1;
	
	if($money > $max) $max = $money;
	if($money < $min) $min = $money;
	
	echo 'num: '. $num .'; money: '. $money .'<br>';
}

echo 'money: '. $money .'<br>max: '. $max .'<br>min: '. $min;

echo '<pre>';
print_r($arr_nums);
echo '</pre>';

?>