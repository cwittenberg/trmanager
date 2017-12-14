<?php		
	session_start();

	function isLoggedIn() {
		if(!isset($_SESSION) || !isset($_SESSION['logon']) || $_SESSION['logon']==false || !isset($_SESSION['user'])) {
			header("Location:../");
		}
		
		if($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['_csrf'] != $_POST['_csrf']) {
			die('Invalid csrf token');
		}
	}
			
	isLoggedIn();

	header('Content-Type: text/html; charset=utf-8');

	if(!ob_start('ob_gzhandler'))
		ob_start();
	
	include('conf/config.php');	
	include('lib/lazy_mofo.php');	
	include('lib/tunnel.php');	
		
	$_SESSION['clientID']=2;
	
	//Page detection
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : null;
			
	$pageFile = null;
	switch($page) {
		case "connections":
			$pageFile = "connections.inc.php";		
			break;
		case "relaystatus":
			$pageFile = "relaystatus.inc.php";		
			break;				

		case "forwards":
			$pageFile = "forwards.inc.php";		
			break;				
			
		case "audit":
			$pageFile = "audit.inc.php";
			break;	
			
		case "users":
			$pageFile = "users.inc.php";
			break;	
		case "configuration":
			$pageFile = "settings.inc.php";
			break;				

		case "overview":
		default:
			$pageFile="relaystatus.inc.php";
			$page="relaystatus";
	}		
	
	$activeFlag=" class=\"active\"";


	require("lib/database.php"); 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/ico"  href="favicon/favicon.ico">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="favicon/ms-icon-144x144.png">
	<meta name="theme-color" content="#ffffff">    
    

    <title>Tunnel-Relay Manager</title>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!--<link href="assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">//-->
    
    <link href="dashboard.css" rel="stylesheet">    
    <link href="style.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap-switch.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="assets/js/ie-emulation-modes-warning.js"></script>
	
	<script src="js/jquery.js"></script>
	<script src="js/bootstrap-switch.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/editor/">Tunnel-Relay Manager&nbsp;</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="?page=configuration">Settings</a></li>            
            <li><a href="../logout.php">Logout <?php echo $_SESSION['user']['firstName']; ?></a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li<?php echo $page=="relaystatus" ? $activeFlag : ""; ?>><a href="?page=relaystatus">Dashboard <span class="sr-only">(current)</span></a></li>                                   
          </ul>
          <ul class="nav nav-sidebar">            			
			<li<?php echo $page=="connections" ? $activeFlag : ""; ?>><a href="?page=connections">Connections <span class="sr-only">(current)</span></a></li>                                   
          </ul>
          <?php if(isAdmin()) { ?>
          <ul class="nav nav-sidebar">			
            <li<?php echo $page=="users" ? $activeFlag : ""; ?>><a href="?page=users">Users</a></li>
            <li<?php echo $page=="audit" ? $activeFlag : ""; ?>><a href="?page=audit">Auditing</a></li>
          </ul>
          <?php } ?>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <?php
			//Main page
			require("inc/" . $pageFile);
          ?>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="js/bootstrap.min.js"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="assets/js/vendor/holder.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
