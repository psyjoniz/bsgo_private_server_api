<?php

if(Post::_()->get('email')    == '')                                                 throw new Exception('Missing input: email');
if(Post::_()->get('password') == '')                                                 throw new Exception('Missing input: password');
if(!API::_()->authenticateUser(Post::_()->get('email'), Post::_()->get('password'))) throw new Exception('Invalid login');

$oResp->user_id = API::_()->getUserId(Post::_()->get('email'));
$oResp->user_key = API::_()->generateUserKey(Post::_()->get('email'));

/* debug */
/*
$aDebug = [];
$aDebug['api_key'] = Post::_()->get('api_key');
$aDebug['email'] = Post::_()->get('email');
$aDebug['password'] = Post::_()->get('password');
$oResp->debug = $aDebug;
 */

API::_()->updateUserAuthenticated(Post::_()->get('email'));
API::_()->updateUserLastSeen(Post::_()->get('email'));

