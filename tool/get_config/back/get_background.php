<?php

require_once('./common_func.php');

echo ">>>>>>>>>>>>>>>> begin to get background config\n";

$base_dir = dirname(dirname(__FILE__));

$base_back_dir = $base_dir . "/background";

$origin_dir = dirname(dirname($base_dir));

$base_names = get_path_dir_and_file($base_back_dir, 1);

$save_path = $base_back_dir . "/config.json";
$save_data = array();

foreach ($base_names as $key => $value) {
	$cur_back_path = $base_back_dir . "/" . $value;
	if(is_dir($cur_back_path)){
		echo "\tsave background file dir " . $value . "\n";
		$data = analyse_single_background($cur_back_path);
		$save_data[$value] = $data;
	}
}

$save_json = json_encode($save_data);
$ssss = stripslashes($save_json);
save_json_file($save_path, $ssss);

echo "<<<<<<<<<<<<<<<< end to get background config\n";

$g_id = 0;
function analyse_single_background($path){
	global $origin_dir;
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
			$r["path"] = get_relative_path($cur_image_path, $origin_dir);
			$r["icon"] = substr($r["path"], 0, -4) . "_icon.png";
			$r["width"] = $info["width"];
			$r["height"] = $info["height"];
			$r["size"] = $info["size"];

			array_push($res, $r);
		}
	}

	return $res;
}