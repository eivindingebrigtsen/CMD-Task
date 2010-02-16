<?
/**
* String Import
*/
class StringImport extends ImportThingy
{
	private static $reg = array();
	private static $tasksymbols = '(\*|•|-)';
	function __construct(){
		self::$reg['task'] 			= '/'.self::$tasksymbols.'(.*)$/';
		self::$reg['urls']			= '/(?=http:\/\/|ftp:\/\/|https:\/\/|www\.)(.*?)\s/';
		self::$reg['contexts'] 	= '/@(\S{1,64})/';
		self::$reg['tags']				= '/\#(\S{1,64})/';
		self::$reg['projects'] 	= '/\|(\S{1,64})/';
		self::$reg['lists'] 			= '/(?<!\/)\/(?!\/)(\S{1,64})/';
		self::$reg['comment']		= '/(\/\*[\d\D]*?\*\/|\/\/.*?)(.*?)$/';	
		self::$reg['time'] 			= '/(mon|tue|wed|thu|fri|sat|sun|sunday|monday|tuesday|wednesday|thursday|friday|saturday|yesterday|tomorrow|today|now|\+[0-9]\sday|\+[0-9]\sweek|\+[0-9]\smonth|\+[0-9]\syear|((31(?! (FEB|APR|JUN|SEP|NOV)))|((30|29)(?! FEB))|(29(?= FEB (((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)))))|(0?[1-9])|1\d|2[0-8]) (JAN|FEB|MAR|MAY|APR|JUL|JUN|AUG|OCT|SEP|NOV|DEC) ((1[6-9]|[2-9]\d)\d{2}))/';
	}
	public function array_flatten($array,$return)
	{
		foreach($array as $key => $value){
				if(is_array($value))
				{
					$return = $this->array_flatten($value,$return);
				}
				else
				{
					if($value)
					{
						$return[$key] = $value;
					}
				}
			}
		return $return;
	}	

	public function interpret($string){		
		$string_array = preg_split('/(\n|\r)/', $string);
		$keywords = array();
		$items = 0;
		foreach($string_array as $line){
			$data = array();			
			$match = $this->tryMatch($line);
			FB::info($match,'MATCH FOR LINE');                                                                         	
			if(!array_key_exists('break', $match)){
				if(!array_key_exists('task', $match)){
					$keywords[] = $match;
				}else{                            
					$blurb = array_merge($keywords, $match);
					$data['nada'] 	= false;
					$data['raw'] 	= false;
					$data['text'] 	= false;
					$data['desc'] 	= null;
					$data['date'] 	= null;
					$data['keys'] 	= false;
					$data['raw'] = $match['task'];
					$data['text'] = $match['title'];
					if(array_key_exists('time', $match)){
						$data['date'] = Time::strToTime($match['time']);						
					}
					$data['keys'] = $blurb;
		   		#FB::info($data,'TASK DATA'); 
					TaskerHelper::addItem($data);
					$items++;
				}
			}else{
				$pop = array_pop($keywords);
			}
		FB::log($data, 'DATA');
		}
		FB::log($keywords, 'Keywords');
		
		FB::log($matches, 'Interpret Tasks');
	}
	
	
	
	public function getMatches($string, $reg){
		preg_match_all($reg, $string, $matches);
		return array_unique($matches);
		
	}
	public function tryMatch($string){
		$matches = array();
		$matched = false;
		foreach(self::$reg as $key => $exp){
			$match = $this->getMatches($string, $exp);
			if(!empty($match[0][0])){
				$matched = true;
				if($key == 'time'){
					$matches[$key] = $match[0][0];					
				}else{
					$matches[$key] = substr($match[0][0], 1);
				}
				if($key == 'task'){
					FB::info($matches, 'TASK');
					$matches['title'] = $this->removeKeys($match[0][0]);
				}
			}
		}
		if(!$matched){
			$matches['break'] = true;
		}
		FB::log($matches, $string);
		return $matches;
	}
	public function removeKeys($string){
		$matches = array();
		foreach(self::$reg as $key => $exp){			
			if($key != 'task'){
				$string = $this->removeKey($string, $exp);				
			}else{
				$string = $this->removeKey($string, '/'.self::$tasksymbols.'/');
			}
			$matches[$key] = $string;
		}
		return trim($string);
	}
	public function removeKey($string, $exp){
		return preg_replace($exp, '', $string);
	}
	public function getReplaces($string, $reg){
		$replacements = array();
		$txt = preg_replace_callback(
			$reg, 
			create_function(
				'$replacements[] = $matches',
				'return $matches[0];'
			), 
			$string);
		return $replacements;
		
	}
}
