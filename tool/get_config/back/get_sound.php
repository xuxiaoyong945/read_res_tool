<?php
require('./common_func.php');

echo ">>>>>>>>>>>>>>>>>>> begin to get sound config\n";

$base_dir = dirname(dirname(__FILE__));

$base_sound_dir = $base_dir . "/sound";

$origin_dir = dirname(dirname($base_dir));

$base_names = get_path_dir_and_file($base_sound_dir, 1);

$save_path = $base_sound_dir . "/config.json";

$save_data = array();

$g_id = 0;

foreach ($base_names as $key => $value) {
	$cur_sound_path = $base_sound_dir . "/" . $value;
	if(is_dir($cur_sound_path)){
		echo "\tsave sound file dir " . $value . "\n";
		$data = analyse_single_sound($cur_sound_path);
		$save_data[$value] = $data;
	}
}

$save_json = json_encode($save_data);
$ssss = stripslashes($save_json);
save_json_file($save_path, $ssss);

echo "<<<<<<<<<<< end to get sound config\n";

function analyse_single_sound($path){
	global $origin_dir;
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
	 		$r["path"] = get_relative_path($cur_sound_path, $origin_dir);
	 		$r["len"] = $info["time"];
	 		$r["size"] = $info["size"];
	 		array_push($res, $r);
	 	}
	} 

	return $res;
}
