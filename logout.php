<?php
	session_start();

	include('editor/conf/config.php');	
	require('editor/lib/database.php');

	$_SESSION['user'] = array(); 
	$_SESSION['logon'] = false;
	
	unset($_SESSION['user']);				
	unset($_SESSION['logon']);

	session_destroy();
	
	header("Location:index.php");
?>
