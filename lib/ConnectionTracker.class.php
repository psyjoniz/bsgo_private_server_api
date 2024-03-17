<?php

require_once('MySQL.class.php');

class ConnectionTracker {

	protected static $oInstance;

	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance = new self();
		return self::$oInstance;
	}

	public static function _() { return self::getInstance(); }
	
	public function cleanup() {
		MySQL::_()->query("DELETE FROM `connection_tracker` WHERE `created` < NOW() - INTERVAL 6 MONTH");
		MySQL::_()->query("DELETE FROM `connection_cooldown` WHERE DATE_ADD(`created`, INTERVAL `duration_seconds` SECOND) < NOW()");
	}

	public function track() {
		$this->cleanup();
		if($this->onCooldown()) throw new Exception('Connection is on cooldown for abuse');
		$mUserId = (API::_()->getUserId(Post::_()->get('email'))??'NULL');
		$sIp     = $_SERVER['REMOTE_ADDR'];
		//record to table `connection_tracker`A
		$sQuery = "
			INSERT INTO
				`connection_tracker`
			SET
				`ip_address` = '{$sIp}',
				`user_id`    = {$mUserId},
				`last_seen`  = NOW()
			ON DUPLICATE KEY UPDATE
				`last_seen`  = NOW()
		";
		MySQL::_()->query($sQuery);
	}

	public function putOnCooldown($sEvent, $iSeconds) {
		$mUserId  = (API::_()->getUserId(Post::_()->get('email'))??'NULL');
		$sIp      = $_SERVER['REMOTE_ADDR'];
		$sEvent   = MySQL::_()->escapeString($sEvent);
		$iSeconds = (int) $iSeconds;
		$sQuery = "
			INSERT INTO
				`connection_cooldown`
			SET
				`ip_address` = '{$sIp}',
				`user_id` = {$mUserId},
				`duration_seconds` = {$iSeconds}
			ON DUPLICATE KEY UPDATE
				`duration_seconds` = IF(
					VALUES(`duration_seconds`) > `duration_seconds`,
					VALUES(`duration_seconds`),
					`duration_seconds`
				)
		";
		MySQL::_()->query($sQuery);
		$sLogCooldownQuery = "
			INSERT INTO
				`connection_cooldown_log`
			SET
				`ip_address` = '{$sIp}',
				`user_id` = {$mUserId},
				`event` = '{$sEvent}',
				`duration_seconds` = {$iSeconds}
		";
		MySQL::_()->query($sLogCooldownQuery);
	}

	public function onCooldown() {
		$mUserId  = (API::_()->getUserId(Post::_()->get('email'))??'NULL');
		$sIp      = $_SERVER['REMOTE_ADDR'];
		$sCountQuery = "
			SELECT
				COUNT(`id`) AS `total`
			FROM
				`connection_cooldown`
			WHERE
				`ip_address` = '{$sIp}'
				AND `user_id` = {$mUserId}
		";
		$sCount = MySQL::_()->query($sCountQuery);
		return ($sCount[0]['total'] == 1 ? true : false);
	}

}

