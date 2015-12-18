<?php
require 'vendor/parse/php-sdk/autoload.php';
use Parse\ParseClient;
use Parse\ParseObject;
use Parse\ParseQuery;


/**
 * Same code from analytics.php Should need a ajax-Php function in order to call those methods...
 * Copied the code for quickness
 */

$x = $_GET['q'];

ParseClient::initialize('P60EfTUuOZoeZyD2qSLpOrc8DWwUk2YjEqU2HY1R', 's3b2cfGtQhSFYM16ZIJQ7yXioTjt35Um5mn9SyP8', '3jz6CONqt5psS4UlGu3RB28ldIw311Iv2I8eA3Mh');

$query = new ParseQuery("Reading");
if($x<32){
    $period = date('Y-m-d\TH:i:s.u\Z',strtotime("-".$x. "day"));
} else {
    $period = date('Y-m-d\TH:i:s.u\Z',strtotime("-7 day"));
    $x=7;
}


$results = Array();


$query->greaterThan("createdAt", $period);
$query->limit(10000);
$results = $query->find();



$weeklyTraffic = Array();
$currentDay = $results[0]->getCreatedAt()->format('d');
$weeklyTraffic[$currentDay] = 0;

$labelsString = "";
$data = "";

for($i = 0; $i < count($results); $i++){


    if($currentDay == $results[$i]->getCreatedAt()->format('d')){
        $weeklyTraffic[$results[$i]->getCreatedAt()->format('d').""] = $weeklyTraffic[$results[$i]->getCreatedAt()->format('d').""]+1;
    } else{
        /*
         * Prepare strings for x axis in javascript
         */
        $labelsString .= $currentDay . ',';
        $data .= $weeklyTraffic[$currentDay]. ',';

        $weeklyTraffic[$results[$i]->getCreatedAt()->format('d').""] = 1;
        $currentDay = $results[$i]->getCreatedAt()->format('d');
    }

}

//set last results
$labelsString .= $currentDay . ',';
$data .= $weeklyTraffic[$currentDay] . ',';

$vars['period'] = $x;
$vars['labels'] = $labelsString;
$vars['data'] = $data;

echo json_encode($vars);