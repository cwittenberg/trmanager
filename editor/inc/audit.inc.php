<?php
	//only admins are allowed
	if(!isAdmin()) { 
		registerEvent("Security", "Admin page accessed by non-admin! - Audit");
		die("This attempt is registered");
	}
?>
	<h1 class="page-header">Auditing</h1>  
	<div class="table-responsive">        
<?php          
         // create LM object, pass in PDO connection
$lm = new lazy_mofo($dbh); 


// table name for updates, inserts and deletes
$lm->table = 'audit';

// identity / primary key for table
$lm->identity_name = 'eventID';

// optional, make friendly names for fields
//$lm->rename['userID'] = 'User';

//$lm->form_input_control['password'] = '--password';

// optional, define what is displayed on edit form. identity id must be passed in also.  
$lm->grid_sql = "
	select time,concat(firstName, ' ', lastName) as User, category,IP,description,eventID from audit a
	left join users u on u.ID=a.userID
	order by eventID desc
";

$lm->grid_add_link = "";
$lm->grid_delete_link = "";
$lm->grid_edit_link = "";
$lm->button= "";


$lm->pagination_text_records="events";
$lm->select_first_option_blank=false;
$lm->form_sql = "
	select 
	  select time, category,IP,description,eventID from audit order by eventID desc
	from audit
	where eventID = :eventID
";
$lm->form_sql_param[":$lm->identity_name"] = @$_REQUEST[$lm->identity_name]; 
//$lm->form_sql_param[":clientID"] = $_SESSION['clientID']; 
//$lm->form_input_control['hostTypeID'] = 'select hostTypeID, hostTypeName from host_type; --select';
//$lm->form_additional_html= "<input type='hidden' name='clientID' value='" . $_SESSION['clientID'] . "'>";
$lm->form_input_control['isAdmin'] = "--checkbox";

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
