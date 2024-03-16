<?php

use Aws\Ec2\Ec2Client;

class AwsSdk {

	protected static $oInstance;
	private static $sKey = '';
	private static $sSecret = '';
	private static $hEc2Client;
	private static $sSecurityGroupId;

	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance    = new self();
		return self::$oInstance;
	}

	public static function _() { return self::getInstance(); }

	public function __construct() {
		$aAwsSdkCredentials     = require '../aws_sdk_credentials.inc';
		self::$sKey             = $aAwsSdkCredentials['aws_key'];
		self::$sSecret          = $aAwsSdkCredentials['aws_secret'];
		self::$sSecurityGroupId = $aAwsSdkCredentials['security_group_id'];
		self::$hEc2Client       = new Ec2Client([
			'version'     => 'latest',
			'region'      => 'us-east-2',
			'credentials' => [
				'key'    => self::$sKey,
				'secret' => self::$sSecret,
			],
		]);
	}

	public function getWhitelistRules() {
		try {
			$aSecurityGroups = self::$hEc2Client->describeSecurityGroups([
				'GroupIds' => [self::$sSecurityGroupId],
			]);
			return $aSecurityGroups['SecurityGroups'];
		} catch (Aws\Exception\AwsException $e) {
			throw new Exception($e->getMessage());
		}
	}

	public function addWhitelistRule($sMachineFingerprint, $sIp, $sEmail, $iPort) {
		try {
			self::$hEc2Client->authorizeSecurityGroupIngress([
				'GroupId' => self::$sSecurityGroupId,
				'IpPermissions' => [
					[
						'IpProtocol' => 'tcp',
						'FromPort'   => $iPort,
						'ToPort'     => $iPort,
						'IpRanges'   => [
							[
								'CidrIp'      => $sIp.'/32',
								'Description' => 'bsgo-whitelist::'.$sMachineFingerprint.'::'.$sEmail,
							],
						],
					],
				],
			]);
		} catch (Aws\Exception\AwsException $e) {
			throw new Exception($e->getMessage());
		}
	}

	public function removeWhitelistRules($sMachineFingerprint, $sIp, $sEmail, $aRules = null) {
		$ingressRulesToDelete = [];
		if(null === $aRules) $aRules = $this->getWhitelistRules();
		foreach ($aRules as $securityGroup) {
			foreach ($securityGroup['IpPermissions'] as $permission) {
				foreach ($permission['IpRanges'] as $range) {
					if (
						isset($range['Description'])
						&& (
							strpos($range['Description'], 'bsgo-whitelist::'.$sMachineFingerprint.'::'.$sEmail) !== false)
							|| (isset($range['CidrIp']) && strpos($range['CidrIp'], $sIp) !== false
						)
					) {
						$ingressRulesToDelete[] = [
							'IpProtocol' => $permission['IpProtocol'],
							'FromPort'   => $permission['FromPort'],
							'ToPort'     => $permission['ToPort'],
							'IpRanges'   => [['CidrIp' => $range['CidrIp'], 'Description' => $range['Description']]],
						];
					}
				}
			}
		}
		if (!empty($ingressRulesToDelete)) {
			try {
				self::$hEc2Client->revokeSecurityGroupIngress([
					'GroupId'       => self::$sSecurityGroupId,
					'IpPermissions' => $ingressRulesToDelete,
				]);
			} catch (Aws\Exception\AwsException $e) {
				throw new Exception($e->getMessage());
			}
		}
	}

}

