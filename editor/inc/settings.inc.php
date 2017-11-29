	<h1 class="page-header">Configuration</h1>  
	<div class="table-responsive">        
<?php          
         // create LM object, pass in PDO connection
$lm = new lazy_mofo($dbh); 

// table name for updates, inserts and deletes
$lm->table = 'settings';

// identity / primary key for table
$lm->identity_name = 'configurationID';

// optional, make friendly names for fields
$lm->rename['configurationName'] = 'Setting';
$lm->rename['configurationValue'] = 'Value';

// optional, define what is displayed on edit form. identity id must be passed in also.  
$lm->grid_sql = "
	select configurationName,configurationValue,configurationID from settings 	
	order by configurationName
";

$lm->grid_delete_link = "";

$lm->pagination_text_records="settings";
$lm->select_first_option_blank=false;
$lm->form_sql = "
	select 
	  *,configurationID
	from  settings
	where configurationID = :configurationID
";
$lm->form_sql_param[":$lm->identity_name"] = @$_REQUEST[$lm->identity_name]; 
$lm->form_input_control['configurationValue'] = "--textarea";
$lm->on_insert_validate['configurationName'] = array('/.+/', 'Missing name', 'Required'); 

// copy validation rules to update - same rules
$lm->on_update_validate = $lm->on_insert_validate;  

$lm->grid_add_link = "";
$lm->grid_delete_link = "";
$lm->grid_export_link = "";
$lm->paginate=false;
$lm->button= "";

$cats = query2Array("select distinct category from settings order by category desc");
foreach($cats as $row) {
	echo "<h3>{$row['category']}</h3>";
	
	$lm->grid_sql = "
	select configurationName,configurationValue,configurationID from settings 
	where category='{$row['category']}'
	order by configurationName
	";
	
	// use the lm controller
	$lm->run();
}

?>
</div>