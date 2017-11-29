<?php

//sometimes forwardID is provided, get appropriate connection
if(isset($_REQUEST['forwardID'])) {
	$fwd=query2Array("select connectionID from forwards where forwardID='{$_REQUEST['forwardID']}' ");
	$_SESSION['connectionID']=$fwd[0]['connectionID'];
	$connID=$_SESSION['connectionID'];
} else {
	if(isset($_GET['connectionID'])) {	
		$_SESSION['connectionID'] = $_GET["connectionID"];
	}

	$connID=$_SESSION['connectionID'];
}

if($connID=="ALL") {
	echo "<h1 class=\"page-header\">Relays (all connections)</h1>";
} else {
	
	$name=query2Array("select Name from connections where ID='{$connID}' ");
	
	echo "<h1 class=\"page-header\">Relays from {$name[0]['Name']}</h1>";
}
?>
	
	<div class="table-responsive">        
<?php          
// create LM object, pass in PDO connection
$lm = new lazy_mofo($dbh); 

// table name for updates, inserts and deletes
$lm->table = 'forwards';


// identity / primary key for table
$lm->identity_name = 'forwardID';

$lm->rename['connectionID'] = 'Associated connection';
$lm->rename['description'] = 'Service name';

// optional, define what is displayed on edit form. identity id must be passed in also.  


if($_SESSION['connectionID']=="ALL") {
	$lm->grid_sql = "
		select (case when type=0 then 'On-Prem to Cloud' else 'Cloud to On-prem' end) as type, Name as connectionName,description,localPort,remoteTargetHost,remoteTargetPort,forwardID 
		from forwards lf
		left join connections c on lf.connectionID=c.ID
		order by type, description
	";
}
else 
{
	$lm->grid_sql = "
		select (case when type=0 then 'On-Prem to Cloud' else 'Cloud to On-prem' end) as type, description,virtualHost,localPort,remoteTargetHost,remoteTargetPort,forwardID 
		from forwards lf
		left join connections c on lf.connectionID=c.ID
		where lf.connectionID=:connectionID 
		order by type, description
	";
	
	//$lm->grid_input_control['virtualHost'] = "--checkbox";
	$lm->grid_output_control['virtualHost'] = "--outputVHost";
	
	function outputVHost($column_name, $value, $command, $called_from) {
		return $value == 0 ? "": "Yes";
	}

	$lm->grid_sql_param[":connectionID"] = @$_SESSION['connectionID']; 
}

$lm->form_input_control['type'] = "select 0, 'On-prem to Cloud' union select 1, 'Cloud to On-prem'; --select";
$lm->form_input_control['virtualHost'] = "select 0, 'Do not expose as Virtual Host' union select 1, 'Expose as Virtual Host'; --select";
$lm->form_input_control['description'] = "--renderDescription";

function renderDescription($column_name, $value, $command, $called_from){	
	return "<input type='text' name='description' class='lm_description' style='text-align:right;width:100px;' value='{$value}' size='35' placeholder=''><font color='#cccccc'>." . getConfigValue("Virtual host domain") . ":" . getConfigValue("Virtual host port") . "</font>";
}

$lm->pagination_text_records="relays";
$lm->select_first_option_blank=false;

//derived forward ID from connection ID selected when this is not available (workflow to select all fowards)

if($connID=="ALL" && isset($_REQUEST['forwardID'])) {
	$forwardID=$_REQUEST['forwardID'];	
	$fwd=query2Array("select connectionID from forwards where forwardID='{$forwardID}' ");
	$connID=$fwd[0]['connectionID'];
} 

if(isset($_REQUEST['forwardID'])) {
	
	$lm->form_sql = "
		select 
		 connectionID, type, description,virtualHost,localPort,remoteTargetHost,remoteTargetPort,forwardID 
		from  forwards lf
		left join connections c on c.ID=lf.connectionID
		where forwardID = :forwardID
	";

	
	$lm->form_input_control['connectionID'] = 'select ID, Name from connections; --select';
} else {
	$lm->form_sql = "
		select 
		  description,type,virtualHost,localPort,remoteTargetHost,remoteTargetPort,forwardID 
		from  forwards lf		
		where forwardID = :forwardID
	";
	
	
	$lm->form_additional_html= "<input type='hidden' name='connectionID' value='" . $connID . "'>";
}

$lm->form_sql_param[":$lm->identity_name"] = @$_REQUEST[$lm->identity_name]; 


$lm->on_insert_validate['localPort'] = array('/.+/', 'Missing local port name that is used for the tunnel', 'Required'); 
$lm->on_insert_validate['remoteTargetHost'] = array('/.+/', 'Missing Target Hostname', 'Required'); 
$lm->on_insert_validate['remoteTargetPort'] = array('/.+/', 'Missing Target Port', 'Required'); 
$lm->on_insert_validate['description'] = array('/.+/', 'Missing description', 'Required'); 

// copy validation rules to update - same rules
$lm->on_update_validate = $lm->on_insert_validate;  


/**
 * Event processing
 */ 

$lm->on_insert_user_function = 'checkForward';
$lm->on_update_user_function = $lm->on_insert_user_function;
 
$lm->after_insert_user_function = 'newForward';
$lm->after_update_user_function = 'updateForward';
$lm->on_delete_user_function = 'deleteForward';

function checkForward() {
	$v = $_POST['description'];
	if(strlen($v) < 2) {
		return "Service name too short";
	}
	
	if(strstr($v, " ")) {
		return "Service name cannot contain spaces";
	}
	
	if(strtolower($v) != $v) {
		return "Service name cannot contain Upper Case characters";
	}
		
	$pma = preg_match('/[#$%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', $v);
	
	if($pma > 0) {
		return "Service name cannot contain special chars";
	}	
}

function tunnelInfo($forwardID) {
	$row = query2Array("select * from forwards lf left join connections c on lf.connectionID=c.ID where forwardID='{$forwardID}'");	
	return $row[0];
}

function updateForward($id){
	$t = new Tunnel();
	$t->killTunnel($id);
	$t->startTunnel($id);	
}

function newForward($id){
	$t = new Tunnel();
	$t->startTunnel($id);	
}

function deleteForward($id){
	$t = new Tunnel();
	return !$t->killTunnel($id);
}


function errorMsg($title, $err) {
	echo "<div class=\"errordialog\">";
	echo "<div class=\"error\">{$title}</div><br>";
	echo $err;
	echo "</div><br>";
	
	registerEvent("Error", $title . ": " . $err);
}


// use the lm controller
$lm->run();
?>
</div>