<?php

    function getDestination($con){
        $sql = "SELECT destinationId, destinationName  FROM Destination";
        $stmt = sqlsrv_query( $con, $sql );
        if( $stmt === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        $destination = array();
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $destination[$row['destinationId']]=$row['destinationName'];
        }
        $destination['key'] = 'destinationId';
        return $destination;
    }
    function getSource($con){
        $sql = "SELECT sourceId, sourceName  FROM Source";
        $stmt = sqlsrv_query( $con, $sql );
        if( $stmt === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        $source = array();
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $source[$row['sourceId']]=$row['sourceName'];
        }
        $source['key'] = 'sourceId';
        $source['name'] = 'Source';
        return $source;
    }
    function getComposition($con){
        $sql = "SELECT compositionId, compositionName  FROM Composition";
        $stmt = sqlsrv_query( $con, $sql );
        if( $stmt === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        $composition = array();
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $composition[$row['compositionId']]=$row['compositionName'];
        }
        $composition['key'] = 'compositionId';
        return $composition;
    }
    function getHistoryList($uid, $con){
        $historyList=array();
        $sql = "SELECT historyName, createDate  FROM History WHERE userId = " . $uid;
        $stmt = sqlsrv_query( $con, $sql );
        if( $stmt === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $historyList[] = array(
                "scenarioName" => $row["historyName"],
                "date" => $row["createDate"]
            );
        }
        return json_encode($historyList);
    }

    function getHistoryId($uid, $scenarioName, $con){
        $historyId=null;
        $sql = "SELECT historyId  FROM History WHERE userId = " . $uid . " AND historyName = '" . $scenarioName . "'";
        $stmt = sqlsrv_query( $con, $sql );
        if( $stmt === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $historyId=$row['historyId'];
        }
        if($historyId==null)
            die();
        return $historyId;
    }

    function getBarLineChartXbyY($historyId, $xAxis, $yAxis, $con, $tableName){
        $xData = array();
        $yData = array();
        $xKey = $xAxis['key'];
        $yKey = $yAxis['key'];
        $sql = "SELECT " . $xKey .", " . $yKey . ", tonnageWT, tonnageTO FROM " . $tableName . " WHERE historyId = " . $historyId;
        $stmt = sqlsrv_query( $con, $sql );
        if( $stmt === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $x = $xAxis[$row[$xKey]];
            if (!in_array($x, $xData))
                $xData[]=$x;
            $y = $yAxis[$row[$yKey]];
            if(!array_key_exists($y,$yData))
                $yData[$y] = array(array(),array(),array());
            $yData[$y][0][]=$row['tonnageWT'] ;
            $yData[$y][1][]=$row['tonnageTO'] ;
            $yData[$y][2][]=$row['tonnageWT'] + $row['tonnageTO'];
        }

        $data = array(array(),array(),array());
        $total = array(array(),array(),array());

        foreach ($yData as $key => $value) {
            for ($x = 0; $x < 3; $x++) {
                foreach ($value[$x] as $index => $num) {
                    if(!array_key_exists($index,$total[$x]))
                    {
                        $total[$x][$index]=0;
                    }
                    $total[$x][$index]+=$num;
                }
                $data[$x][] = array(
                    "label" => $key,
                    "data" => $value[$x],
                    "type" =>  "bar"
                );
            }
        }
        for ($x = 0; $x < 3; $x++) {
            $data[$x][]  = array(
                "label" => 'Total',
                "data" => $total[$x],
                "type" =>  "line"
            );
        }
        return array(
            "labels" => $xData,
            "data" => array(
                "WOT" => $data[0],
                "TO" => $data[1],
                "WT" => $data[2]
            )
        );
    }

    function getPieChartXbyY($historyId, $xAxis, $yAxis, $con, $tableName){
        $xKey = $xAxis['key'];
        $yKey = $yAxis['key'];
        $sql = "SELECT " . $xKey .", " . $yKey . ", tonnageWT FROM " . $tableName . " WHERE historyId = " . $historyId;
        $stmt = sqlsrv_query( $con, $sql );
        if( $stmt === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        $ret = array();
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $x = $xAxis[$row[$xKey]];
            if (!array_key_exists($x, $ret))
                $ret[$x] = array('labels1'=>array(), 'data1'=>array(), 'data2'=>array());
            $ret[$x]['labels1'][]=$yAxis[$row[$yKey]];
            $ret[$x]['data1'][]=$row['tonnageWT'];
        }

        $sql = "SELECT destinationId, waste, transportation, electricity FROM DestinationWaste";
        $stmt = sqlsrv_query( $con, $sql );
        if( $stmt === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        $labels2=array('TotalWaste', 'Transportation', 'Electricity');     
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $x = $xAxis[$row[$xKey]];
            if (array_key_exists($x, $ret)){
                $ret[$x]['labels2']=$labels2;
                $ret[$x]['data2'][]=$row['waste'];
                $ret[$x]['data2'][]=$row['transportation'];
                $ret[$x]['data2'][]=$row['electricity'];
            }
        }
        return $ret;
    }
    
    function getBarLineCharts($destination, $source, $composition, $historyId, $con){
        $sourceByComp = getBarLineChartXbyY($historyId, $source, $composition, $con, 'SourceByComp');
        $destByComp = getBarLineChartXbyY($historyId, $destination, $composition, $con, 'DestByComp');
        $sourceByDest = getBarLineChartXbyY($historyId, $source, $destination, $con, 'SourceByDest');
        $destBySource = getBarLineChartXbyY($historyId, $destination, $source, $con, 'SourceByDest');

        $ret = array();

        $ret[]=array(
            "title" => 'Source Emission by Compositions (Without Transportation)',
            'typeid' => 1,
            'type' => 'bar_line',
            'labels' => $sourceByComp["labels"],
            'data' => $sourceByComp["data"]["WOT"],
            'filterType' => 'srcbycomp'
        );
        $ret[]=array(
            'title' => 'Source Emission by Compositions (Transportation Only)',
            'typeid' => 1,
            'type' => 'bar_line',
            'labels' => $sourceByComp["labels"],
            'data' => $sourceByComp["data"]["TO"],
            'filterType' => 'srcbycomp'
        );
        $ret[]=array(
            'title' => 'Source Emission by Compositions (With Transporation)',
            'typeid' => 1,
            'type' => 'bar_line',
            'labels' => $sourceByComp["labels"],
            'data' => $sourceByComp["data"]["WT"],
            'filterType' => 'srcbycomp'
        );

        $ret[]=array(
            "title" => 'Destination Emission by Compositions (Without Transportation)',
            'typeid' => 1,
            'type' => 'bar_line',
            'labels' => $destByComp["labels"],
            'data' => $destByComp["data"]["WOT"],
            'filterType' => 'destbycomp'
        );
        $ret[]=array(
            'title' => 'Destination Emission by Compositions (Transportation Only)',
            'typeid' => 1,
            'type' => 'bar_line',
            'labels' => $destByComp["labels"],
            'data' => $destByComp["data"]["TO"],
            'filterType' => 'destbycomp'
        );
        $ret[]=array(
            'title' => 'Destination Emission by Compositions (With Transportation)',
            'typeid' => 1,
            'type' => 'bar_line',
            'labels' => $destByComp["labels"],
            'data' => $destByComp["data"]["WT"],
            'filterType' => 'destbycomp'
        );


        $ret[]=array(
            'title' => 'Source Emission by Destination (Without Transportation)',
            'typeid' => 1,
            'type' => 'bar_line',
            'labels' => $sourceByDest["labels"],
            'data' => $sourceByDest["data"]["WOT"],
            'filterType' => 'srcbydest'
        );
        $ret[]=array(
            'title' => 'Source Emission by Destination (Transportation Only)',
            'typeid' => 1,
            'type' => 'bar_line',
            'labels' => $sourceByDest["labels"],
            'data' => $sourceByDest["data"]["TO"],
            'filterType' => 'srcbydest'
        );
        $ret[]=array(
            'title' => 'Source Emission by Destination (With Transportation)',
            'typeid' => 1,
            'type' => 'bar_line',
            'labels' => $sourceByDest["labels"],
            'data' => $sourceByDest["data"]["WT"],
            'filterType' => 'srcbydest'
        );

        $ret[]=array(
            'title' => 'Destination Emission by Source (Without Transportation)',
            'typeid' => 1,
            'type' => 'bar_line',
            'labels' => $destBySource["labels"],
            'data' => $destBySource["data"]["WOT"],
            'filterType' => 'destbysrc'
        );
        $ret[]=array(
            'title' => 'Destination Emission by Source (Transportation Only)',
            'typeid' => 1,
            'type' => 'bar_line',
            'labels' => $destBySource["labels"],
            'data' => $destBySource["data"]["TO"],
            'filterType' => 'destbysrc'
        );
        $ret[]=array(
            'title' => 'Destination Emission by Source (With Transportation)',
            'typeid' => 1,
            'type' => 'bar_line',
            'labels' => $destBySource["labels"],
            'data' => $destBySource["data"]["WT"],
            'filterType' => 'destbysrc'
        );
        return $ret;
    }

    function getPieCharts($destination, $source, $composition, $historyId, $con){
        
        $getDestByCompBarChart = getPieChartXbyY($historyId, $destination, $composition, $con, 'destByComp');
        $getDestBySourceBarChart = getPieChartXbyY($historyId, $destination, $source, $con, 'sourceByDest');
        
        $ret = array();
        foreach ($getDestByCompBarChart as $key => $value) {
            $ret[] = array(
                "title" => $key . " Landfill Emissions by Composition (tonne CO2e)",
                "typeid" => 2,
                "type" => "pie",
                'filterType' => 'destbycompPie',
                "data" => array(
                    array(
                        "labels" => $value['labels1'],
                        "data" => $value['data1'],
                    ),
                    array(
                        "labels" => $value['labels2'],
                        "data" => $value['data2'],
                    ),                 
                )
            );
        }
        foreach ($getDestBySourceBarChart as $key => $value) {
            $ret[] = array(
                "title" => $key . " Landfill Emissions by Composition (tonne CO2e)",
                "typeid" => 2,
                "type" => "pie",
                'filterType' => 'destbysrcPie',
                "data" => array(
                    array(
                        "labels" => $value['labels1'],
                        "data" => $value['data1'],
                    ),
                    array(
                        "labels" => $value['labels2'],
                        "data" => $value['data2'],
                    ),                 
                )
            );
        }
        return $ret;
    }

    function getAnalyzedData($uid, $scenarioName, $con){
        $destination = getDestination($con);
        $source = getSource($con);
        $composition = getComposition($con);
        $historyId = getHistoryId($uid, $scenarioName, $con);

        $barLineCharts = getBarLineCharts($destination, $source, $composition, $historyId, $con);
        $pieCharts = getPieCharts($destination, $source, $composition, $historyId, $con);

        $result = array_merge($barLineCharts, $pieCharts);

        $ems = array(   // emission stats
            'results' => $result
        );
        $jsonArray = json_encode($ems);
        // print out the json data
        return $jsonArray;
    }

?>