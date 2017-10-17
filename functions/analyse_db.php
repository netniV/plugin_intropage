<?php

function analyse_db() {
	global $config;
	
	$result = array(
		'name' => 'Database check',
		'alarm' => 'green',
		'data' => '',
	);
	
	$damaged = 0;
	$memtables = 0;
	$db_check_level = read_config_option('intropage_db_check_level');
	
	$tables = db_fetch_assoc ("SHOW TABLES");
	foreach($tables as $key=>$val) {
		$row = db_fetch_row("check table ".current($val)." $db_check_level");
		if (preg_match('/^note$/i',$row["Msg_type"]) && preg_match('/doesn\'t support/i',$row["Msg_text"])) { $memtables++; }
		elseif (!preg_match('/OK/i',$row["Msg_text"])) { $damaged++; $result['detail'] .= "Table " . $row["Table"] . " status " . $row["Msg_text"] . "<br/>\n"; }
	}
	
	if ($damaged) { 
	    $return['alarm'] = "red";
	    $result['data'] = "<span class=\"txt_big\">DB problem</span><br/><br/>"; 
	}
	else	{
	    $result['data'] = "<span class=\"txt_big\">DB OK</span><br/><br/>"; 
	}	

// connecton errors
	$cerrors = 0;
	$con_err = db_fetch_assoc ("SHOW GLOBAL STATUS LIKE '%Connection_errors%'");

	foreach($con_err as $key=>$val) {
	    $cerrors = $cerrors + $val['Value'];
	    $return['alarm'] = "red";
	
	}

        $result['data'] .= "Connection errors: $cerrors<br/>";

	
	$result['data'] .= "Damaged tables: $damaged<br/>Memory tables: $memtables<br/>All tables: " . count($tables);
	
	return $result;
}

?>