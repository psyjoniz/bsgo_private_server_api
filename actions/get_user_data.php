<?php

if(Post::_()->get('email') == '') throw new Exception('Missing input: email');
if(Post::_()->get('user_key') == '') throw new Exception('Missing input: user_key');
if(Post::_()->get('user_id') == '') throw new Exception('Missing input: user_id');
if(!API::_()->verifyUserKey(Post::_()->get('email'), Post::_()->get('user_key'))) throw new Exception('Unauthorized');

$aUserId = API::_()->getUserId(Post::_()->get('email'));
$aUserData = API::_()->getUserData($aUserId); //authenticated user

//if the authenticated user_id does not match the requested user_id then we check if they are an admin first
if($aUserId != Post::_()->get('user_id') && $aUserData['user']['type'] != 'admin') throw new Exception('Unauthorized');

$oResp->user_data = API::_()->getUserData(Post::_()->get('user_id'));

API::_()->updateUserLastSeen(Post::_()->get('email'));

