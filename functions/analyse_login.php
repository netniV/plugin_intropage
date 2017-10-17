<?php

function analyse_login() {
	global $config;
	
	$result = array(
		'name' => "Last 10 logins",
		'alarm' => 'green',
		'data' => '',
		'detail' => '',
	);


	$flog = 0;	
	$count = 0;
	$sql_user_log = db_fetch_assoc("SELECT user_log.username, user_auth.full_name, user_log.time, user_log.result, user_log.ip FROM user_auth INNER JOIN user_log ON user_auth.username = user_log.username ORDER  BY user_log.time desc LIMIT 10");
	foreach($sql_user_log as $row) {
	
		if ($row['result'] == 0) {
			$result['alarm'] = "red";
			$flog++;
		    if ($count < 5)
			$result['data'] .= sprintf("<b>%s | %s | %s | %s</b><br/>",$row['time'],$row['ip'],$row['username'],  ($row['result'] == 0)? "failed":"succes");
		    else
			$result['detail'] .= sprintf("<b>%s | %s | %s | %s</b><br/>",$row['time'],$row['ip'],$row['username'], ($row['result'] == 0)? "failed":"succes");
		}
		else {
		    if ($count < 5)
			$result['data'] .= sprintf("%s | %s | %s | %s<br/>",$row['time'],$row['ip'],$row['username'], ($row['result'] == 0)? "failed":"succes");
		    else
			$result['detail'] .= sprintf("%s | %s | %s | %s<br/>",$row['time'],$row['ip'],$row['username'], ($row['result'] == 0)? "failed":"succes");
		}
		$count++;
	}

	$result['data'] = "<span class=\"txt_big\">Failed logins: $flog</span><br/><br/>" . $result['data'];

	$loggin_access = (db_fetch_assoc("select realm_id from user_auth_realm where user_id='" . $_SESSION["sess_user_id"] . "' and user_auth_realm.realm_id=19"))?true:false;
	if ($result['detail'] && $loggin_access)	    
		$result['detail'] .= "<a href=\"" . htmlspecialchars($config['url_path']) . "utilities.php?action=view_user_log\">Full log</a><br/>\n";
	
	return $result;
}

?>