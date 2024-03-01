<?php

class Post {

	protected static $oInstance;
	private          $aPost;

	function __construct() {
		$this->aPost = $_POST;
		//if we were working with complex data we would be using:
		//$this->aPost = json_decode(file_get_contents('php://input'),true); //because php's wrapper that generates $_POST doesn't yet work with json.  see: https://stackoverflow.com/questions/8893574/php-php-input-vs-post
//echo('$this->aPost:'.chr(10).print_r($this->aPost,true).chr(10));
//echo('$_POST:'.chr(10).print_r($_POST,true).chr(10));
	}

	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance = new self();
		return self::$oInstance;
	}

	public static function _() { return self::getInstance(); }

	public function get($sKey) {
		return (isset($this->aPost[$sKey]) ? $this->aPost[$sKey] : null);
	}

}

