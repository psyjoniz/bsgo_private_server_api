Code Sample : Timer

A basic PHP class for timing things.

Example of use :

<?php

include_once('Timer.class.php');

//optionally start a timer upon instantiation
$oTimer = new Timer('timer_name');

//start another timer
$oTimer->start('new_timer');

//stop first timer and get its' results
$aTimer_timer_name = $oTimer->stop('timer_name');

//stop second timer and get its' results
$aTimer_new_timer = $oTimer->stop('new_timer');

//display elapsed time in seconds
echo('timer_name : ' . $aTimer_timer_name['elapsed']);
