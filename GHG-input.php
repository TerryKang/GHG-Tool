<?php

function getLastScenario($con){
    $result = array(
        "facility"=>getDestination($con),
        "trucks"=>getTrucks($con),
        "comps"=>remKey(getComposition($con)),
        "results"=>array()
    );
    $source = getSource($con);
    foreach($source as $single){
        $result["results"][] = array(
            "label"=>$single,
            "data"=>array(1000,55,10,2,29,4),//add actual data
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
function remKey($tmp){ 
    $tmp['key'] = null;
    return $tmp;
}
function getTrucks($con){
    $sql = "SELECT model FROM Vehicle";
    $stmt = sqlsrv_query( $con, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    $model = array();
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
        $model[]=$row['model'];
    }
    return $model;
}

