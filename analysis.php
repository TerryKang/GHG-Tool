<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>GHG Tool</title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!-- Bootstrap 3.3.6 -->
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="dist/css/AdminLTE.min.css">
<!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
<link rel="stylesheet" href="dist/css/skins/skin-black.min.css">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
<style>
    .loader {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border: 16px solid #ffffff;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
    }

    @-webkit-keyframes spin {
    0% { -webkit-transform: rotate(0deg); }
    100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }
</style>
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-black sidebar-mini">
<div class="wrapper">
    <?php include "header.php" ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        GHG-Analysis
      </h1>
      <div class='form-group'>
        <label for="selScenario">Select Scenario :</label>
        <select class="form-control" id="selScenario">
        </select>
      </div>
        <div id="chartFilter">
            <button class="btn btn-primary filter-button" data-filter="all">All</button>
            <button class="btn btn-default filter-button" data-filter="srcbycomp">SourceByComposition</button>
            <button class="btn btn-default filter-button" data-filter="srcbydest">SourceByDestination</button>
            <button class="btn btn-default filter-button" data-filter="destbysrc">DestinationBySource(Bar)</button>
            <button class="btn btn-default filter-button" data-filter="destbysrcPie">DestinationBySource(Pie)</button>
            <button class="btn btn-default filter-button" data-filter="destbycomp">DestinationByComposition(Bar)</button>
            <button class="btn btn-default filter-button" data-filter="destbycompPie">DestinationByComposition(Pie)</button>
        </div>
      <ol class="breadcrumb">
        <li><a href="input.html"><i class="fa fa-dashboard"></i>Home</a></li>
        <li><a href="#">Analysis</a></li>
      </ol>
    </section>
    <div class="loader"></div>
    <!-- Main content -->
    <section class="content" id="chartContent">
      <!-- /.row -->

    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php include "footer.php" ?>

    <?php include "sidebar.php" ?>

</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- chartjs 2.3.0-->
<script src="src/chartjs-2.3.0/Chart.bundle.min.js"></script>
<!-- GHG-chart api -->
<script src="src/GHG-chart.js"></script>
<!-- aja 0.4.1 -->
<script src="src/aja.js-0.4.1/aja.min.js"></script>
<script>

$( document ).ready(function() {
    aja()
    .url('analysis/historyList')
    .on('success', function(data){
        for(var i=0; i<data.length;i++){
            var newOption = $('<option>');
            var text = data[i].scenarioName + ", " + new Date(data[i].date.date)
            newOption.attr('value', data[i].scenarioName).text(text);
            $('#selScenario').append(newOption);
        }
        $("#selScenario").change(function () {
            var scenarioName = $('#selScenario :selected').val();
            selectScenario(scenarioName);
        });
        if($('#selScenario option').size()>0){
            var scenarioName = $('#selScenario :selected').val();
            selectScenario(scenarioName);
        }else
            $('.loader').hide();
    })
    .go();

    $(".filter-button").click(function(){
        var value = $(this).attr('data-filter');
        if(value == "all")
        {
            $('.filter').show('slow');
        }
        else
        {
            $(".filter").not('.'+value).hide('slow',function(){
                $('.filter').filter('.'+value).show('slow');
            });
        }
    });
});

function selectScenario(scenarioName){
    $("#chartContent").empty();
    $('.loader').show('slow', function(){
        aja()
        //.url('testData.json')
        .url('analysis/' + scenarioName)
        .on('success', function(data){
            $('.loader').hide();
            if(data.hasOwnProperty('results')){
                $("#chartFilter").show();
                var ghgChart = new GHGChart($("#chartContent"),data);
                ghgChart.init();
            }else{
                $("#chartFilter").hide();
            }
        })
        .go();
    });
}
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
