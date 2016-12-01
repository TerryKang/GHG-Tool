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
                Base
                <small>Dataset</small>
            </h1>

        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row clearfix">
                <div class="col-md-6 column">
                    <table class="table table-bordered table-hover" id="tab_source">
                        <thead>
                            <tr >
                                <th class="text-center">
                                    Source
                                </th>
                                <th class="text-center" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <a id="add_source" class="btn btn-default pull-left">Add Source</a>
                </div>
                <div class="col-md-6 column">
                    <table class="table table-bordered table-hover" id="tab_composition">
                        <thead>
                            <tr >
                                <th class="text-center">
                                    Composition
                                </th>
                                <th class="text-center" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                        <a id="add_composition" class="btn btn-default pull-left">Add Composition</a>
                </div>
                <div class="col-md-6 column">
                    <table class="table table-bordered table-hover" id="tab_destination">
                        <thead>
                            <tr >
                                <th class="text-center">
                                    Destination
                                </th>
                                <th class="text-center" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                        <a id="add_destination" class="btn btn-default pull-left">Add Destination</a>
                </div>
            </div>
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
<script src="src/aja.js-0.4.1/aja.min.js"></script>

<script>
$(document).ready(function(){
    aja()
    .url('base/data')
    .on('success', function(data){
        if(data.hasOwnProperty('source')){
            var source = data.source;
            Object.keys(source).map(function(k) {
                if(isNaN(k))
                    return;
                addSource(source[k]);
            });
        }
        if(data.hasOwnProperty('comps')){
            var composition = data.comps;
            Object.keys(composition).map(function(k) {
                if(isNaN(k))
                    return;
                addComposition(composition[k]);
            });
        }

        if(data.hasOwnProperty('destination')){
            var destination = data.destination;
            Object.keys(destination).map(function(k) {
                if(isNaN(k))
                    return;
                addDestination(destination[k]);
            });
        }
    })
    .go();

    $("#add_source").click(function(){
       addSource();
    });    
    $("#add_composition").click(function(){
      addComposition();
    });
    $("#add_destination").click(function(){
      addDestination();
    });
});

function addSource(val){
    var tr = document.createElement('tr');
    var input = document.createElement('td');
    var delButton = document.createElement('td');
    $(input).html("<input type='text' placeholder='Source Name' class='form-control input-md'" +  ((val!=null)? "value='" + val + "' disabled" : "") + ">");
    $(delButton).html("<button class='btn btn-danger glyphicon glyphicon-remove row-remove'> </button>");
    $(tr).append($(input)).append($(delButton));
    $('#tab_source').append($(tr));
        $(delButton).on("click", function() {
            $(this).closest("tr").remove();
        });
}
function addComposition(val){
    var tr = document.createElement('tr');
    var input = document.createElement('td');
    var delButton = document.createElement('td');
    $(input).html("<input type='text' placeholder='Composition Name' class='form-control input-md'" +  ((val!=null)? "value='" + val + "' disabled" : "") + ">");
    $(delButton).html("<button class='btn btn-danger glyphicon glyphicon-remove row-remove'> </button>");
    $(tr).append($(input)).append($(delButton));
    $('#tab_composition').append($(tr));
        $(delButton).on("click", function() {
            $(this).closest("tr").remove();
        });
}
function addDestination(val){
    var tr = document.createElement('tr');
    var input = document.createElement('td');
    var delButton = document.createElement('td');
    $(input).html("<input type='text' placeholder='Destination Name' class='form-control input-md'" +  ((val!=null)? "value='" + val + "' disabled" : "") + ">");
    $(delButton).html("<button class='btn btn-danger glyphicon glyphicon-remove row-remove'> </button>");
    $(tr).append($(input)).append($(delButton));
    $('#tab_destination').append($(tr));
        $(delButton).on("click", function() {
            $(this).closest("tr").remove();
        });
}
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
