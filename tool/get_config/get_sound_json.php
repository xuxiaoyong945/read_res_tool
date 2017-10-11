<?php
require_once('./common_func.php');

echo ">>>>>>>>>> start get sound json\n";
$save_path = dirname(dirname(dirname(dirname(__FILE__)))) . "/res/tool/sound/sound.json";
$base_dir = dirname(dirname(__FILE__));
$base_sound_dir = $base_dir . "/sound";
$error_flag = false; // 是否含有错误
$g_id = 0;//id的计数

$db = new mysqli(HOST, USER, PASSWORD, TABLE);
if(mysqli_connect_error()){
	echo ERROR_MYSQL_CONNECT . "\n";
	exit;
}

mysqli_query($db,'set names utf8');
$save_data = array();

$sound_result = mysqli_query($db, 'select * from sound');
$sound_res = get_mysql_data_by_result($sound_result);
$save_data["type"] = get_sound_type_data($sound_res);
$save_data["lib"] = get_sound_lib_data();

// 检查整个sound是否有重名的
$sound_names = array();
foreach ($save_data["lib"] as $key => $value) {
	foreach ($value as $k => $v) {
		if(array_key_exists($v["name"], $sound_names)) {
			$error_flag = true;
			echo ERROR_BACKGROUND_RENAMED . " " . $v["name"] . " in " . $sound_names[$v["name"]] . " : " . $key . "\n";
		}else {
			$sound_names[$v["name"]] = $key;
		}
	}
}

if ($error_flag) {
	echo ERROR_SOUND . "\n";
	exit();
}

save_json_data($save_path, $save_data);

echo ">>>>>>>>>> end get sound json\n";

function get_sound_type_data($data){
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
	global $save_data;
	$res = array();
	$types = $save_data["type"];

	foreach ($types as $key => $value) {
		$cur_sound_path = $base_sound_dir . "/" . $value["tag"];
		if(is_dir($cur_sound_path)){
			$data = get_sound_single_data($cur_sound_path, $value["tag"]);
			$res[$value["tag"]] = $data;
		}
	}

	return $res;
}

function get_sound_single_data($path, $type) {
	global $error_flag;
	global $base_dir;
	global $g_id;

	$sounds_name = get_path_dir_and_file($path, 2);
	$res = array();

	$back_str = ".mp3";
	$back_len = strlen($back_str);
	foreach ($sounds_name as $key => $value) {
		if(strlen($value) < 4) {
			$error_flag = true;
			echo ERROR_SOUND_TYPE . " type:" . $type . " name:" . $value . "\n";
			continue;
		}

		if(substr($value, -4) != ".mp3") {
			$error_flag = true;
			echo ERROR_SOUND_TYPE . " type:" . $type . " name:" . $value . "\n";
		}
	 	if(strlen($value) > $back_str && substr($value, -$back_len) == $back_str){
	 		$cur_sound_path = $path . "/" . $value;
	 		$name = substr($value, 0, -$back_len);
	 		$g_id = $g_id + 1;
	 		$info = get_sound_info($cur_sound_path);
	 		$r = array();
	 		$r["id"] = $g_id;
	 		$r["name"] = $name;
	 		if(!check_name_vaild($name)) {
				$error_flag = true;
				echo ERROR_SOUND_NAME . " type:" . $type . " name:" . $name . "\n";
			}
	 		$r["path"] = get_relative_path($cur_sound_path, $base_dir);
	 		$r["len"] = $info["time"];
	 		$r["size"] = $info["size"];
	 		if($info["size"] != 3200) {
	 			$error_flag = true;
	 			echo ERROR_SOUND_BITRATE . " type:" . $type . " name:" . $name . " bitrate: " . $bitrate . "\n";
	 		}
	 		array_push($res, $r);
	 	}
	} 

	return $res;
}