<?php

class date_time {
	public $odate;	# yyyy-mm-dd	/	Y-m-d
	public $otime;	# hh:mm:ss		/	H-i-s
	public $year;
	public $month;
	public $day;
	private $language;
	
	public function __construct($date_time, $lan = '') {
	# $date_time format must be:		# separators can vary from - or :
	# yyyy-mm-dd hh:mm:ss				# yyyy-mm-dd
		$str_date = substr($date_time, 0, 10);
		$year = substr($str_date, 0, 4);
		$month = substr($str_date, 5, 2);
		$day = substr($str_date, 8, 2);
		if(checkdate($month, $day, $year)) {
			$this->odate = $year .'-'. $month .'-'. $day;
			$this->year = $year;	$this->month = $month;	$this->day = $day;
		}
		else
			$this->odate = '0000-00-00';
		
		$time = substr($date_time, 11, 8);
		if($time)
			$this->otime = $time;
		else 
			$this->otime = '00:00:00';
		
		if($lan != '')
			$this->language = $lan;
		else
			$this->language = 'es';
	}
	
	public function get_date() {
		return $this->odate;
	}
	
	public function get_time() {
		return $this->otime;
	}
	
	public function format_date($format = '') {
		# $format: '' -> 15-06-2006; med -> 15-jul-2006; long -> 15 de junio de 2006; very_long -> miércoles, 24 de julio de 2006; year_month -> jul 2011; month_day -> 24 jul
		# $format: '' -> 15-06-2011; med -> Jul-15-2011; long -> June 15th 2011; very_long -> wednesday, 24th of july 2011; year_month -> jul 2011; month_date -> Jul 24th 
		$method_name = 'format_date_'. $format;
		if(method_exists($this, $method_name))
			return $this->$method_name();
		else 
			return $this->format_date_med();
	}

	private function format_date_long() {
		switch($this->language) {
			case 'es':	#2006-07-24 -> 24 de julio de 2006
				$months_es = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
				return $this->remove_zeroes($this->day) .' de '. $months_es[$this->month - 1] .' de '. $this->year;
			break;
			case 'en':	#2006-07-24 -> July 24th 2006
				$my_mk = mktime(0, 0, 0, $this->month, $this->day, $this->year);
				return @date('F jS Y', $my_mk);
			break;
		}
	}

	private function format_date_med() {
		switch($this->language) {
			case 'es':	#15-jul-2006
				$months_es = array('ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic');
				return $this->day .'-'. $months_es[$this->month - 1] .'-'. $this->year;
			break;
			case 'en':	#Jul-15-2006
				$months_en = array('jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'agu', 'sep', 'oct', 'nov', 'dec');
				return ucfirst($months_en[$this->month - 1]) .'-'. $this->day .'-'. $this->year;
			break;
		}
	}
	
	private function format_date_month_day() {
		switch($this->language) {
			case 'es':	#24 jul
				$months_es = array('ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic');
				return $this->day .' '. $months_es[$this->month - 1];
			break;
			case 'en':	#Jul 24th
				$my_mk = mktime(0, 0, 0, $this->month, $this->day, $this->year);				
				return @date('F jS', $my_mk);
			break;
		}
	}
	
	private function format_date_year_month() {
		switch($this->language) {
			case 'es':	#jul 2011
				$months_es = array('ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic');
				return $months_es[$this->month - 1] .' '. $this->year;
			break;
			case 'en':	#Jul 24th
				$my_mk = mktime(0, 0, 0, $this->month, $this->day, $this->year);				
				return @date('F Y', $my_mk);
			break;
		}
	}
	
	public function format_time($format = '') {
		# $format '' -> 14:21;	full -> 14:21:22; ampm -> 2:21 pm
		switch($format) {
			case 'full':
				return $this->otime;
			break;
			case 'ampm':
				$my_mk = mktime(substr($this->otime, 0, 2), substr($this->otime, 3, 2), substr($this->otime, 6, 2));
				return date('g:i a', $my_mk);
			break;
			default:
				return substr($this->otime, 0, 5);
		}
	}
	
	private function remove_zeroes($value) {
		return $value += 0;
//		return preg_replace('~^[0]*([1-9][0-9]*)$~','$1',$value);
	}
	
	public function date_diff($date) {
		$this_mk = mktime(0, 0, 0, $this->month, $this->day, $this->year);
		$date_mk = mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));
		return $this_mk - $date_mk;
	}
	
	public function is_valid_date() {
		return $this->odate <> '0000-00-00';
	}
	
}

class user {
	public $user_id;
	private $user_name;
	private $user_level;
	private $is_admin;

	public function __construct($user_id, $user_name = '') {
		$this->user_id = $user_id;
		
		if($user_name == '') {
			$arr_usr = simple_select('users', 'user_id', $this->user_id, 'uname', ' AND active = \'1\'');
			$this->set_user_name($arr_usr['uname']);
		}
		else
			$this->set_user_name($user_name);
	}
	
	public function set_user_name($user_name) {
		$this->user_name = $user_name;
	}
	
	public function get_user_name() {
		return $this->user_name;
	}
	
	public function is_admin() {
		$arr_admin = simple_select('users', 'user_id', $this->user_id, 'isadmin');
		return $arr_admin['isadmin'] <> '0';
	}
}


?>