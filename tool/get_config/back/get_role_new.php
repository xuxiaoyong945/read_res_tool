<?php
// error_reporting(0);

require_once('./common_func.php');

echo ">>>>>>>>>>>>>>>>begin to get role config\n";

$base_dir = dirname(dirname(__FILE__));

$base_role_dir = $base_dir . "/role";

// $origin_dir = dirname(dirname($base_dir));  // 这个是旧的方法调用的
$origin_dir = $base_dir; // 这个是新的方法调用的

$base_names = get_path_dir_and_file($base_role_dir, 1);

// $base_names = array();
// array_push($base_names, 'a_1');

foreach ($base_names as $key => $value) {
	$cur_role_path = $base_role_dir . "/" . $value;
	if(is_dir($cur_role_path)){
		echo "\tsave role file dir " . $value . "\n";
		analyse_single_role($cur_role_path);	
	}
}

echo "<<<<<<<<<<<<<<<<< end to get role config\n";

function analyse_single_role($path){
	$save_path = $path . "/config.json";
	$save_data = array();

	$sound_path = $path . "/sound";
	if(is_dir($sound_path)){
		$sound_data = analyse_sound($sound_path);
		$save_data["sound"] = $sound_data;
	}else{
		$save_data["sound"] = array();
	}

	$image_path = $path . "/image";
	if(is_dir($image_path)){
		$image_data = analyse_image($image_path);
		$save_data["image"] = $image_data;
	}else{
		$save_data["image"] = array();
	}

	$armature_path = $path . "/armature";
	if(is_dir($armature_path)){
		$armature_data = analyse_armature($armature_path);
		$save_data["ani_res"] = $armature_data["ani_res"];
		$save_data["ani_list"] = $armature_data["ani_list"];
	}else{
		$save_data["ani_res"] = array();
		$save_data["ani_list"] = array();
		$save_data["ani_res"]["path"] = array();
	}
	

	$frames_path = $path . "/frames";
	if(is_dir($frames_path)){
		$frames_data = analyse_frames($frames_path);
		$save_data["frames"] = $frames_data;
	}else{
		$save_data["frames"] = array();
	}
	
	$save_json = json_encode($save_data);

	$ssss = stripslashes($save_json);

	save_json_file($save_path, $ssss);
}

function analyse_sound($path){
	global $origin_dir;
	$res = array();
	$sound_names = get_path_dir_and_file($path, 2);
	foreach ($sound_names as $key => $value) {
		$cur_sound_path = $path . "/" . $value;
		$info = get_sound_info($cur_sound_path);

		$r = array();
		$r["name"] = substr($value, 0, -4); //去掉 .mp3
		$r["path"] = get_relative_path($cur_sound_path, $origin_dir);
		$r["len"] = $info["time"];
		$r["size"] = $info["size"];
		array_push($res, $r);
	}

	return $res;
}

function analyse_image($path){
	global $origin_dir;
	$res = array();
	$image_names = get_path_dir_and_file($path, 2);
	foreach ($image_names as $key => $value) {
		$cur_image_path = $path . "/" . $value;
		$info = get_image_info($cur_image_path);

		$r = array();
		$r["name"] = substr($value, 0, -4); //去掉 .png
		$r["path"] = get_relative_path($cur_image_path, $origin_dir);
		$r["width"] = $info["width"];
		$r["height"] = $info["height"];
		$r["size"] = $info["size"];

		array_push($res, $r);
	}
	return $res;
}

function analyse_armature($path){
	global $origin_dir;
	$res = array();
	$res["ani_res"] = array();
	$res["ani_res"]["path"] = array();
	$res["ani_list"] = array();

	$armature_names = get_path_dir_and_file($path, 2);
	foreach ($armature_names as $key => $value) {
		if(strlen($value) > 4 && substr($value, -4) == ".xml"){
			$cur_armature_path = $path . "/" . $value;
			$info = get_armature_info($cur_armature_path);
			foreach ($info as $k => $v) {
				array_push($res["ani_list"], $v);
			}

			$p = get_relative_path($cur_armature_path, $origin_dir);
			$r = substr($p, 0, -4);
			array_push($res["ani_res"]["path"], $r);			
		}
	}
	return $res;
}

function analyse_frames($path){
	global $origin_dir;
	$res = array();
	$frames_names = get_path_dir_and_file($path, 2);
	foreach ($frames_names as $key => $value) {
		if(strlen($value) > 6 && substr($value, -6) == ".plist"){
			$cur_frames_path = $path . "/" . $value;
			$len = get_frames_len($cur_frames_path);

			$name = substr($value, 0, -6);
			$p = get_relative_path($cur_frames_path, $origin_dir);
			$p = substr($p, 0, -6);
			// $r = array("name"=>$name, "path"=>$p);
			$r = array("name"=>$name, "path"=>$p, "len"=>$len);
			array_push($res, $r);
		}
	}

	return $res;
}