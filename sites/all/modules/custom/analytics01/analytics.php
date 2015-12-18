<?php
require 'vendor/parse/php-sdk/autoload.php';
use Parse\ParseClient;
use Parse\ParseObject;
use Parse\ParseQuery;


/**
 * @param $x [days ago to show]
 * @return mixed [associative array with period, labels, data]
 */
function getLastXTraffic($x){
    //Initialize PArse in PHP
    ParseClient::initialize('P60EfTUuOZoeZyD2qSLpOrc8DWwUk2YjEqU2HY1R', 's3b2cfGtQhSFYM16ZIJQ7yXioTjt35Um5mn9SyP8', '3jz6CONqt5psS4UlGu3RB28ldIw311Iv2I8eA3Mh');

    $query = new ParseQuery("Reading"); //Parse Object
    //filter the result (maximum 31 days)
    if($x<32){
        $period = date('Y-m-d\TH:i:s.u\Z',strtotime("-".$x. "day")); //set period in Parse format
    } else { //if else set to one week by default
        $period = date('Y-m-d\TH:i:s.u\Z',strtotime("-7 day")); //set period in Parse format
        $x=7;
    }


    $results = Array();


    $query->greaterThan("createdAt", $period); //Select all the reading done after those date
    $results = $query->find(); //Run query



    $weeklyTraffic = Array();
    $currentDay = $results[0]->getCreatedAt()->format('d'); //register the day, will use it as a checker
    $weeklyTraffic[$currentDay] = 0; //Set this day with 0 view already

    $labelsString = "";
    $data = "";

    for($i = 0; $i < count($results); $i++){


        if($currentDay == $results[$i]->getCreatedAt()->format('d')){
            //here we have element to store in current array's day
            $weeklyTraffic[$results[$i]->getCreatedAt()->format('d').""] = $weeklyTraffic[$results[$i]->getCreatedAt()->format('d').""]+1;
        } else{
            /*
             * Prepare strings for x axis in javascript
             */
            $labelsString .= "'" . $currentDay . "',"; //made the string ready in json
            $data .= "'" . $weeklyTraffic[$currentDay] . "',"; //made the string ready in json

            //set new day into the array
            $weeklyTraffic[$results[$i]->getCreatedAt()->format('d').""] = 1;
            $currentDay = $results[$i]->getCreatedAt()->format('d'); //set new currentday with the one actually in use
        }

    }

    //set last results
    $labelsString .= "'" . $currentDay . "',"; //made the string ready in json with the last element
    $data .= "'" . $weeklyTraffic[$currentDay] . "',";//made the string ready in json with the last element

    // Set the array in order to return it
    $vars['period'] = $x;
    $vars['labels'] = $labelsString;
    $vars['data'] = $data;

    return $vars;
}

function infoArticle($title){

    //Initialize parse api php
    ParseClient::initialize('P60EfTUuOZoeZyD2qSLpOrc8DWwUk2YjEqU2HY1R', 's3b2cfGtQhSFYM16ZIJQ7yXioTjt35Um5mn9SyP8', '3jz6CONqt5psS4UlGu3RB28ldIw311Iv2I8eA3Mh');

    //creating Parse Object
    $query = new ParseQuery("Reading");

    $query->equalTo("title",  $title); //select views by title
    $query->limit(1000); //Maximum limit
    $results = $query->find(); //run the query

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

    //For each article count each parameter
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
    }

    if($readArticleHowMany > 0){
        $node['readArticlePercentage'] = round($readArticleHowMany/$node['nViewers'], 3);
        $node['readArticleAfterAVG'] = round($readArticleCount/$readArticleHowMany, 3);
    }

    if($pageBottomHowMany > 0){
        $node['pageBottomPercentage'] = round($pageBottomHowMany/$node['nViewers'], 3);
        $node['pageBottomAfterAVG'] = round($pageBottomCount/$pageBottomHowMany, 3);
    }


    //Return associative array
    return $node;

}

/**
 * @param $uid
 */
function getTagUser($uid){

    //initialaza Parse PHP api
    ParseClient::initialize('P60EfTUuOZoeZyD2qSLpOrc8DWwUk2YjEqU2HY1R', 's3b2cfGtQhSFYM16ZIJQ7yXioTjt35Um5mn9SyP8', '3jz6CONqt5psS4UlGu3RB28ldIw311Iv2I8eA3Mh');

    $query = new ParseQuery("Reading");

    $query->equalTo("user",  $uid.""); //retrun user with selected id
    $query->limit(1000);
    $results = $query->find();


    $list = Array();

    for($t=0; $t<count($results); $t++){ //for each article I memorize the title
        $title = $results[$t]->title;

        $query2 = "SELECT name FROM taxonomy_term_data WHERE tid IN (
            SELECT field_tags_tid FROM field_data_field_tags WHERE entity_id IN (
                SELECT vid FROM node WHERE title = '". htmlspecialchars($title, ENT_QUOTES) ."'
            )
        )"; //select all the tags from an article (not optimized because I run multiples queries even if the article is the same)

        /***
         *
         *          TO DO
         *              select all the tags from an article (not optimized because I run multiples queries even if the article is the same)
         *              And put in a method
         *
         */

        $tags = db_query($query2);


        foreach ($tags as $record) {  //for each tag i increment its counter
            if(empty($list[$record->name])){
                $list[$record->name] = 1;
            } else {
                $list[$record->name] = $list[$record->name]+1;
            }
        }
    }


    return $list;
}


/*
 * method that should be used by the method above ->> Change
 */
function getTag($title){

    $query2 = "SELECT name FROM taxonomy_term_data WHERE tid IN (
            SELECT field_tags_tid FROM field_data_field_tags WHERE entity_id IN (
                SELECT vid FROM node WHERE title = '". htmlspecialchars($title, ENT_QUOTES) ."'
            )
        )";

    $tags = db_query($query2);

    $list = Array();

    foreach ($tags as $record) {
        if(empty($list[$record->name])){
            $list[$record->name] = 1;
        } else {
            $list[$record->name] = $list[$record->name]+1;
        }
    }
    return $list;
}