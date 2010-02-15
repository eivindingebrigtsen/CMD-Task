<?php
class Time {
	const MINUTE = 60;
	const HOUR = 3600; // 60 * 60 
	const DAY = 86400; //  60 * 60 * 24 
	const WEEK = 604800; // 60 * 60 * 24 * 7
	const MONTH = 2592000; // 60 * 60 * 24 * 30
	const YEAR = 31536000; // 60 * 60 * 24 * 365
  	public function __construct() {
   	}
	public function getDates($dates){
		if(is_array($dates)){
			$arr = '[';
			$last_item = end($dates);
			foreach($dates as $date){
				$arr .= '"'.self::getDate($date).'"';
				if($date!=$last_item){
					$arr .= ',';
				}
			}
			$arr .= ']'; 
			return '{
				"status": "success",
				"dates": '.$arr.'
			}';
		}else{
			self::getDateFromString($dates);
		}
		
	}
	public function getDate($str){
		if(is_numeric($str)){
		   return strftime('%d %B %G', $str); 
		}
		if(strtotime($str)){
		   return  date('d M Y', strtotime($str));
		}else{
		   return 'Didn\'t compute: '.$str; 
		}
	}
	public function getDateFromString($str){
		$date = '';
		if($str){           
			$date = self::getDate($str);
			return '{
				"status": "success",
				"date": "'.$date.'"
			}';
		}else{
			return '{
				"status": "error"
			}';
		}
	}                 
	public function strToTime($str){
		if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
		@date_default_timezone_set(@date_default_timezone_get());
		return strtotime($str);
	}
	public function timeTo($date){
		$time = time();
		$dateDiff = ($date+self::DAY)-$time;
		$diff = array();
		$diff['years'] = floor($dateDiff/self::YEAR);   
		$diff['months'] = floor($dateDiff/self::MONTH);   
		$diff['days'] = floor($dateDiff/self::DAY);   
	    $diff['hours'] = floor(($dateDiff-($diff['days']*self::DAY))/self::HOUR);   
	    $diff['min'] = floor(($dateDiff-($diff['days']*self::DAY)-($diff['hours']*self::HOUR))/self::MINUTE);
		if($diff['months']>1){
			$due = strftime('%d %B %G', $date);					
		}else{
			$due = '';
			if($diff['days']){
				$due .= $diff['days'].' days, ';
			}else{
				if($diff['hours']){
					$due .= $diff['hours'].' hours, ';
				}
				if($diff['min']){
					$due .= $diff['min']. ' minutes';
				}							
			}
		}
		return $due;		
	}
	public function timeSince($date) {  
		if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
		@date_default_timezone_set(@date_default_timezone_get());

	    $since = abs(strtotime('now') - $date);

	    if($since > self::YEAR) {
	        $year = floor($since / self::YEAR);
	        return "more than $year year(s) ago";
	    }    

	    if ($since > self::MONTH) {
	        $month = floor($since / self::MONTH);
	        return "about $month month(s) ago";
	    } 

	    if ($since > self::WEEK) {
	        $week = floor($since / self::WEEK);
	        $day = floor(($since - ($week * self::WEEK)) / self::DAY);
	        return "about $week week(s), and $day day(s) ago";
	    }

	    if ($since > self::DAY) {        
	        $day = floor($since / self::DAY);
	        $hour = floor(($since - ($day * self::DAY)) / self::HOUR);
	        return "about $day day(s), $hour hour(s) ago";
	    }

	    if ($since > self::HOUR) {        
	        $hour = floor($since / self::HOUR);
	        $minute = floor(($since - ($hour * self::HOUR)) / self::MINUTE);
	        return "about $hour hour(s), $minute minute(s) ago";
	    }

	    if ($since > self::MINUTE) {        
	        $minute = floor($since / self::MINUTE);
	        return "$minute minute(s) ago";
	    }
	    return "under 1 minute ago";	
	}
}
?>       




