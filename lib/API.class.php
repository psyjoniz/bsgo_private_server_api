<?php

require_once('MySQL.class.php');

class API {

	protected static $oInstance;
	protected $iMaxDaysLoggedIn = 90;
	protected $iMaxHoursSeen = 12;

	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance = new self();
		return self::$oInstance;
	}

	public static function _() { return self::getInstance(); }

	public function doesUserExist($sEmail) {
		$sEmail = MySQL::_()->escapeString($sEmail);
		$sCheckForUserQuery  = "
			SELECT
				COUNT(`u`.`id`) AS `total`
			FROM
				`users` `u`
			WHERE
				`u`.`email` = '{$sEmail}'
		";
		$aCheckForUserResult = MySQL::_()->query($sCheckForUserQuery);

		return (!empty($aCheckForUserResult[0]['total']) && $aCheckForUserResult[0]['total'] > 0 ? true : false);
	}

	public function createUser($sEmail, $sPassword) {
		$sEmail    = MySQL::_()->escapeString($sEmail);
		$sPassword = MySQL::_()->escapeString($sPassword);
		$sCreateUserQuery = "
			INSERT INTO
				`users`
			SET
				`email` = '{$sEmail}',
				`password` = PASSWORD('{$sPassword}')
		";
		MySQL::_()->query($sCreateUserQuery);
		$iUserId = MySQL::_()->getInsertId();
		$sCreateUserServerQuery = "
			INSERT INTO
				`user_server`
			SET
				`user_id` = {$iUserId}
		";
		MySQL::_()->query($sCreateUserServerQuery);
	}

	public function authenticateUser($sEmail, $sPassword) {
		$sEmail    = MySQL::_()->escapeString($sEmail);
		$sPassword = MySQL::_()->escapeString($sPassword);
		$sValidateQuery = "
			SELECT
				COUNT(`u`.`id`) AS `total`
			FROM
				`users` `u`
			WHERE
				`u`.`email` = '{$sEmail}'
				AND `u`.`password` = PASSWORD('{$sPassword}')
		";
		$aValidateResult = MySQL::_()->query($sValidateQuery);

		if(!empty($aValidateResult[0]['total']) && $aValidateResult[0]['total'] == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function getUserId($sEmail) {
		$sEmail = MySQL::_()->escapeString($sEmail);
		$sQuery = "
			SELECT
				`u`.`id`
			FROM
				`users` `u`
			WHERE
				`u`.`email` = '{$sEmail}'
		";
		$aResult = MySQL::_()->query($sQuery);
		return (int) $aResult[0]['id'];
	}

	public function updateUserAuthenticated($sEmail) {
		$sEmail    = MySQL::_()->escapeString($sEmail);
		$sQuery = "
			UPDATE
				`users` `u`
			SET
				`u`.`authenticated` = NOW()
			WHERE
				`u`.`email` = '{$sEmail}'
		";
		MySQL::_()->query($sQuery);
	}

	public function updateUserLastSeen($sEmail) {
		$sEmail    = MySQL::_()->escapeString($sEmail);
		$sQuery = "
			UPDATE
				`users` `u`
			SET
				`u`.`last_seen` = NOW()
			WHERE
				`u`.`email` = '{$sEmail}'
		";
		MySQL::_()->query($sQuery);
	}

	public function generateUserKey($sEmail, $bForceNewKey = false) {
		$sEmail = MySQL::_()->escapeString($sEmail);
		$sIp    = MySQL::_()->escapeString($_SERVER['REMOTE_ADDR']);
		if(!$bForceNewKey) {
			$sGetCurrentKeyQuery = "
				SELECT
					`u`.`key`
				FROM
					`users` `u`
				WHERE
					`u`.`email` = '{$sEmail}'
					AND `u`.`ip` = '{$sIp}'
			";
			$aCurrentKey = MySQL::_()->query($sGetCurrentKeyQuery);
			$sKey = $aCurrentKey[0]['key'];
		}
		if($bForceNewKey || empty($sKey) || !$this->verifyUserKey($sEmail, $sKey)) {
			$sKey   = md5(microtime());
			$sUpdateUserKeyQuery = "
				UPDATE
					`users` `u`
				SET
					`u`.`key` = '{$sKey}',
					`u`.`ip` = '{$sIp}'
				WHERE
					`u`.`email` = '{$sEmail}'
			";
			MySQL::_()->query($sUpdateUserKeyQuery);
		}
		if(empty($sKey)) throw new Exception('Unable to generate key!');
		return $sKey;
	}

	public function verifyUserKey($sEmail, $sUserKey) {
		$sEmail   = MySQL::_()->escapeString($sEmail);
		$sUserKey = MySQL::_()->escapeString($sUserKey);
		$sIp      = MySQL::_()->escapeString($_SERVER['REMOTE_ADDR']);
		$sVerifyQuery = "
			SELECT
				COUNT(`u`.`id`) AS `total`
			FROM
				`users` `u`
			WHERE
				`u`.`email` = '{$sEmail}'
				AND `u`.`key` = '{$sUserKey}'
				AND `u`.`ip` = '{$sIp}'
				AND (
					`u`.`authenticated` > DATE_ADD(NOW(), INTERVAL -{$this->iMaxDaysLoggedIn} DAY)
					OR `u`.`last_seen` > DATE_ADD(NOW(), INTERVAL -{$this->iMaxHoursSeen} HOUR)
				)
		";
		$aVerifyResult = MySQL::_()->query($sVerifyQuery);

		if(!empty($aVerifyResult[0]['total']) && $aVerifyResult[0]['total'] == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function updateLastSeen($sEmail) {
		$sEmail   = MySQL::_()->escapeString($sEmail);
		$sUpdateLastSeenQuery = "
			UPDATE
				`users` `u`
			SET
				`u`.`last_seen` = NOW()
			WHERE
				`u`.`email` = '{$sEmail}'
		";
		MySQL::_()->query($sUpdateLastSeenQuery);
	}

	public function getUserData($iId) {
		$aReturn = [];
		$iId = (int) $iId;
		$sUserQuery = "
			SELECT
				`u`.`id`,
				`u`.`email`,
				`u`.`authenticated`,
				`u`.`last_seen`,
				`u`.`type`,
				`u`.`ip`
			FROM
				`users` `u`
			WHERE
				`u`.`id` = '{$iId}'
		";
		$sUserResult = MySQL::_()->query($sUserQuery);
		$aReturn['user'] = $sUserResult[0];
		$sUserServerQuery = "
			SELECT
				`s`.`name`,
				`s`.`address`,
				`s`.`port`
			FROM
				`user_server` `us`
			JOIN
				`users` `u` ON
					`u`.`id` = `us`.`user_id`
			JOIN
				`servers` `s` ON
					`s`.`id` = `us`.`server_id`
			WHERE
				`u`.`id` = {$iId}
		";
		$aUserServerResult = MySQL::_()->query($sUserServerQuery);
		$aReturn['server'] = $aUserServerResult[0];
		return $aReturn;
	}

	public function getAllUsers() {
		$sAllUsersQuery = "
			SELECT
				`u`.`id`
			FROM
				`users` `u`
		";
		$aAllUsers = MySQL::_()->query($sAllUsersQuery);
		$aAllUsersReturn = [];
		foreach($aAllUsers as $iKey => $aUser) {
			$aAllUsersReturn[] = $this->getUserData($aUser['id']);
		}
		return $aAllUsersReturn;
	}

	public function addServer($sName, $sAddress, $iPort) {
		$sName = MySQL::_()->escapeString($sName);
		$sAddress = MySQL::_()->escapeString($sAddress);
		$iPort = (int) $iPort;
		$sCreateServerQuery = "
			INSERT INTO
				`servers`
			SET
				`name`    = '{$sName}',
				`address` = '{$sAddress}',
				`port`    = {$iPort}
		";
		MySQL::_()->query($sCreateServerQuery);
	}

	public function getServers() {
		$sGetServersQuery = "
			SELECT
				`s`.`id`,
				`s`.`name`,
				`s`.`address`,
				`s`.`port`
			FROM
				`servers` `s`
		";
		return MySQL::_()->query($sGetServersQuery);
	}

}

