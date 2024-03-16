<?php

require_once('MongoDB.class.php');

class BSGO {

	protected static $oInstance;

	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance = new self();
		return self::$oInstance;
	}

	public static function _() { return self::getInstance(); }

	function createUser($iUserId) {

		$iUserId = (string) $iUserId;
		$sSessionId = null;
		$aSessionsTried = [];

		while(null === $sSessionId || $this->doesSessionExist($sSessionId)) {
			$sSessionId = Utils::_()->randomPassword(64, false, true, true, false);
			$aSessionsTried[] = $sSessionId;
		}

		APIResponse::_()->sSessionId     = $sSessionId;
		APIResponse::_()->aSessionsTried = $aSessionsTried;
		APIResponse::_()->iUserId        = $iUserId;

		MongoDB::_()->usersCollection()->insertOne(['playerid' => $iUserId, 'sessioncode' => $sSessionId]);

		return [
			'sSessionId' => $sSessionId,
			'iUserId'    => $iUserId
		];
	}

	function doesSessionExist($sSessionId) {
		$aUser = MongoDB::_()->usersCollection()->findOne(['sessioncode' => $sSessionId]);
		return ($aUser??false);
	}

	/* didn't work very well; went to re-using the id from mysql db as it will be unique
	function getNextUserId() {
		$aUser = MongoDB::_()->usersCollection()->find([], ['sort' => ['playerid' => -1], 'limit' => 1])->toArray();
		if(!empty($aUser)) return ((int)$aUser[0]['playerid'] + 1);
		return 1; //if no users exist, gotta start somewhere
	}
	 */

}

