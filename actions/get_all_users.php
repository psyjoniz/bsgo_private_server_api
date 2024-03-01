<?php

$aUserData = API::_()->getUserData(API::_()->getUserId(Post::_()->get('email')));

if($aUserData['user']['type'] != 'admin') throw new Exception('Unauthorized');

$oResp->all_users = API::_()->getAllUsers();

