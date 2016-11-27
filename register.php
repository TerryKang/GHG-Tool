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

  <link rel="stylesheet" href="style/login.css">

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
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script language="JavaScript" src="include/global.js" type="text/javascript"></script>
<script>
	//check email availability
	$(document).ready(function() {
				<?php

		if(!empty($_SESSION['UserId'])) {
			header('Location: '.'dashboard.html');
		}
		?>
		$("#email").on('change keyup paste', function () {
		   var email = $(this).val();
		   if(email.length > 6) {
			   $.post('registerCheck.php', {'email':email}, function(data) {
			   $("#userResult").html(data); //display the availability
			   });
		   }
		});
	});
	
	function confirmLogout() {
		if(window.confirm("Are you sure you want to logout? All unsaved data will be lost."))
			window.open('logout.php', '_parent');
	}
	
	//validate email address
	function validateEmail() {
		//var email = document.getElementById('email');
		var email = $$('email', document);
		var pattern = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (!pattern.test(email.value)) {
			//email.focus();
			$$('errEmail', document).innerHTML = "<span class=msg>youremail@example.com</span>";
			return false;
		} else {
			$$('errEmail', document).innerHTML = null;
			return true;
		}
	}

	/* 
		Validate first name using regular expression 
		Allowed: a-z, A-Z, dash(-), and underscore(_)
		Not allowed: numbers and invalid characters (#`!@$%, etc)
	*/
	function validateFirstName(){
		var name = $$('firstName', document);
		var pattern = /^[a-zA-Z_-]+$/;
		//if(name.value == null || name.value == '') return true;
		var val = name.value.replace(/\s/g, '');
		if(!pattern.test(val) || val.length < 1) {
			$$('errFirstName', document).innerHTML = "<span class=msg>Only letters, dashes, and underscores are allowed.</span>";
			return false;
		} else {
			$$('errFirstName', document).innerHTML = null;
			return true;
		}
	}

	/* 
		Validate last name using regular expression 
		Allowed: a-z, A-Z, dash(-), and underscore(_)
		Not allowed: numbers and invalid characters (#`!@$%, etc)
	*/
	function validateLastName(){
		var name = $$('lastName', document);
		var pattern = /^[a-zA-Z_-]+$/;
		//if(name.value == null || name.value == '') return true;
		var val = name.value.replace(/\s/g, '');
		if(!pattern.test(val) || val.length < 1) {
			$$('errLastName', document).innerHTML = "<span class=msg>Only letters, dashes, and underscores are allowed.</span>";
			return false;
		} else {
			$$('errLastName', document).innerHTML = null;
			return true;
		}
	}


	//validate password
	function validatePassword() {
		var password = $$('password', document);
		if (password.value.length < 3) {
			//password.focus();
			$$('errPassword', document).innerHTML = "<span class=msg>Password must contain at least 3 characters.</span>";
			return false;
		} else {
			$$('errPassword', document).innerHTML = null;
			return true;
		}			
	}

	//validate retype password
	function validatePassword1() {
		var password = $$('password', document);
		var password1 = document.getElementById('password1');
		if (password1.value != password.value) {
			//password1.focus();
			$$('errPassword1', document).innerHTML = "<span class=login_msg>Passwords not matched.</span>";
			return false;
		} else {
			$$('errPassword1', document).innerHTML = null;
			return true;
		}
	}

	//validate form
	function validateForm(){
		var result = validatePassword1();
		return result;
	}

</script>
</head>

<body class="hold-transition skin-black sidebar-collapse">
  <!-- Main Header -->
  <header class="main-header">
  <nav class="navbar navbar-static-top " role="navigation">
		<!-- Sidebar toggle button-->
		<div class="col-sm-14">
		<form  method="post" action="/login" class="form-inline pull-right">
			<?php 
					if(isset($loginError) && $loginError != 0)
						echo "<span class='login_msg'>Email or password is incorrect</span><br/>";
			?>
			<input type='email' name='loginemail' class='form-control' placeholder='Email' maxlength=30 required>
			<input type='password' name='loginpass' class='form-control'  placeholder='Password' maxlength=15 required>
			<button type='submit' class='btn'>Login</button>
            <div class='col-sm-7 pull-right'><a href='password.html'>Forgot password?</a></div>
		</form>
		</div>
	</nav>
</header>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <!--Registration input-->
  <div class="box-body">
    <div class="col-sm-11">
      <form class="pull-right" id="registerForm" name="registerForm" action="/register" method="post" onsubmit="return validateForm()">

        <h2>Register</h2>
        <table>
          <tbody>
            <tr>
              <td>  <input type="text" name="firstName" class="form-control" placeholder="First name" maxlength=15 required></td>
              <td>  <input  type="text" name="lastName" id="lastName" class="form-control"  placeholder="Last name" maxlength=15 required></td>
            </tr>
            <tr>
              <td colspan="2">  <input  type="email" id="email" name="email" class="form-control"  placeholder="Email" maxlength=30 required></td>
            </tr>
            <tr>
              <td colspan="2">
				<input  type="password" id="password" name="password" class="form-control"  placeholder="Password" required>
				<div id="errPassword"></div>
			  </td>
            </tr>
            <tr>
              <td colspan="2">
				<input  type="password" id="password1" name="password1" class="form-control"  placeholder="Retype password" required>
				<div id="errPassword1"></div>
			  </td>
            </tr>

          </tbody></table>
		  <div id="userResult"></div>
          <button type="submit" class="btn btn-block btn-success btn-lg">Register</button>

        </div>
      </form>
    </div>

    <!--Registration input end-->
  </div>
  <!-- /.content-wrapper -->


  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
      GHG Tool
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2016 <a href="#">Amiran Services</a>.</strong> All rights reserved.
  </footer>

  <!-- ./wrapper -->

  <!-- REQUIRED JS SCRIPTS -->

  <!-- jQuery 2.2.3 -->
  <script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
  <!-- Bootstrap 3.3.6 -->
  <script src="bootstrap/js/bootstrap.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/app.min.js"></script>

  <!-- Optionally, you can add Slimscroll and FastClick plugins.
  Both of these plugins are recommended to enhance the
  user experience. Slimscroll is required when using the
  fixed layout. -->
</body>
</html>
