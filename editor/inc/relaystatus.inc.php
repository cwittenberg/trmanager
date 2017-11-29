	<h1 class="page-header">Relay status dashboard</h1>  
	<div class="table-responsive">        
<?php          
// create LM object, pass in PDO connection
$lm = new lazy_mofo($dbh); 


// table name for updates, inserts and deletes
$lm->table = 'status';


// identity / primary key for table
$lm->identity_name = 'ID';

// optional, make friendly names for fields
$lm->rename['SSHHostName'] = 'SSH Host';
$lm->rename['localPort'] = 'Port';
$lm->rename['SSHUser'] = 'SSH User';
$lm->rename['SSHKey'] = 'SSH Key';
$lm->rename['TCPKeepAlive'] = 'TCP Keep Alive';

$lm->grid_input_control['SSHKey'] = '--text';
$lm->grid_input_control['Compression'] = '--checkbox';


$lm->grid_input_control['Operations'] = '--forwardsLink';
$lm->grid_input_control['localPort'] = '--portLink';

function forwardsLink($column_name, $value, $command, $called_from){

    // $column_name: field name
    // $value: field value  
    // $command: full command as defined in the arrays: form_input_control, grid_input_control, or grid_output_control
    // $called_from: which function called this user function; form, or grid

    global $lm;
    $val = $lm->clean_out($value);
    return "<a href='?page=forwards&connectionID=$val'>[Forwards]</a>";    
}

function portLink($column_name, $value, $command, $called_from){
	$host = getConfigValue("Tunnel Relay Manager URL");
	return "<a href=\"{$host}:{$value}\" target=\"_blank\">{$value}</a>";
}


$lm->grid_add_link = "";
$lm->grid_delete_link = "";
$lm->grid_edit_link = "";
$lm->button= "";

// optional, define what is displayed on edit form. identity id must be passed in also.  
$lm->grid_sql = "
			select c.ID as Connection, description, localPort,concat(remoteTargetHost, ':', remoteTargetPort) as Target, PID, forwardID as Status, 
			timediff(now(),s.activeSince) as ActiveSince,
			ID from forwards lf
			left join connections c on lf.connectionID=c.ID
			left join status s on s.localForwardID=lf.forwardID
			where type=0
			order by ID,type,PID";						


function getStatus($column_name, $value, $command, $called_from){
	$rows = query2Array("select * from forwards lf left join connections c on lf.connectionID=c.ID left join status s on s.localForwardID=lf.forwardID where forwardID='{$value}'");	
	$row = $rows[0];
	
	$lfID = $value;
	$localPort = $row['localPort'];
	$PID = $row['PID'];
	
	$PIDActive = isRunning($PID);
	$socketOK = false;
	
	$errno = ""; 
	$errstr = "";
	$timeout=3;	//3 seconds at most

	//test port
	if($row['type']	== 1) {
		$connection = @fsockopen($row['remoteTargetHost'], $row['remoteTargetPort'],$errno, $errstr, $timeout);
	}
	else 
	{
		$connection = @fsockopen("localhost", $localPort,$errno, $errstr, $timeout);
	} 

    if (is_resource($connection)) {        
		$socketOK = true;
        fclose($connection);
    }
	
	if($socketOK) 
		return "<a href=\"?page=forwards&action=edit&forwardID={$lfID}\"><div style='color:green'>ACTIVE</div></a>";	
	else 
		return "<a href=\"?page=forwards&action=edit&forwardID={$lfID}\"><div style='color:red'>INACTIVE</div></a>";
}

function getConnection($column_name, $value, $command, $called_from){	
	$conn=query2Array("select ID,Name from connections where ID=:val", array("val"=>$value));
	$conn=$conn[0];
	
	$lnk="?page=forwards&connectionID={$conn['ID']}";
	return "<a href=\"{$lnk}\">{$conn['Name']}</a>";
}


$lm->grid_output_control['Status'] = '--getStatus';
$lm->grid_output_control['Connection'] = '--getConnection';
			
$lm->pagination_text_records="relays";
$lm->select_first_option_blank=false;
$lm->form_sql = "
	select 
	  * 
	from  connections
	where ID = :ID
";
$lm->form_sql_param[":$lm->identity_name"] = @$_REQUEST[$lm->identity_name]; 
$lm->form_input_control['Compression'] = "select 'yes', 'Yes' union select 'no', 'No'; --radio";
$lm->form_input_control['SSHKey'] = "--textarea";
$lm->form_input_control['SSHPort'] = "--number";
$lm->form_input_control['ServerAliveInterval'] = "--number";
$lm->form_input_control['TCPKeepAlive'] = "select 'yes', 'Yes' union select 'no', 'No'; --radio";



// optional, validation. input:  regular expression (with slashes), error message, tip/placeholder
// first element can also be a user function or 'email'
$lm->on_insert_validate['SSHHostName'] = array('/.+/', 'Missing SSH Host Name', 'Required'); 
$lm->on_insert_validate['SSHPort'] = array('/.+/', 'Missing SSH Port', 'Required'); 
$lm->on_insert_validate['SSHUser'] = array('/.+/', 'Missing SSH User', 'Required'); 
$lm->on_insert_validate['SSHKey'] = array('/.+/', 'Missing SSH Key', 'Required'); 

// copy validation rules to update - same rules
$lm->on_update_validate = $lm->on_insert_validate;  
$lm->grid_add_link="<h3><span class=\"fwdType\"><img src=\"assets/img/cloud-to-onprem.png\">Cloud to On-Prem</span></h3>";

// use the lm controller
$lm->grid_export_link="";
$lm->run();


$lm2 = $lm;
$lm2->grid_add_link="<h3><span class=\"fwdType\"><img src=\"assets/img/onprem-to-cloud.png\">On-prem to Cloud</span></h3>";
$lm2->grid_export_link="";
$lm2->grid_sql = "
			select c.ID as Connection, description, localPort,concat(remoteTargetHost, ':', remoteTargetPort) as Target, PID, forwardID as Status, 
			timediff(now(),s.activeSince) as ActiveSince,
			ID from forwards lf
			left join connections c on lf.connectionID=c.ID
			left join status s on s.localForwardID=lf.forwardID
			where type=1
			order by ID,type,PID";						

$lm2->run();
			
?>
</div>