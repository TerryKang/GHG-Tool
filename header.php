<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="/dashboard" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>GHG</b></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>GHG</b>tool</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <div class="row">
                <div class="col-md-8">
                    <div class='login_txt'>
                    <p>Welcom, <?php echo $username; ?> </p><a href='editProfile.php' target='_parent'><img src='images/profile.gif' alt='Profile' title='Profile'></a>
                    <a href='/logout'>logout</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="nav navbar-nav">
                        <!-- Control Sidebar Toggle Button -->
                        <li>
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header"><h4>SP Application</h4></li>
            <!-- Optionally, you can add icons to the links -->
            <?php if($path=="/input") echo "<li class='active'>"; else echo "<li>"; ?>
                <a href="/input"><i class="fa fa-list"></i> <span>Input</span></a>
            </li>
            <?php if($path=="/analysis") echo "<li class='active'>"; else echo "<li>"; ?>
                <a href="/analysis"><i class="fa fa-connectdevelop"></i> <span>Analysis</span></a>
            </li>
            <?php if($path=="/base") echo "<li class='active'>"; else echo "<li>"; ?>
                <a href="/base"><i class="fa fa-database"></i> <span>Base Dataset</span></a>
            </li>
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
