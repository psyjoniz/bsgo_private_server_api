<?php

require_once('MySQL.class.php');
require_once('Response.class.php');
require_once('ConnectionTracker.class.php');

class RateLimiter {

	protected static $oInstance;

	private $aConfig = [
		'register' => [
			'seconds_per_event'       => 30,   //1 event per 30 seconds (retrying stacks 'offenses')
			'max_offenses'            => 10,   //gets your connection put on cooldown
			'cooldown_period_seconds' => 3600, //1 hour
		],
		'authenticate' => [
			'seconds_per_event'       => 30,
			'max_offenses'            => 10,
			'cooldown_period_seconds' => 3600, //1 hour
		],
		'generate_invite_key' => [
			'seconds_per_event'       => 86400,  //24 hours
			'max_offenses'            => 10,
			'cooldown_period_seconds' => 604800, //1 week
		],
	];

	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance = new self();
		return self::$oInstance;
	}

	public static function _() { return self::getInstance(); }

	public function cleanup($sEvent) {
		if(!empty($this->aConfig[$sEvent]['seconds_per_event'])) {
			MySQL::_()->query("DELETE FROM `rate_limit_tracker` WHERE `created` < NOW() - INTERVAL {$this->aConfig[$sEvent]['seconds_per_event']} SECOND");
		}
	}

	public function cleanupOffenses($sEvent) {
		$mUserId = (API::_()->getUserId(Post::_()->get('email'))??'NULL');
		$sIp     = $_SERVER['REMOTE_ADDR'];
		$sEvent  = MySQL::_()->escapeString($sEvent);
		$sQuery = "
			DELETE FROM
				`rate_limit_offenses`
			WHERE
				`ip_address`  = '{$sIp}'
				AND `user_id` = {$mUserId}
				AND `event`   = '{$sEvent}'
		";
		MySQL::_()->query($sQuery);
	}

	public function enforce($sEvent) {
		$mUserId = (API::_()->getUserId(Post::_()->get('email'))??'NULL');
		$sIp     = $_SERVER['REMOTE_ADDR'];
		$sEvent  = MySQL::_()->escapeString($sEvent);
		if(!empty($this->aConfig[$sEvent])) {
			$this->cleanup($sEvent);
			$sQuery = "
				INSERT INTO
					`rate_limit_tracker`
				SET
					`ip_address` = '{$sIp}',
					`user_id`    = {$mUserId},
					`event`      = '{$sEvent}'
			";
			MySQL::_()->query($sQuery);
			if(!empty($this->aConfig[$sEvent]['seconds_per_event'])) {
				$sCountQuery = "
					SELECT
						COUNT(`id`) AS `total`
					FROM
						`rate_limit_tracker`
					WHERE
						`ip_address`  = '{$sIp}'
						AND `user_id` = {$mUserId}
						AND `event`   = '{$sEvent}'
				";
				$aCount = MySQL::_()->query($sCountQuery);
				if($aCount[0]['total'] > 1) {
					$this->recordOffense($sEvent);
					throw new Exception('Rate Limit exceeded: You may not ['.$sEvent.'] more than once per '.$this->aConfig[$sEvent]['seconds_per_event'].' second(s)');
				} else {
					$this->cleanupOffenses($sEvent);
				}
			}
		}
	}

	public function recordOffense($sEvent) {
		$mUserId = (API::_()->getUserId(Post::_()->get('email'))??'NULL');
		$sIp     = $_SERVER['REMOTE_ADDR'];
		$sEvent  = MySQL::_()->escapeString($sEvent);
		$sQuery = "
			INSERT INTO
				`rate_limit_offenses`
			SET
				`ip_address` = '{$sIp}',
				`user_id`    = {$mUserId},
				`event`      = '{$sEvent}',
				`count`      = 1
			ON DUPLICATE KEY UPDATE
				`count`      = (`count` + 1)
		";
		MySQL::_()->query($sQuery);
		$sCountQuery = "
			SELECT
				`count` AS `total`
			FROM
				`rate_limit_offenses`
			WHERE
				`ip_address`  = '{$sIp}'
				AND `user_id` = {$mUserId}
				AND `event`   = '{$sEvent}'
		";
		$aCount = MySQL::_()->query($sCountQuery);
		if($aCount[0]['total'] >= $this->aConfig[$sEvent]['max_offenses']) {
			ConnectionTracker::_()->putOnCooldown($sEvent, $this->aConfig[$sEvent]['cooldown_period_seconds']);
			throw new Exception('You have exceeded the maximum number of allowable offenses. Your connection is now on cooldown.');
		}
	}

}

