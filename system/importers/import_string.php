<?
/**
* String Import
*/
class StringImport extends ImportThingy
{
	private static $regtask 		= '/(\*(^\/)|•|-)(.*)(?=\n|\r|\@|\||\#|\/\*|\*\/)/';
	private static $regcontext 		= '/\@(\w{1,64})/';
	private static $regurls			= '/(?=http:\/\/|ftp:\/\/|https:\/\/|www\.)(.*?)\s/';
	private static $regtag			= '/\#(\w{1,64})/';
	private static $regproject 		= '/\|(\w{1,64})/';
	private static $reglist 		= '/\s\/(\w{1,64})/';
	private static $regcomment		= '/(\/\*[\d\D]*?\*\/|\/\/.*?)/';	
	private static $regtime 		= '/(mon|tue|wed|thu|fri|sat|sun|sunday|monday|tuesday|wednesday|thursday|friday|saturday|yesterday|tomorrow|today|now|\+[0-9]\sday|\+[0-9]\sweek|\+[0-9]\smonth|\+[0-9]\syear|((31(?! (FEB|APR|JUN|SEP|NOV)))|((30|29)(?! FEB))|(29(?= FEB (((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)))))|(0?[1-9])|1\d|2[0-8]) (JAN|FEB|MAR|MAY|APR|JUL|JUN|AUG|OCT|SEP|NOV|DEC) ((1[6-9]|[2-9]\d)\d{2}))/';
	function __construct()	
	{
	}
	public function interpret($string){		
	   	$matches = array();
		$matches['tasks'] 		= $this->getMatches($string, self::$regtask);
		$matches['urls'] 		= $this->getMatches($string, self::$regurls);
		$matches['context'] 	= $this->getMatches($string, self::$regcontext);
		$matches['projects'] 	= $this->getMatches($string, self::$regproject);
		$matches['lists'] 		= $this->getMatches($string, self::$reglist);
		$matches['tags']		= $this->getMatches($string, self::$regtag);
		
		
		#FB::log($matches, 'Interpret Tasks');
	}
	public function getMatches($string, $reg){
		preg_match_all($reg, $string, $matches);
		return array_unique($matches);
		
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
