<?php
require('./common_func.php');
echo ">>>>>>>>>>>>>>>>>>> begin to insert role \n";

$role_path = dirname(dirname(dirname(dirname(__FILE__)))) . "/res/tool/role/role.json";

echo "role path " . $role_path . "\n";

// $base_dir = dirname(dirname(__FILE__));
// $base_back_dir = $base_dir . "/role";
// $origin_dir = dirname(dirname($base_dir));
// $g_id = 0;

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

$role_data = file_get_contents($role_path);

$role_data = json_decode($role_data, true);

// var_dump($role_data);

$role_info = $role_data["lib"];

$insert_data = "";

// $index = 0;

$connect_tag = "";

foreach ($role_info as $key => $value) {
	// if ($index < 5) {
		// $index = $index + 1;
		$insert_data = $insert_data . $connect_tag . "(" . $value["id"] . ", " . $value["type"] . ", \"" . $value["name"] . "\")";
		$connect_tag = ", ";
	// }
}

echo $insert_data . "\n";

$result = mysqli_query($db, "insert into role_info (id, type, name)values " . $insert_data);

// $save_data = array();

// $background_result = mysqli_query($db, 'select * from background');
// $background_res = get_mysql_data_by_result($background_result);
// $save_data["type"] = get_background_data_type($background_res);
// $save_data["lib"] = get_background_data_lib();
// echo "\tsave background res\n";

// $save_json =  urldecode(json_encode($save_data));
// $ssss = stripslashes($save_json);
// save_json_file($save_path, $ssss);

echo "<<<<<<<<<<<<<<<<<<<<< end to insert role\n";

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

function get_background_data_type($data){
	$res = array();

	foreach ($data as $key => $value) {
		$r = array();
		$r["id"] = (int)$value["id"];
		$r["name"] = urlencode($value["name"]);
		$r["tag"] = $value["tag"];

		array_push($res, $r);	
	}
	return $res;
}

function get_background_data_lib(){
	$res = array();
	global $base_back_dir;
	$base_names = get_path_dir_and_file($base_back_dir, 1);

	foreach ($base_names as $key => $value) {
		$cur_back_path = $base_back_dir . "/" . $value;
		if(is_dir($cur_back_path)){
			echo "\tcur_back_path " . $cur_back_path . "\n";
			$data = analyse_single_background($cur_back_path);
			$res[$value] = $data;
		}
	}

	return $res;
}

function analyse_single_background($path){
	global $base_dir;
	global $g_id;
	$images_name = get_path_dir_and_file($path, 2);
	$res = array();

	$back_str = "_icon.png";
	$back_len = strlen($back_str);
	foreach ($images_name as $key => $value) {
		if(strlen($value) > $back_len && substr($value, -$back_len) == $back_str){
			$name = substr($value, 0, -$back_len);
			$cur_icon_path = $path . "/" . $value;
			$cur_image_path = $path . "/" . $name . ".png";
			$info = get_image_info($cur_image_path);

			$g_id = $g_id + 1;

			$r = array();
			$r["id"] = $g_id;
			$r["name"] = $name; //去掉 .png
			$r["path"] = get_relative_path($cur_image_path, $base_dir);
			$r["icon"] = substr($r["path"], 0, -4) . "_icon.png";
			$r["width"] = $info["width"];
			$r["height"] = $info["height"];
			$r["size"] = $info["size"];

			array_push($res, $r);
		}
	}

	return $res;
}