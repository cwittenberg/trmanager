<?php
	session_start();

	include('editor/conf/config.php');	
	require('editor/lib/database.php');

	$resp = array('login'=>false, 'guid'=>null);
		
	if(isset($_POST['guid']) && isset($_POST['user']) && isset($_POST['pass'])) {
		if($_SESSION['guid'] == $_POST['guid']) {
			$user=$_POST['user']; 
			$password=$_POST['pass']; 
		
			$res = query2Array("select * from users where email=:user and password=:password", array('user'=>$user, 'password'=>$password));						
			
			if(sizeof($res)>0) {	
				if(isset($res[0]['email'])) {
					if($res[0]['email']!="") {
						$resp['login'] = true;
						$resp['guid'] = $_SESSION['guid'];
						
						$_SESSION['user'] = $res[0]; 
						$_SESSION['logon'] = true;
						$_SESSION['_csrf'] = base64_encode(openssl_random_pseudo_bytes(15));
						
						registerEvent("Login", "Succesful");
					}
				}
			}
		}
	}
	
	if($resp['login']==false) {
		registerEvent("Login", "Failed login attempt using email: {$_POST['user']}");
	}
		
	echo json_encode($resp);
?>
