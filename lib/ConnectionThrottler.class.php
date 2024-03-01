<?php

require_once('MySQL.class.php');

class ConnectionThrottler {

	protected static $oInstance;
	private          $oDB;

	function __construct() {
		$this->oDB = MySQL::_();
	}

	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance = new self();
		return self::$oInstance;
	}

	public static function _() { return self::getInstance(); }

}

