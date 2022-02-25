<?php
error_reporting(E_ERROR | E_PARSE);
include(APPPATH . 'libraries/simple_html_dom.php');

$myObj = new stdClass();

$html = file_get_html('http://www.indiarailinfo.com/train/1520?');

$list = $html->find('div[class="newschtable newbg inline"]', 0);


$h1 = $html->find('h1', 0);
$table = $html->find('table[class="deparrgrid ltGrayColor brownColor"]', 0);
$days = $table->find('tr', 0);
$catering = $html->find('div[style="text-align:left;"]', 0);
$coach = $html->find('div[class="num"]');
$station = $list->find('div[style="width:150px;background-color:#BFFCA3;color:#000000;"]');
$arrival = $list->find('div[class=""]');

$trainName = preg_replace('/[^\00-\255]+/u', '', $h1->plaintext);
$trainName = preg_replace('/[0-9]+/', '', $trainName);
$trainName = trim(str_replace(array('/', '-'), "", $trainName));
$myObj->trainName = $trainName;
$myObj->daysRunning = "Runs on: " . $days->plaintext;
$count = 0;

foreach ($arrival as $ar) {
    $st = $ar->find('div', 3);
    $myObj->scheduleData[$count]->stationName = $st->plaintext;
    //echo $st;

    $artime = $ar->find('div', 6);
    $myObj->scheduleData[$count]->arrivalTime = $artime->plaintext;
    //echo $artime;

    $detime = $ar->find('div', 8);
    $myObj->scheduleData[$count]->departureTime = $detime->plaintext;
    //echo $detime;

    $distance = $ar->find('div', 13);
    $myObj->scheduleData[$count]->distance = $distance->plaintext . " KM";
    //echo $distance;

    $day = $ar->find('div', 12);
    $myObj->scheduleData[$count]->day = $day->plaintext;
    //echo $day;

    $platform = $ar->find('div', 11);
    $myObj->scheduleData[$count]->platform = $platform->plaintext;
    //echo $platform;

    $avgDelay = $ar->find('div', 7);
    $myObj->scheduleData[$count]->avgDelay = $avgDelay->plaintext;
    //echo $avgDelay . "<br>";

    $count++;
}

$myObj->catering = $catering->plaintext;
$str = "";
foreach ($coach as $c) {
    $str .= "<-" . $c->plaintext;
}
$coachPosition =  $str . "<-";
$myObj->coachPosition = $coachPosition;

$myjson = json_encode($myObj);
echo $myjson;
