	<h1 class="page-header">Connections</h1>  
	<div class="table-responsive">        
<?php          
// create LM object, pass in PDO connection
$lm = new lazy_mofo($dbh); 

// table name for updates, inserts and deletes
$lm->table = 'connections';


// identity / primary key for table
$lm->identity_name = 'ID';

// optional, make friendly names for fields
$lm->rename['SSHHostName'] = 'SSH Host';
$lm->rename['SSHPort'] = 'SSH Port';
$lm->rename['SSHUser'] = 'SSH User';
$lm->rename['SSHKey'] = 'SSH Key';
$lm->rename['TCPKeepAlive'] = 'TCP Keep Alive';

// optional, define editable input controls on the grid
//$lm->grid_input_control['is_active'] = '--checkbox';
$lm->grid_input_control['SSHKey'] = '--text';
$lm->grid_input_control['Compression'] = '--checkbox';


$lm->grid_input_control['Operations'] = '--forwardsLink';

function forwardsLink($column_name, $value, $command, $called_from){

    // $column_name: field name
    // $value: field value  
    // $command: full command as defined in the arrays: form_input_control, grid_input_control, or grid_output_control
    // $called_from: which function called this user function; form, or grid

    global $lm;
    $val = $lm->clean_out($value);
    return "<a href='?page=forwards&connectionID=$val'>[Forwards]</a>";    
}

// optional, define what is displayed on edit form. identity id must be passed in also.  
$lm->grid_sql = "
	select Name,SSHUser,SSHHostName, SSHPort,ID as 'Operations',ID from connections order by Name
";
//$lm->grid_input_control['hostTypeID'] = 'select hostTypeID, hostTypeName from host_type; --select';
//$lm->grid_sql_param[":clientID"] = $_SESSION['clientID']; 

$lm->pagination_text_records="connections";
$lm->select_first_option_blank=false;
$lm->form_sql = "
	select 
	  * 
	from  connections
	where ID = :ID
";
$lm->form_sql_param[":$lm->identity_name"] = @$_REQUEST[$lm->identity_name]; 
$lm->form_input_control['Compression'] = "select 'yes', 'Yes' union select 'no', 'No'; --radio";
$lm->form_input_control['SSHKey'] = "--decryptedKeyArea";
$lm->form_input_control['SSHPort'] = "--number";
$lm->form_input_control['ServerAliveInterval'] = "--number";
$lm->form_input_control['TCPKeepAlive'] = "select 'yes', 'Yes' union select 'no', 'No'; --radio";

$lm->button= "";


// optional, validation. input:  regular expression (with slashes), error message, tip/placeholder
// first element can also be a user function or 'email'
$lm->on_insert_validate['SSHHostName'] = array('/.+/', 'Missing SSH Host Name', 'Required'); 
$lm->on_insert_validate['SSHPort'] = array('/.+/', 'Missing SSH Port', 'Required'); 
$lm->on_insert_validate['SSHUser'] = array('/.+/', 'Missing SSH User', 'Required'); 
$lm->on_insert_validate['SSHKey'] = array('/.+/', 'Missing SSH Key', 'Required'); 

// copy validation rules to update - same rules
$lm->on_update_validate = $lm->on_insert_validate;  


//encrypt Key before storage in DB
$lm->on_update_user_function = 'encryptKey';
$lm->on_insert_user_function = $lm->on_update_user_function;

function encryptKey(){
	if(mb_strlen($_POST['SSHKey']) < 10)
		return "Not a valid key";

	if(isset($_POST['SSHKey'])) {
		$c = new Cipher();		
		$_POST['SSHKey'] = $c->encrypt($_POST['SSHKey']);
	}		
}

function decryptedKeyArea($column_name, $value, $command, $called_from) {
	$key = decryptKey($value);
	return "<div id='keyTable'><textarea name='{$column_name}' id='{$column_name}'>{$key}</textarea></div>";
}

function decryptKey($key){
	$c = new Cipher();		
	return $c->decrypt($key);	
}

// use the lm controller
$lm->run();
?>
</div>
