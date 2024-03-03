<?php

if(Post::_()->get('machine_fingerprint') == '') throw new Exception('Missing input: machine_fingerprint');
if(Post::_()->get('ip') == '')                  throw new Exception('Missing input: ip');

//$oResp->ip = Post::_()->get('ip');
//$oResp->machine_fingerprint = Post::_()->get('machine_fingerprint');
//$oResp->whitelist_rules = AwsSdk::_()->getWhitelistRules();
AwsSdk::_()->removeWhitelistRules(Post::_()->get('machine_fingerprint'), Post::_()->get('ip'));
AwsSdk::_()->addWhitelistRule(Post::_()->get('machine_fingerprint'), Post::_()->get('ip'), 22);

