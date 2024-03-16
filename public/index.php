<?php

header('Content-type: application/json');

try {

	$api_key = trim(file_get_contents('../api_key'));

	require_once('../vendor/autoload.php');
	require_once('../lib/Response.class.php');
	require_once('../lib/Request/Get.class.php');
	require_once('../lib/Request/Post.class.php');
	require_once('../lib/MySQL.class.php');
	require_once('../lib/MongoDB.class.php');
	require_once('../lib/API.class.php');
	require_once('../lib/AwsSdk.class.php');
	require_once('../lib/BSGO.class.php');

	$oResp   = APIResponse::_();
	$sApiKey = Post::_()->get('api_key');
	$sAction = Get::_()->get('action');

	if($sApiKey != $api_key)                        throw new Exception('Access denied'); //this is where brute forcing the api key would be stopped (keep track of requests from ip for volume and frequency)
	if(!file_exists('../actions/'.$sAction.'.php')) throw new Exception('Action does not exist');

	require_once('../actions/'.$sAction.'.php');

	$oResp->status = 'OK'; //everything above ran ok (no thrown exceptions)

} catch(Exception $e) {

	$oResp          = APIResponse::_();
	$oResp->status  = 'ERROR';
	$oResp->message = $e->getMessage();

}

die(json_encode($oResp->compile()));

