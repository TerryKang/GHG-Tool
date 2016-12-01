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
<body class="hold-transition skin-black sidebar-mini" onload="init();">
<div class="wrapper">

    <?php include "header.php" ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                User profile
            </h1>

        </section>

        <!-- Main content -->
        <section class="content">
            <table class="table table-hover table-condensed" id="inputTable"></table>
			
			    <div class="box-body col-sm-4 col-sm-offset-4 col-xs-12">

       

        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Change credentials</h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <form role="form" action="/editProfile" method="post">
            <div class="box-body">
              <div class="form-group" id="profileForm">
                <input name="firstName" type="text" class="form-control"  placeholder="First Name" value=<?php echo "'" . $firstName . "'"?>>
                <input name='lastName' type="text" class="form-control" placeholder="Last Name" value=<?php echo "'" . $lastName . "'"?>>
				<input name="phone" type="text" class="form-control" placeholder="Enter Phone" value=<?php echo "'" . $phone . "'"?>>
				<input name="address" type="text" class="form-control" placeholder="Enter Address" value=<?php echo "'" . $address . "'"?>>
                <input name='email' type="email" class="form-control" placeholder="Enter email" value=<?php echo "'" . $email . "'"?>>
                <input name='oldPassword' type="password" class="form-control" placeholder="Current Password" >
                <input name='newPassword' type="password" class="form-control" placeholder="New Password" >
                <input name='confirmPassword' type="password" class="form-control" placeholder="Cofirm password">
              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
              <button type="button" onClick="reset()" class="btn btn-default">Reset</button>
            </div>
          </div>
          <!-- /.box-body -->
        </form>
    

          <!-- /.box-body -->
        </form>
		
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
<script>
	function reset(){
		$("#profileForm").html();
		$("#profileForm").append('<input name="firstName" type="text" class="form-control"  placeholder="First Name" value=<?php echo "'" . $firstName . "'"?>>' + 
                '<input name="lastName" type="text" class="form-control" placeholder="Last Name" value=<?php echo "'" . $lastName . "'"?>>' + 
                '<input name="email" type="email" class="form-control" placeholder="Enter email" value=<?php echo "'" . $email . "'"?>>' + 
                '<input name="oldPassword" type="password" class="form-control" placeholder="Password" >' + 
                '<input name="newPassword" type="password" class="form-control" placeholder="Cofirm password">' + 
				'<input name="address" type="text" class="form-control" placeholder="Enter Address" value=<?php echo "'" . $address . "'"?>>');
	}
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
