<?php

require_once('Request/Get.class.php');
require_once('Request/Post.class.php');

class Request {

	protected static $oInstance;
	private $oGet;
	private $oPost;

	function __construct() {
		$this->oGet  = Get::getInstance();
		$this->oPost = Post::getInstance();
	}

	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance = new self();
		return self::$oInstance;
	}

	public static function _() { return self::getInstance(); }

	public function get($sKey) {
		$mGet  = $this->oGet->get($sKey);
		$mPost = $this->oPost->get($sKey);
		return (null !== $mPost ? $mPost : $mGet);
	}

}

