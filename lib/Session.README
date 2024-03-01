Code Sample : Session

A basic PHP class for Session handling.

Example of use :

<?php

include_once('Cookie.class.php');
include_once('Session.class.php');

//optionally specify session id
//use optional namespace
$oSession = new Session('session_id12345', 'my_namespace');

//set
$oSession->set('name', 'value');

//get
echo('name : ' . $oSession->get('name') . '<br />');

//remove
$oSession->remove('name');

//remove all
$oSession->removeAll();
