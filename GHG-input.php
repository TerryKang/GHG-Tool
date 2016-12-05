<?php

//-------------------
//==>Saving Values<==
//-------------------
function saveDests($con, $uid, $data){
    $history = getNewestDest($con,$uid);
    //create a new history record
    $histSql = "INSERT INTO History (historyName,userId,createDate) VALUES ('Scenario $history','$uid',GETDATE())";
    $stmt = sqlsrv_query( $con, $histSql );
    if( $stmt === false) {
        die(__LINE__. print_r( sqlsrv_errors(), true) );
    }
    $newrec = getNewest($con, $uid);//i cant get the last insert id from sqlsrv nicely
                         //when you can replace newrec with it
    foreach($data['source'] as $source => $entry){
        if($source == 'key')
            continue;
        //find the tonnage for this source
        $sql = "SELECT SUM(tonnageWT) AS W, SUM(tonnageTO) AS O FROM SourceByDest "
            ." WHERE sourceId = '$source'"
            ." AND historyId = $history"
            ." AND historyId IN (SELECT H.historyId FROM History H"
            ." INNER JOIN SourceByDest S ON (S.historyId = H.historyId)"
            ." WHERE userId = '$uid')";
            ;
        $stmt = sqlsrv_query( $con, $sql );
        if( $stmt === false)
            die(__LINE__. print_r( sqlsrv_errors(), true) );
        
        $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
        $tonWT = $row['W'];
        $tonOT = $row['O'];
        foreach($entry['dest'] as $facility => $dest){
            $percent = $dest['percent']/100.0;
            $newWT = $tonWT * $percent;
            $newOT = $tonOT * $percent;
            //vehicle not currently stored in the db
            //$dest['vehicle']
            $sql = "INSERT INTO SourceByDest VALUES("
                ."'$source','$facility','$newrec',2015,'$newWT','$newOT')";
            $stmt = sqlsrv_query( $con, $sql );
            if( $stmt === false)
                die(__LINE__. print_r( sqlsrv_errors(), true) );
        }
    }
}
//-----------------------
//==>Getting Histories<==
//-----------------------

//get the newest scenario and building the respones
function getNewest($con, $uid){
    $sql = "SELECT MAX(historyId) AS M FROM History WHERE userId = '$uid'";
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die(__LINE__. print_r( sqlsrv_errors(), true) );
    }
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $id=$row['M'];
    }
    return $id;
}

//get the newest scenario and building the respones
function getNewestComp($con, $uid){
    $sql = "SELECT MAX(historyId) AS M FROM SourceByComp WHERE historyId IN"
        ." (SELECT historyId FROM History WHERE userId = '$uid')";
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die(__LINE__. print_r( sqlsrv_errors(), true) );
    }
    return sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)['M'];
}

//get the newest scenario and building the respones
function getNewestDest($con, $uid){
    $sql = "SELECT MAX(historyId) AS M FROM SourceByDest WHERE historyId IN"
        ." (SELECT historyId FROM History WHERE userId = '$uid')";
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die(__LINE__. print_r( sqlsrv_errors(), true) );
    }
    return sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)['M'];
}

//check if history exists
function checkHistory($con, $uid,$history){
    $sql = "SELECT COUNT(*) AS C FROM History WHERE historyId = '$history'"
        ." AND userId = '$uid'";
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die(__LINE__. print_r( sqlsrv_errors(), true) );
    }
    return (sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)['C'] != 0);
}

//get history list
function getSourceHistory($con,$uid){
    $historyList=array();
    $sql = "SELECT DISTINCT History.historyId as H, historyName, createDate  FROM History"
        ." INNER JOIN SourceByComp ON (History.historyId = SourceByComp.historyId)"
        ." WHERE userId = '$uid'"
        ;
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $historyList[] = array(
            "scenarioName" => $row["historyName"],
            "date" => $row["createDate"],
            "historyId" => $row["H"]
        );
    }
    return json_encode($historyList);
}


//get history list
function getDestinationHistory($con,$uid){
    $historyList=array();
    $sql = "SELECT DISTINCT History.historyId as H, historyName, createDate  FROM History"
        ." INNER JOIN SourceByDest ON (History.historyId = SourceByDest.historyId)"
        ." WHERE userId = '$uid'"
        ;
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $historyList[] = array(
            "scenarioName" => $row["historyName"],
            "date" => $row["createDate"],
            "historyId" => $row["H"]
        );
    }
    return json_encode($historyList);
}


//--------------------
//==>Getting Values<==
//--------------------
//handle building scenario data by history id
function getScenario($con, $uid,$dest,$comp){
    if(!(checkHistory($con, $uid,$dest)&&checkHistory($con, $uid,$comp))){
        return json_encode(["message"=>"historyId not found","error_code"=>404]);
    }
    $comp = (int)$comp;//force to an int to reduce user impact on queries
    $dest = (int)$dest;//force to an int to reduce user impact on queries
    $result = array(
        "facility"=>getDestination($con),
        "trucks"=>getTrucks($con),
        "comps"=>getComposition($con),
        "results"=>array(),
        "compHistoryId"=>$comp,
        "destHistoryId"=>$dest
    );
    $source = getSource($con);
    foreach($source as $key => $val){
        if(!is_numeric($key))
            continue;
        $tons = getSourceTonnage($con, $uid,$val,$comp);
        if($tons==0){
            $tons=1;//avoid devide by zero if there are no listed tons
        }
        $result["results"][] = array(
            "label"=>$val,
            "tonnage"=> $tons,
            "data"=>getDataBySource($con, $uid,$val,$tons,$comp),
            "dest"=>getDestinationBySource($con, $uid,$val,$tons,$dest)
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
        die(__LINE__. print_r( sqlsrv_errors(), true) );
    }
    $model = array();
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $model[$row['modelId']]=$row['model'];
    }
    $model['key'] = 'modelId';
    return $model;
}

//get the percentage of each source's compositon
function getDataBySource($con, $uid, $source, $tons, $history){
    $sql = 
        " SELECT compositionId, tonnageWT FROM SourceByComp"
        ." LEFT JOIN Source ON (SourceByComp.sourceId = Source.sourceId)"
        ." WHERE sourceName = '$source'"
        ." AND historyId = $history"
        ." AND historyId IN (SELECT historyId FROM History WHERE userId = '$uid')"
        ;
    //its ok but not good to use string insertion here because this is an internal only 
    //call without user interaction otherwise it would be a major injection point
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die(__LINE__. print_r( sqlsrv_errors(), true) );
    }
    $compData = array();
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $compData[$row['compositionId']]=round(100*(($row['tonnageWT']) / $tons),2);
    }
    $compData['key'] = 'compositionId';
    return $compData;
}
//get the percentage of each source's compositon
function getDestinationBySource($con, $uid, $source, $tons, $history){
    $sql = 
        "SELECT destinationId, tonnageWT FROM SourceByDest"
        ." LEFT JOIN Source ON (SourceByDest.sourceId = Source.sourceId)"
        ." WHERE sourceName = '$source'"
        ." AND historyId = $history"
        ." AND historyId IN (SELECT historyId FROM History WHERE userId = '$uid')"
        ;
    //its ok but not good to use string insertion here because this is an internal only 
    //call without user interaction otherwise it would be a major injection point
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die(__LINE__. print_r( sqlsrv_errors(), true) );
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
function getSourceTonnage($con, $uid, $source, $history){
    $sql = 
        " SELECT SUM(tonnageWT) AS S FROM SourceByComp "
        ." LEFT JOIN Source ON (SourceByComp.sourceId = Source.sourceId)"
        ." WHERE sourceName = '$source'"
        ." AND historyId = $history"
        ." AND historyId IN (SELECT historyId FROM History WHERE userId = '$uid')"
        ;
    //its ok but not good to use string insertion here because this is an internal only 
    //call without user interaction otherwise it would be a major injection point
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die(__LINE__. print_r( sqlsrv_errors(), true) );
    }
    return sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)['S'];
}
