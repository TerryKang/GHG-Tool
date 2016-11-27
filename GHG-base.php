<?php
function getBase($con, $uid, $comp){
    if(!checkHistory($con,$comp)){
        return json_encode(["message"=>"historyId not found","error_code"=>404]);
    }
    $comp = (int)$comp;//force to an int to reduce user impact on queries
    $result = array(
        "facility"=>getDestination($con),
        "trucks"=>getTrucks($con),
        "comps"=>getComposition($con),
        "source"=>getSource($con),
        "results"=>array(),
        "compHistoryId"=>$comp,
        );
        foreach($result['source'] as $key=>$val){
            if(!is_numEric($key))
                continue;
            $result['results'][] = getTonnageBySource($con, $uid, $key, $comp);
        }
    //case insenstive functions are amusing
    return jSoN_EnCoDe($result);
}
function getTonnageBySource($con, $uid, $source, $history){
    $sql = 
        " SELECT tonnageWT AS W tonnageTO AS O FROM SourceByComp"
        ." WHERE sourceId = '$source'"
        ." AND historyId = '$history'"
        ." AND historyId IN (SELECT historyId FROM History WHERE userId = '$uid')"
        ;
    //its ok but not good to use string insertion here because this is an internal only 
    //call without user interaction otherwise it would be a major injection point
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    $compData = array();
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $compData[]=array(
            "tonnageWT"=>$row['W'],
            "tonnageOT"=>$row['O']
        );
    }
    $compData['key'] = 'compositionId';
    return $compData;
}
