<?php

if(Post::_()->get('email')    == '')                            throw new Exception('Missing input: email');
if(Post::_()->get('password') == '')                            throw new Exception('Missing input: password');
if(Post::_()->get('invite_key') == '')                          throw new Exception('Missing input: invite_key');
if(!API::_()->isInviteKeyValid(Post::_()->get('invite_key')))   throw new Exception('Invalid invite_key');
if(API::_()->doesUserExist(Post::_()->get('email')))            throw new Exception('Account already exists');
if(!filter_var(Post::_()->get('email'), FILTER_VALIDATE_EMAIL)) throw new Exception('Invalid email');

$iUserId = API::_()->createUser(Post::_()->get('email'), Post::_()->get('password'));

API::_()->useInviteKey($iUserId, Post::_()->get('invite_key'));

$aBSGOUserData = BSGO::_()->createUser($iUserId);

API::_()->updateBSGOUserData($iUserId, $aBSGOUserData);

