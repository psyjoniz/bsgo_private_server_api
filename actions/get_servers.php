<?php

if(Post::_()->get('email') == '') throw new Exception('Missing input: email');
if(Post::_()->get('user_key') == '') throw new Exception('Missing input: user_key');
if(!API::_()->verifyUserKey(Post::_()->get('email'), Post::_()->get('user_key'))) throw new Exception('Unauthorized');

$aUserData = API::_()->getUserData(API::_()->getUserId(Post::_()->get('email')));

$oResp->servers = API::_()->getServers($aUserData['user']['type']);

