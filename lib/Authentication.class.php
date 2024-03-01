<?php

require_once('MySQL.class.php');

class Authentication {

	protected static $oInstance;

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
	}

}

