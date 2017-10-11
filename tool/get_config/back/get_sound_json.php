<?php
require('./common_func.php');
echo ">>>>>>>>>>>>>>>>>>> begin to get test json\n";

$save_path = dirname(dirname(dirname(dirname(__FILE__)))) . "/res/tool/sound/sound.json";

echo "save path " . $save_path . "\n";

$base_dir = dirname(dirname(__FILE__));

$base_sound_dir = $base_dir . "/sound";

$origin_dir = dirname(dirname($base_dir));

$g_id = 0;

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

$sound_result = mysqli_query($db, 'select * from sound');
$sound_res = get_mysql_data_by_result($sound_result);
$save_data["type"] = get_sound_type_data($sound_res);
$save_data["lib"] = get_sound_lib_data();
echo "\tsave sound res\n";

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

function get_sound_type_data($data){
	$res = array();
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

function get_sound_lib_data(){
	global $base_sound_dir;
	$res = array();
	$base_names = get_path_dir_and_file($base_sound_dir, 1);
	foreach ($base_names as $key => $value) {
		$cur_sound_path = $base_sound_dir . "/" . $value;
		if(is_dir($cur_sound_path)){
			echo "\tsave sound file dir " . $value . "\n";
			$data = analyse_single_sound($cur_sound_path);
			$res[$value] = $data;
		}
	}

	return $res;
}

function analyse_single_sound($path){
	global $base_dir;
	global $g_id;

	$sounds_name = get_path_dir_and_file($path, 2);
	$res = array();

	$back_str = ".mp3";
	$back_len = strlen($back_str);

	foreach ($sounds_name as $key => $value) {
	 	if(strlen($value) > $back_str && substr($value, -$back_len) == $back_str){
	 		$cur_sound_path = $path . "/" . $value;
	 		$name = substr($value, 0, -$back_len);
	 		$g_id = $g_id + 1;
	 		$info = get_sound_info($cur_sound_path);
	 		$r = array();
	 		$r["id"] = $g_id;
	 		$r["name"] = $name;
	 		$r["path"] = get_relative_path($cur_sound_path, $base_dir);
	 		$r["len"] = $info["time"];
	 		$r["size"] = $info["size"];
	 		array_push($res, $r);
	 	}
	} 

	return $res;
}