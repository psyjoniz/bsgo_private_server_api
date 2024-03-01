Code Sample : MySQL

A basic stand-alone PHP class for MySQL queries.

Example of Use :

<?php

include_once('MySQL.class.php');

$MySQL = new MySQL();

//insert
$sSQL = 'insert into `tmp` set `string_field` = "' .
$MySQL->escapeString('test (' . date('Ymd H:i:s') . ')') . '"';
$MySQL->query($sSQL);

//get last id
$iRecordId = $MySQL->getInsertId();

//select
$sSQL = 'select * from `tmp` where `id` = ' . $iRecordId;
$aResults = $MySQL->query($sSQL);
echo('<pre>' . print_r($aResults, true) . '</pre>');
