<?php

if(Post::_()->get('email') == '')                                                 throw new Exception('Missing input: email');
if(Post::_()->get('user_key') == '')                                              throw new Exception('Missing input: user_key');
if(!API::_()->verifyUserKey(Post::_()->get('email'), Post::_()->get('user_key'))) throw new Exception('Unauthorized');
if(Post::_()->get('machine_fingerprint') == '')                                   throw new Exception('Missing input: machine_fingerprint');
if(Post::_()->get('ip') == '')                                                    throw new Exception('Missing input: ip');

//$oResp->ip = Post::_()->get('ip');
//$oResp->machine_fingerprint = Post::_()->get('machine_fingerprint');
//$oResp->whitelist_rules = AwsSdk::_()->getWhitelistRules();
AwsSdk::_()->removeWhitelistRules(Post::_()->get('machine_fingerprint'), Post::_()->get('ip'), Post::_()->get('email'));
AwsSdk::_()->addWhitelistRule(Post::_()->get('machine_fingerprint'), Post::_()->get('ip'), Post::_()->get('email'), 22);

