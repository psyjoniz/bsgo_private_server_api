<?php

if(Post::_()->get('email')    == '')                            throw new Exception('Missing input: email');
if(Post::_()->get('password') == '')                            throw new Exception('Missing input: password');
if(API::_()->doesUserExist(Post::_()->get('email')))            throw new Exception('Account already exists');
if(!filter_var(Post::_()->get('email'), FILTER_VALIDATE_EMAIL)) throw new Exception('Invalid email');

API::_()->createUser(Post::_()->get('email'), Post::_()->get('password'));

