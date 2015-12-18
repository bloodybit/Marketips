
<?php
require 'vendor/parse/php-sdk/autoload.php';
require_once 'analytics.php';
use Parse\ParseClient;
use Parse\ParseObject;
use Parse\ParseQuery;

/**
 * Same code from analytics.php Should need a ajax-Php function in order to call those methods...
 * Copied the code for quickness
 */

$title = $_GET['q'];

ParseClient::initialize('P60EfTUuOZoeZyD2qSLpOrc8DWwUk2YjEqU2HY1R', 's3b2cfGtQhSFYM16ZIJQ7yXioTjt35Um5mn9SyP8', '3jz6CONqt5psS4UlGu3RB28ldIw311Iv2I8eA3Mh');


$query = new ParseQuery("Reading");

$query->equalTo("title",  $title);
$query->limit(10000);
$results = $query->find();

// in result I have all the first 1000 results stored (title)
// I count the average time spent on an article
$startReadingCount = 0;
$startReadingHowMany = 0;
$readArticleCount = 0;
$readArticleHowMany = 0;
$pageBottomCount = 0;
$pageBottomHowMany = 0;
$scanned = 0;
$readed = 0;

for($i=0; $i<count($results); $i++){
//StartReading count
if($results[$i]->startReading != null){
$startReadingCount += $results[$i]->startReading;
$startReadingHowMany++;
}

//ReadArticleCount
if($results[$i]->readArticle != null){
$readArticleCount += $results[$i]->readArticle;
$readArticleHowMany++;
}

//pageBottomCount
if($results[$i]->pageBottom != null){
$pageBottomCount += $results[$i]->pageBottom;
$pageBottomHowMany++;
}

//Scanned or readed
if($results[$i]->type != null){
if($results[$i]->type == "Scanner"){
$scanned++;
} else {
$readed++;
}
}
}

//return data
$node['nViewers'] = count($results);

if($startReadingHowMany > 0){
    $node['startReadingPercentage'] = round($startReadingHowMany/$node['nViewers'], 3);
    $node['startReadingAfterAVG'] = round($startReadingCount/$startReadingHowMany, 3);
} else {
    $node['startReadingPercentage'] = 0;
    $node['startReadingAfterAVG'] = 0;
}

if($readArticleHowMany > 0){
    $node['readArticlePercentage'] = round($readArticleHowMany/$node['nViewers'], 3);
    $node['readArticleAfterAVG'] = round($readArticleCount/$readArticleHowMany, 3);
} else {
    $node['readArticlePercentage'] = 0;
    $node['readArticleAfterAVG'] = 0;
}

if($pageBottomHowMany > 0){
    $node['pageBottomPercentage'] = round($pageBottomHowMany/$node['nViewers'], 3);
    $node['pageBottomAfterAVG'] = round($pageBottomCount/$pageBottomHowMany, 3);
} else {
    $node['pageBottomPercentage'] = 0;
    $node['pageBottomAfterAVG'] = 0;
}


echo json_encode($node);
