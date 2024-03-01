<?php

if(Post::_()->get('email')    == '')                                               throw new Exception('Missing input: email');
if(Post::_()->get('password') == '')                                               throw new Exception('Missing input: password');
if(!API::_()->authenticateUser(Post::_()->get('email'), Post::_()->get('password'))) throw new Exception('Invalid login');

$oResp->user_id = API::_()->getUserId(Post::_()->get('email'));
$oResp->user_key = API::_()->generateUserKey(Post::_()->get('email'));

API::_()->updateUserAuthenticated(Post::_()->get('email'));
API::_()->updateUserLastSeen(Post::_()->get('email'));

