<?php

//-------------------
//==>Saving Values<==
//-------------------
function saveDests($con, $data){
    foreach($data['results'] as $source => $entry){
        foreach($entry['dest'] as $facility => $dest){
             
        }
    }

}


//--------------------
//==>Getting Values<==
//--------------------

//get the newest scenario and building the respones
function getNewest($con){
    $sql = "SELECT MAX(historyId) AS M FROM History";
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $id=$row['M'];
    }
    return $id;
}

//check if history exists
function checkHistory($con,$history){
    $sql = "SELECT COUNT(*) AS C FROM History WHERE historyId = ". (int)$history;//force to be an int as this is user entered
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    return (sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)['C'] != 0);
}

//handle building scenario data by history id
function getScenario($con,$history){
    if(!checkHistory($con,$history)){
        return json_encode(["message"=>"historyId not found","error_code"=>404]);
    }
    $history = (int)$history;//force to an int to reduce user impact on queries
    $result = array(
        "facility"=>getDestination($con),
        "trucks"=>getTrucks($con),
        "comps"=>getComposition($con),
        "results"=>array(),
        "historyId"=>$history
    );
    $source = getSource($con);
    foreach($source as $key => $val){
        if(!is_numeric($key))
            continue;
        $tons = getSourceTonnage($con,$val,$history);
        if($tons==0){
            $tons=1;//avoid devide by zero if there are no listed tons
        }
        $result["results"][] = array(
            "label"=>$val,
            "tonnage"=> $tons,
            "data"=>getDataBySource($con,$val,$tons,$history),
            "dest"=>getDestinationBySource($con,$val,$tons,$history)
        );
    }
    //case insenstive functions are amusing
    return jSoN_EnCoDe($result);
}

//get all trucks and put them into an assoc array by DB ID
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

//get the percentage of each source's compositon
function getDataBySource($con, $source, $tons, $history){
    $sql = 
        "SELECT compositionId, tonnageWT FROM SourceByComp"
        ." LEFT JOIN Source ON (SourceByComp.sourceId = Source.sourceId)"
        ." WHERE sourceName = '$source'"
        ." AND historyId = $history"
        ;
    //its ok but not good to use string insertion here because this is an internal only 
    //call without user interaction otherwise it would be a major injection point
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    $compData = array();
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $compData[$row['compositionId']]=round(100*(($row['tonnageWT']) / $tons));
    }
    $compData['key'] = 'compositionId';
    return $compData;
}
//get the percentage of each source's compositon
function getDestinationBySource($con, $source, $tons, $history){
    $sql = 
        "SELECT destinationId, tonnageWT FROM SourceByDest"
        ." LEFT JOIN Source ON (SourceByDest.sourceId = Source.sourceId)"
        ." WHERE sourceName = '$source'"
        ." AND historyId = $history"
        ;
    //its ok but not good to use string insertion here because this is an internal only 
    //call without user interaction otherwise it would be a major injection point
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    $dest = array();
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $dest[] = array(
            'facility' => $row['destinationId'],
            'vehicle' => 1,//TODO find the actual vehicle used
            'percent' => round(100*(($row['tonnageWT']) / $tons))
        );
    }
    return $dest;
}
//get the total weight of the source to calulate the percentage
function getSourceTonnage($con, $source, $history){
    $sql = 
        "SELECT SUM(tonnageWT) AS S FROM SourceByComp "
        ." LEFT JOIN Source ON (SourceByComp.sourceId = Source.sourceId)"
        ." WHERE sourceName = '$source'"
        ." AND historyId = $history"
        ;
    //its ok but not good to use string insertion here because this is an internal only 
    //call without user interaction otherwise it would be a major injection point
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $tonnage = $row['S'];
    }
    return $tonnage;
}
