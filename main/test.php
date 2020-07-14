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
//echo $interval;

$n = DateTime::createFromFormat(DATE_ATOM, '2020-07-12 19:46:52');
$n = DateTime::createFromFormat('Y-m-d H:i:s', '2020-07-12 19:46:52');

echo $n->getTimestamp();


