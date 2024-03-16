<?php

class APIResponse {
	protected static $oInstance;
	private          $aData = [];
	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance = new self();
		return self::$oInstance;
	}
	public static function _() { return self::getInstance(); }
	public function __get($sKey) {
		if(array_key_exists($sKey, $this->aData)) {
			return $this->aData[$sKey];
		}
		return null;
	}
	public function __set($sKey, $mVal) {
		$this->aData[$sKey] = $mVal;
	}
	public function compile() {
		return $this->aData;
	}
}

