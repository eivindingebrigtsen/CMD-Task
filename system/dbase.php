<?php
/**           
* dBase class
*
* Wrapper function for database operations
*
* @version      0.1
* @copyright    Copyright (C) 2009-2010 Eivind Ingebrigtsen & Roger C.B. Johnsen
* @author       Eivind Ingebrigtsen <eivindingebrigtsen@gmail.com>
* @author       Roger C.B. Johnsen <rogercbjohnsen@gmail.com>
* @license      MIT
* @package      Site
*/

class dBase extends mysqli {
	public static $num_rows;
	public static $queries;
	public static $totalQueryTime;
	/**
	 * Configuration storage
	 * @var Array
	 */
	private $configuration;

	public function __construct() {
	    $this->configuration = $this->loadConfiguration();
	    /**
	     * Constructs dBase
	     * @see dBase
	     * @return mysqli connection
	     */
	    parent::__construct(
	                $this->configuration['dbhostname'],
	                $this->configuration['dbusername'],
	                $this->configuration['dbpassword'],
	                $this->configuration['dbtable']
					);
	    if (mysqli_connect_error()) {
	        die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
	    }
	}

	private function loadConfiguration() {
	    $config_obj = Config::getInstance();

	    $config = $config_obj->getSection('DATABASE');
	    if($config) {
	        return $config;
	    } else {
	        return false;
	    }
	}

	/**
	 * Gets the executed SQL's and their times
	 * @see DEBUG must be true in config
	 * @return   #FB::table();
	 */
	public function getDebug(){
	    $table = array();
	    $table[] = array('SQL Query', 'Execution Time (s)');
	    $sqls = self::$queries;
	    $count = count($sqls);

	    for($i=0;$i<$count; $i++){
	            $table[] = $sqls[$i];
	    }

	    // reset
	    self::$queries = array();

	    // Output
	    $title = $count.' SQL Executed in '. self::$totalQueryTime .' sec';
	    #FB::table($title, $table);
	}

	/**
	 * Gets the executed SQL's totalQueryTime
	 * @return totalQueryTime in milliseconds
	 */
	public function totalTime(){
	    return self::$totalQueryTime;
	}
	/**
	 * Wrapper function for all queries
	 * Logs all sql's and their execution time
	 * @return   $results from mysqli_query()
	 */
	public function query ( $query ) {
	    $startTime = microtime();
	    $result = parent::query ( $query );
	    $endTime = microtime();
	    $execTime = $endTime - $startTime;
	    // Increment the total query time
	    self::$totalQueryTime += $execTime;
	    if ( $result ) {
	        // Notice that for each query we record the query string itself and the time it took to execute
	        self::$queries[] = array ( $query, $execTime );
	    } else {
	        #FB::error ( $query, 'Error in Query: ' . mysqli_error ( $this ) );
	        #FB::trace ( 'Stack Trace' );
	    }
    
	    return $result;
	} 
}
?>
