<?php

if(Post::_()->get('email')    == '')                                                throw new Exception('Missing input: email');
if(Post::_()->get('user_key') == '')                                                throw new Exception('Missing input: user_key');
if(!API::_()->verifyUserKey(Post::_()->get('email'), Post::_()->get('user_key'))) throw new Exception('Unauthorized');

API::_()->updateLastSeen(Post::_()->get('email'));

