<?php
function is_session_started()
	{
		if ( php_sapi_name() !== 'cli' ) {
			if ( version_compare(phpversion(), '5.4.0', '>=') ) {
				return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
			} else {
				return session_id() === '' ? FALSE : TRUE;
			}
		}
		return FALSE;
	}

if(!is_session_started()) {
	session_start();
}

//generate a guid preventing a MIM
$guid = bin2hex(openssl_random_pseudo_bytes(16));
$_SESSION['guid'] = $guid;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Tunnel-Relay Manager</title>

    <!-- Bootstrap core CSS -->
    <link href="editor/css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="editor/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="main.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="editor/assets/js/ie-emulation-modes-warning.js"></script>

	<!-- MD5 hash lib //-->
	<script src="ext/crypto-js.min.js"></script>
	<script src="ext/md5.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
<script language="JavaScript">    
		function doSubmit() {
			var theGuid='<?php echo $_SESSION['guid'];?>';
			var user = $('#user').val().toString();
			var passhash = CryptoJS.MD5($('#password').val()).toString();
			
			$.post(
			  'login.php', 
			  { user: user, pass: passhash, guid: theGuid},
			  onLogin,
			  'json' );
		}     
		
		function onLogin(result) {						
			if(result.login == true && 
			   result.guid == '<?php echo $_SESSION['guid'];?>') {				
				document.location.href='editor/';	
			} else {
				alert('Wrong credentials');
			}			
		}
		     
    </script>    
  </head>

  <body>
    <div class="site-wrapper">
      <div class="site-wrapper-inner">
        <div class="cover-container">
          <div class="masthead clearfix">
            <div class="inner">
              <h3 class="masthead-brand"></h3>
              <nav>
                <ul class="nav masthead-nav">
                  <li class="active"><a href="#">Login</a></li>                                    
                </ul>
              </nav>
            </div>
          </div>
          <div class="inner cover">            
            <p class="lead">
				<form autocomplete="off" method="post">
				<div class="inner">
					<div class="rect">
						<table>
							<tr>
								<td style="font-size:20px;" width="150" align="left">
									User name:
								</td>
								<td align="left">
									<input type="text" id="user" name="user" autocomplete="off" style="padding:2px;width:400px;font-size:20px"/>
								</td>
							</tr>
							<tr>
								<td style="font-size:20px;" align="left">
									Password:
								</td>
								<td align="left">
									<input type="password" name="password" autocomplete="off" id="password" style="padding:2px;width:400px;font-size:20px"/>
								</td>
							</tr>
							<tr>
								<td>
								</td>
								<td align="left">
									<br>
									<a href="#" class="btn btn-lg btn-default" onclick="doSubmit();">Login</a>
								</td>
							</tr>
						</table>
					</div>
				</div>								
              </form>
            </p>
          </div>
          <div class="mastfoot">
            <div class="inner">              
            </div>
          </div>
        </div>
      </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>   
    <script>
		window.jQuery || document.write('<script src="editor/assets/js/vendor/jquery.min.js"><\/script>');
		
		function setAutoCompleteOFF(tm){
		 if(typeof tm =="undefined"){tm=10;}
			try{
			var inputs=$(".auto-complete-off,input[autocomplete=off]"); 
			setTimeout(function(){
				inputs.each(function(){     
					var old_value=$(this).attr("value");            
					var thisobj=$(this);            
					setTimeout(function(){  
						thisobj.removeClass("auto-complete-off").addClass("auto-complete-off-processed");
						thisobj.val(old_value);
					},tm);
				 });
			 },tm); 
			}catch(e){}
		  }
		$(function(){                                              
			setAutoCompleteOFF();
		})
	</script>
    <script src="editor/js/bootstrap.min.js"></script>  
    <script src="editor/assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
