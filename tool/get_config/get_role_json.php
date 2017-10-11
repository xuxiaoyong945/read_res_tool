<?php
require('./common_func.php');
echo ">>>>>>>>>>>>>>>>>>> begin to get test json\n";

$save_path = dirname(dirname(dirname(dirname(__FILE__)))) . "/res/tool/role/role.json";

echo "save path " . $save_path . "\n";

$host = "192.168.2.22";
$user = "bbm";
$password = "123456";
$bbm = "bbm";

$db = new mysqli($host, $user, $password, $bbm);

if(mysqli_connect_error()){
	echo "connect mysql error\n";
	exit;
}else{
	echo "\tconnect mysql succ\n";
}

mysqli_query($db,'set names utf8');

$save_data = array();

$role_result = mysqli_query($db, "select * from role");
$role_res = get_mysql_data_by_result($role_result);

$role_info_result = mysqli_query($db, "select * from role_info");
$role_info_res = get_mysql_data_by_result($role_info_result);

$save_data = get_role_data($role_res, $role_info_res);
echo "\tsave role res\n";

$save_json =  urldecode(json_encode($save_data));
$ssss = stripslashes($save_json);
save_json_file($save_path, $ssss);

echo "<<<<<<<<<<<<<<<<<<<<< end to get test json\n";

function get_mysql_data_by_result($result){
	$res = array();
	if($result){
		for ($i=0; $i < $result->num_rows; $i++) { 
			$row = $result->fetch_assoc();
	        array_push($res, $row);
		}
	}
	return $res;
}

function get_role_data($data, $data_info){
	$role_path = "role/";
	$res = array();
	$res["type"] = array();

	foreach ($data as $key => $value) {
		$r = array();
		$r["id"] = (int)$value["id"];
		$r["name"] = urlencode($value["name"]);
		$r["tag"] = $value["tag"];

		array_push($res["type"], $r);
	}

	$res["lib"] = array();
	foreach ($data_info as $key => $value) {
		$r = array();
		$r["id"] = (int)$value["id"];
		$r["name"] = $value["name"];
		$r["type"] = (int)$value["type"];
		$r["path"] = $role_path . $r["name"];

		if($value["path"] != ""){
			$r["path"] = $role_path . $value["path"];	
		}

		$r["icon"] = $r["path"] . "/" . $r["name"] . "_icon.png";

		if($value["icon"] != ""){
			$r["icon"] = $r["path"] . "/" . $value["icon"] . "_icon.png";
		}

		array_push($res["lib"], $r);
	}

	return $res;
}