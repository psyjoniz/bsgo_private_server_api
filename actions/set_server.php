<?php

if(Post::_()->get('email') == '') throw new Exception('Missing input: email');
if(Post::_()->get('user_key') == '') throw new Exception('Missing input: user_key');
if(Post::_()->get('server_id') == '') throw new Exception('Missing input: server_id');
if(!API::_()->verifyUserKey(Post::_()->get('email'), Post::_()->get('user_key'))) throw new Exception('Unauthorized');
if(!API::_()->doesServerIdExist(Post::_()->get('server_id'))) throw new Exception('Invalid server');

API::_()->setServer(API::_()->getUserId(Post::_()->get('email')), Post::_()->get('server_id'));

