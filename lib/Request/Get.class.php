<?php

class Get {

	protected static $oInstance;
	private $aGet;

	function __construct() {
		$this->aGet = $_GET;
	}

	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance = new self();
		return self::$oInstance;
	}

	public static function _() { return self::getInstance(); }

	public function get($sKey) {
		return (isset($this->aGet[$sKey]) ? $this->aGet[$sKey] : null);
	}

}

