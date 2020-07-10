<?php


$date = new DateTime();

//Create a new DateInterval object using P30D.
$interval = new DateInterval('P31D');

//Add the DateInterval object to our DateTime object.
$date->add($interval);

//Print out the result.
//echo $date->format("Y-m-d");

$now = new DateTime();

$interval = $date->getTimestamp() - $now->getTimestamp();
echo $interval;



