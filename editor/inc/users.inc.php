<?php
	//only admins are allowed
	if(!isAdmin()) { 
		registerEvent("Security", "Admin page accessed by non-admin! - Users");
		die("This attempt is registered");
	}
?>
	<h1 class="page-header">Users</h1>  
	<div class="table-responsive">        
<?php          
         // create LM object, pass in PDO connection
$lm = new lazy_mofo($dbh); 


// table name for updates, inserts and deletes
$lm->table = 'users';

// identity / primary key for table
$lm->identity_name = 'ID';

// optional, make friendly names for fields
$lm->rename['isAdmin'] = 'Administrator';

$lm->grid_input_control['isAdmin'] = '--checkbox';
$lm->form_input_control['password'] = '--password';

// optional, define what is displayed on edit form. identity id must be passed in also.  
$lm->grid_sql = "
	select firstName,lastName,email, isAdmin, ID from users order by lastName,firstName
";

$lm->on_update_user_function = 'validatePass';

function checkPassRules($pwd, &$errors) {
    $errors_init = $errors;

    if (strlen($pwd) < 8) {
        $errors[] = "Password too short!";
    }

    if (!preg_match("#[0-9]+#", $pwd)) {
        $errors[] = "Password must include at least one number!";
    }

    if (!preg_match("#[a-zA-Z]+#", $pwd)) {
        $errors[] = "Password must include at least one letter!";
    }     
    
    if (!preg_match('/[^a-zA-Z\d]/', $pwd)) {
		$errors[] = "Password must include at least one special character!";
	}       

    return ($errors == $errors_init);
}

//validate and crypt to md5 for storage in DB
function validatePass(){		
	if(isset($_POST['password'])) {
		$pass = $_POST['password'];
		$errs = array();
		checkPassRules($pass, $errs);
		$errTxt=implode("<br>", $errs);
		
		if(sizeof($errs)==0) {		
			$_POST['password'] = md5($_POST['password']);
		} else {
			return $errTxt;
		}
	}
}


$lm->pagination_text_records="users";
$lm->select_first_option_blank=false;
$lm->form_sql = "
	select 
	  firstName,lastName,email,password, isAdmin, ID 
	from users
	where ID = :ID
";
$lm->form_sql_param[":$lm->identity_name"] = @$_REQUEST[$lm->identity_name]; 
//$lm->form_sql_param[":clientID"] = $_SESSION['clientID']; 
//$lm->form_input_control['hostTypeID'] = 'select hostTypeID, hostTypeName from host_type; --select';
//$lm->form_additional_html= "<input type='hidden' name='clientID' value='" . $_SESSION['clientID'] . "'>";
$lm->form_input_control['isAdmin'] = "select 1, 'Yes' union select 0, 'No'; --radio";

$lm->button= "";


// optional, validation. input:  regular expression (with slashes), error message, tip/placeholder
// first element can also be a user function or 'email'
//$lm->on_insert_validate['firstName'] = array('/.+/', 'Missing first name', 'Required'); 
//$lm->on_insert_validate['lastName'] = array('/.+/', 'Missing last name', 'Required'); 
//$lm->on_insert_validate['email'] = array('/.+/', 'Missing email', 'Required'); 


// copy validation rules to update - same rules
$lm->on_update_validate = $lm->on_insert_validate;  


// use the lm controller
$lm->run();
?>
</div>
