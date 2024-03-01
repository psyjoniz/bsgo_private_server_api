<?php

/**
 * 2013.12.01 - Jesse L Quattlebaum (psyjoniz@gmail.com) (https://github.com/psyjoniz/code_sample__Session)
 * A class for handling Session related data.
 */

class Session {

	protected static $oInstance;
	private $sNamespace = 'default';

	function __construct($sSessionId = null, $sNamespace = null) {
		if(null !== $sSessionId) {
			session_id($sSessionId);
		}
		if(!session_start()) {
			throw new Exception('Could not start session.');
		}
		if(null !== $sNamespace) {
			$this->sNamespace = $sNamespace;
		}
	}

	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance = new self();
		return self::$oInstance;
	}

	public static function _() { return self::getInstance(); }

	public function getSessionId() {
		return session_id();
	}

	public function set($sName = null, $mValue = null, $sNamespace = null) {
		if(!isset($_SESSION[(null !== $sNamespace ? $sNamespace : $this->sNamespace)]) || !is_array($_SESSION[(null !== $sNamespace ? $sNamespace : $this->sNamespace)])) {
			$_SESSION[(null !== $sNamespace ? $sNamespace : $this->sNamespace)] = array();
		}
		if(null === $sName) {
			throw new Exception('Invalid name supplied to Session.');
		}
		if(null === $mValue) {
			throw new Exception('Value not supplied to Session.');
		}
		$_SESSION[(null !== $sNamespace ? $sNamespace : $this->sNamespace)][$sName] = $mValue;
		return true;
	}

	public function get($sName, $sNamespace = null) {
		if(null === $sName) {
			throw new Exception('Invalid name supplied to Session.');
		}
		if(isset($_SESSION[(null !== $sNamespace ? $sNamespace : $this->sNamespace)][$sName])) {
			return $_SESSION[(null !== $sNamespace ? $sNamespace : $this->sNamespace)][$sName];
		}
		return false;
	}

	public function getAll($sNamespace = null) {
		return $_SESSION[(null !== $sNamespace ? $sNamespace : $this->sNamespace)];
	}

	public function remove($sName = null) {
		if(null === $sName) {
			throw new Exception('Invalid name supplied to Session.');
		}
		if(isset($_SESSION[$this->sNamespace][$sName])) {
			unset($_SESSION[$this->sNamespace][$sName]);
		}
	}

	public function removeAll() {
		foreach($_SESSION[$this->sNamespace] as $sName => $mValue) {
			$this->remove($sName);
		}
		unset($_SESSION[$this->sNamespace]);
	}

	public function reset() {
		foreach($_SESSION as $sName => $mValue) {
			unset($_SESSION[$sName]);
		}
	}

}

