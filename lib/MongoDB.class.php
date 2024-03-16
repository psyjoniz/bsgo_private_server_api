<?php

require '../vendor/autoload.php';

class MongoDB {

	protected static $oInstance;
	public           $sDBHost = 'localhost';
	public           $sDBData = 'bsgo';
	private          $hClient; //mongodb client
	private          $hDB; //database 'bsgo' handle for generating collection references
	private          $cUsers;
	private          $cCharacters;

	function __construct() {
		if(!$this->hClient     = new MongoDB\Client('mongodb://localhost'))      throw new Exception('Could not connect to MongoDB');
		if(!$this->hDB         = $this->hClient->selectDatabase($this->sDBData)) throw new Exception('Could not select DB ('.$this->sDBData.')');
		if(!$this->cUsers      = $this->hDB->selectCollection('users'))          throw new Exception('Could not find users collection');
		if(!$this->cCharacters = $this->hDB->selectCollection('characters'))     throw new Exception('Could not find characters collection');
	}

	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance = new self();
		return self::$oInstance;
	}

	public static function _() { return self::getInstance(); }

	function test() { return $this->cUsers->findOne(); }

	function client() {
		if($this->hClient) return $this->hClient;
		throw new Exception('Could not get MongoDB client');
	}

	function usersCollection() {
		if($this->cUsers) return $this->cUsers;
		throw new Exception('Could not get users collection');
	}

	function charactersCollection() {
		if($this->cCharacters) return $this->cCharacters;
		throw new Exception('Could not get characters collection');
	}

}

