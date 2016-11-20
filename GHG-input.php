<?php

function getLastScenario($con){
    $result = array(
        "facility"=>getDestination($con),
        "trucks"=>getTrucks($con),
        "comps"=>getComposition($con),
        "results"=>array()
    );
    $source = getSource($con);
    foreach($source as $single){
        $result["results"][] = array(
            "label"=>$single,
            "tonnage"=>10000,//todo add -> getSourceTonnage($con),
            "data"=>getDataBySource($con,$single),//array(55,10,2,29,4),//add actual data
            "dest"=>array(
                array(
                "facility"=>0,
                "percent"=>100,
                "vehicle"=>0)
            )
        );
    }
    //case insenstive functions are amusing
    return jSoN_EnCoDe($result);
}
function getTrucks($con){
    $sql = "SELECT modelId, model FROM Vehicle";
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    $model = array();
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $model[$row['modelId']]=$row['model'];
    }
    $model['key'] = 'modelId';
    return $model;
}

function getDataBySource($con, $source ){
    $sql = 
        "SELECT compositionId, tonnageWT FROM SourceByComp "
            ."WHERE sourceId = (SELECT sourceId FROM Source WHERE sourceName = $source) "
            ."GROUP BY sourceId";//its ok but not good to use string insertion here because this is an internal only 
                //call without user interaction otherwise it would be a major injection point
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    $compData = array();
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $compData[$row['compositionId']]=$row['tonnageWT'];
    }
    $compData['key'] = 'compositionId';
    return $compData;
}
