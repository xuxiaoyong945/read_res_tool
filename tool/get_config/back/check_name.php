<?php
// error_reporting(0);

require_once('./common_func.php');

echo ">>>>>>>>>>>>>>>>begin to check name\n";

$base_dir = dirname(dirname(__FILE__));
$base_role_dir = $base_dir . "/role";
$origin_dir = dirname(dirname($base_dir));
$base_names = get_path_dir_and_file($base_role_dir, 1);
foreach ($base_names as $key => $value) {
	if (check_name($value) == false){
		echo "\t error role name " . $value . "\n";
	}
	$cur_role_path = $base_role_dir . "/" . $value;
	if(is_dir($cur_role_path)){
		analyse_single_role($cur_role_path, $value);	
	}
}

echo "<<<<<<<<<<<<<<<<< end to check name\n";

function analyse_single_role($path, $value){
	$save_path = $path . "/config.json";
	$save_data = array();

	$sound_path = $path . "/sound";
	if(is_dir($sound_path)){
		$sound_data = analyse_sound($sound_path, $value);
		$save_data["sound"] = $sound_data;
	}

	$image_path = $path . "/image";
	if(is_dir($image_path)){
		$image_data = analyse_image($image_path, $value);
		$save_data["image"] = $image_data;
	}

	$armature_path = $path . "/armature";
	if(is_dir($armature_path)){
		$armature_data = analyse_armature($armature_path, $value);
		$save_data["ani_res"] = $armature_data["ani_res"];
		$save_data["ani_list"] = $armature_data["ani_list"];
	}
	

	$frames_path = $path . "/frames";
	if(is_dir($frames_path)){
		$frames_data = analyse_frames($frames_path, $value);
		$save_data["frames"] = $frames_data;
	}
	
	// $save_json = json_encode($save_data);

	// $ssss = stripslashes($save_json);

	// save_json_file($save_path, $ssss);
}

function analyse_sound($path, $vv){
	global $origin_dir;
	$res = array();
	$sound_names = get_path_dir_and_file($path, 2);
	foreach ($sound_names as $key => $value) {

		if(check_name(substr($value, 0, -4)) == false){
			echo "\terror sound name " . substr($value, 0, -4) . " role " . $vv . "\n";
		}

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

function analyse_image($path, $vv){
	global $origin_dir;
	$res = array();
	$image_names = get_path_dir_and_file($path, 2);
	foreach ($image_names as $key => $value) {

		if(check_name(substr($value, 0, -4)) == false){
			echo "\terror image name " . substr($value, 0, -4) . " role " . $vv . "\n";
		}

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

function analyse_armature($path, $vv){
	global $origin_dir;
	$res = array();
	$res["ani_res"] = array();
	$res["ani_res"]["path"] = array();
	$res["ani_list"] = array();

	$armature_names = get_path_dir_and_file($path, 2);
	foreach ($armature_names as $key => $value) {
		if(strlen($value) > 4 && substr($value, -4) == ".xml"){

			if(check_name(substr($value, 0, -4)) == false){
				echo "\terror anim name " . substr($value, 0, -4) . " role " . $vv . "\n";
			}

			$cur_armature_path = $path . "/" . $value;
			$info = get_armature_info($cur_armature_path);
			foreach ($info as $k => $v) {

				if(check_name($v) == false){
					echo "\terror anim_list name " . $v . " role " . $vv . "\n";
				}

				array_push($res["ani_list"], $v);
			}

			$p = get_relative_path($cur_armature_path, $origin_dir);
			$r = substr($p, 0, -4);
			array_push($res["ani_res"]["path"], $r);			
		}
	}
	return $res;
}

function analyse_frames($path, $vv){
	global $origin_dir;
	$res = array();
	$frames_names = get_path_dir_and_file($path, 2);
	foreach ($frames_names as $key => $value) {
		if(strlen($value) > 6 && substr($value, -6) == ".plist"){

			if(check_name(substr($value, 0, -6)) == false){
				echo "\terror frames name " . substr($value, 0, -6) . " role " . $vv . "\n";
			}

			$cur_frames_path = $path . "/" . $value;
			$len = get_frames_len($cur_frames_path);

			$name = substr($value, 0, -6);

			$p = get_relative_path($cur_frames_path, $origin_dir);

			$path = $p;
			$icon = $p;

			$r = array("name"=>$name, "len"=>$len, "path"=>"", "icon"=>"");

			array_push($res, $r);
		}
	}

	return $res;
}