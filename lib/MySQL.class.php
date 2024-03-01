<?php

/**
 * 2013.11.11 - Jesse L Quattlebaum (psyjoniz@gmail.com) (https://github.com/psyjoniz/code_sample__MySQL)
 * A class handling MySQL functionality.
 * 2022.08.12 - Jesse L Quattlebaum (psyjoniz@gmail.com)
 * Updated for use with dev.psyjoniz.com
 */

class MySQL {

	protected static $oInstance;
	public           $sDBHost = 'localhost';
	public           $sDBUser = 'bsgoserver_api';
	public           $sDBPass = 'W86N$99nTIYJ';
	public           $sDBData = 'bsgo_api';
	private          $dbHand  = false;
	private          $sSQL    = '';
	private          $qResults;
	private          $iInsertId;

	/**
	 * constructor with a-la-carte config
	 *
	 * @param array $aConfig allowing overide of hardcoded configuration parameters in an a-la-carte fasion for the following : sDBHost, sDBUser, sDBPass, sDBData and sSQL
	 *
	 * @return boolean|array
	 */
	function __construct($aConfig = null) {
		if(isset($aConfig['sDBHost']))
			$this->sDBHost = $aConfig['sDBHost'];
		if(isset($aConfig['sDBUser']))
			$this->sDBUser = $aConfig['sDBUser'];
		if(isset($aConfig['sDBPass']))
			$this->sDBPass = $aConfig['sDBPass'];
		if(isset($aConfig['sDBData']))
			$this->sDBData = $aConfig['sDBData'];
		$this->connect();
		if(isset($aConfig['sSQL']))
			return $this->query($aConfig['sSQL']);
		return true;
	}

	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance = new self();
		return self::$oInstance;
	}

	public static function _() { return self::getInstance(); }

	/**
	 * connect to database
	 *
	 * @return boolean|resource
	 */
	function connect()
	{
		if(!$this->dbHand)
		{
			if(!$this->dbHand = mysqli_connect($this->sDBHost, $this->sDBUser, $this->sDBPass))
			{
				throw new Exception('ERROR:DB_CONNECTION_FAILED');
				throw new Exception('ERROR:DB_CONNECTION_FAILED:"' . $this->sDBUser . '@`' . $this->sDBHost . '`.`' . $this->sDBData . '`" (mysqli_error() : ' . mysqli_error($this->dbHand) . ')');
			}
			if(!mysqli_select_db($this->dbHand, $this->sDBData))
			{
				throw new Exception('ERROR:DB_SELECTION_FAILED');
				throw new Exception('ERROR:DB_SELECTION_FAILED:"' . $this->sDBUser . '@`' . $this->sDBHost . '`.`' . $this->sDBData . '`" (mysqli_error() : ' . mysqli_error($this->dbHand) . ')');
			}
		}
		return $this->dbHand;
	}

	/**
	 * nothing fancy
	 *
	 * @param string $sInput text to be escaped
	 *
	 * @return string
	 */
	function escapeString($sInput) {
		return mysqli_real_escape_string($this->dbHand, $sInput);
	}

	/**
	 * talk with the database
	 *
	 * @param string $sSQL query statement to send to the database
	 * @param boolean $bReturnResults switch to return or not return found results
	 *
	 * @return boolean|array
	 */
	function query($sSQL, $bReturnResults = true) // lightweight db handling -- great for gotta-have-it-now horrible for after it starts taking off and you gotta *cough* expand
	{
		$aSQLParts = explode(' ', preg_replace('/\s+/', ' ', trim($sSQL)));
		$sSQLStart = strtolower($aSQLParts[0]);
		$this->connect();
		if(!$this->dbHand)
		{
			throw new Exception('ERROR:DB_CONNECTION_FAILED');
			throw new Exception('ERROR:DB_CONNECTION_FAILED:"' . $this->sDBUser . '@`' . $this->sDBHost . '`.`' . $this->sDBData . '`"');
		}
		$this->qResults = mysqli_query($this->dbHand, $sSQL);
		if(false === $this->qResults)
		{
			throw new Exception('ERROR:DB_QUERY_FAILED');
			throw new Exception('ERROR:DB_QUERY_FAILED:"' . mysqli_error($this->dbHand) . '" -- SQL:"' . $sSQL . '"');
		}
		$this->iInsertId = @mysqli_insert_id($this->dbHand);
		if($bReturnResults) // only build and return results if we were looking for them
		{
			if($sSQLStart != 'insert' && $sSQLStart != 'update' && $sSQLStart != 'delete')
			{
				$aResults = array();
				//if(gettype($this->qResults) == 'mysqli_result')
					while($aResult = mysqli_fetch_array($this->qResults, MYSQLI_ASSOC))
						$aResults[] = $aResult;
				//if(count($aResults) == 0)
					//throw new Exception('NOTICE:NO_RESULTS_FOUND');
			}
			if(isset($aResults)) {
				return $aResults;
			}
		}
		return true;
	}

	/**
	 * get the most recent inserted records id
	 *
	 * @return integer
	 */
	function getInsertId() {
		return $this->iInsertId;
	}
}

