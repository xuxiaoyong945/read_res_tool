<?php
// error_reporting(0);

require_once('./common_func.php');

echo ">>>>>>>>>>>>>>>>begin to check role anim\n";

$base_dir = dirname(dirname(__FILE__));

$base_role_dir = $base_dir . "/role";

$origin_dir = dirname(dirname($base_dir));

$base_names = get_path_dir_and_file($base_role_dir, 1);

foreach ($base_names as $key => $value) {
	$cur_role_path = $base_role_dir . "/" . $value;
	if(is_dir($cur_role_path)){
		// echo "\tcheck role file dir " . $value . "\n";
		analyse_single_role($cur_role_path, $value);	
	}
}

echo "<<<<<<<<<<<<<<<<< end to check role anim\n";

function analyse_single_role($path, $value){
	$armature_path = $path . "/armature";
	if(is_dir($armature_path)){
		$ret = analyse_armature($armature_path, $value);
		check_anim($ret, $value);
	}
}

function analyse_armature($path, $vv){
	// global $origin_dir;

	$ret = array();
	$ret["res_name"] = array();
	$ret["image_name"] = array();
	$ret["anim_name"] = array();

	$armature_names = get_path_dir_and_file($path, 2);

	foreach ($armature_names as $key => $value) {
		if(strlen($value) > 4 && substr($value, -4) == ".xml"){
			$cur_armature_path = $path . "/" . $value;
			$info = get_armature_info($cur_armature_path);
			foreach ($info as $k => $v) {
				array_push($ret["anim_name"], $v);
			}

			array_push($ret["res_name"], substr($value, 0, -4));		
		}

		if(strlen($value) > 4 && substr($value, -4) == ".png"){
			array_push($ret["image_name"], substr($value, 0, -4));
		}
	}
	
	// return check_anim($ret);
	return $ret;
}

function check_anim($ret, $v){
	
	$res1 = $ret["image_name"];
	$res2 = array_merge($ret["res_name"], $ret["anim_name"]);

	$res = array_diff($res2, $res1);

	foreach ($res as $key => $value) {
		echo "\t error name " . $v . " value " . $value . "\n";
	}

	$res = array_diff($res1, $res2);

	foreach ($res as $key => $value) {
		echo "\t error name " . $v . " value " . $value . "\n";
	}
}