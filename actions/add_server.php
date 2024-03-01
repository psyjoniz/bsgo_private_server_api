<?php

if(Post::_()->get('email') == '') throw new Exception('Missing input: email');
if(Post::_()->get('user_key') == '') throw new Exception('Missing input: user_key');
if(Post::_()->get('name') == '') throw new Exception('Missing input: name');
if(Post::_()->get('address') == '') throw new Exception('Missing input: address');
if(Post::_()->get('port') == '') throw new Exception('Missing input: port');
if(!API::_()->verifyUserKey(Post::_()->get('email'), Post::_()->get('user_key'))) throw new Exception('Unauthorized');

$aUserData = API::_()->getUserData(Post::_()->get('email'));

if($aUserData['user']['type'] != 'admin') throw new Exception('Unauthorized');

API::_()->addServer(Post::_()->get('name'), Post::_()->get('address'), Post::_()->get('port'));

API::_()->updateUserLastSeen(Post::_()->get('email'));

