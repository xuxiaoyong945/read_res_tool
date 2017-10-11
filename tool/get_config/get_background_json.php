<?php
require_once('./common_func.php');

echo ">>>>>>>>>> start get background json\n";

$save_path = dirname(dirname(dirname(dirname(__FILE__)))) . "/res/tool/background/background.json";
$base_dir = dirname(dirname(__FILE__));
$base_back_dir = $base_dir . "/background";
$error_flag = false; // 是否含有错误
$g_id = 0;//id的计数

$db = new mysqli(HOST, USER, PASSWORD, TABLE);
if(mysqli_connect_error()){
	echo ERROR_MYSQL_CONNECT . "\n";
	exit;
}

mysqli_query($db,'set names utf8');
$background_result = mysqli_query($db, 'select * from background');
$background_res = get_mysql_data_by_result($background_result);

$save_data = array();
$save_data["type"] = get_background_data_type($background_res);
$save_data["lib"] = get_background_data_lib($save_data, $base_back_dir);

//check background renamed
$background_names = array();
foreach ($save_data["lib"] as $key => $value) {
	foreach ($value as $k => $v) {
		if(array_key_exists($v["name"], $background_names)) {
			$error_flag = true;
			echo ERROR_BACKGROUND_RENAMED . " " . $v["name"] . " " . $background_names[$v["name"]] . " : " . $key . "\n";
		}else {
			$background_names[$v["name"]] = $key;
		}
	}
}

if ($error_flag) {
	echo ERROR_BACKGROUND . "\n";
	exit();
}

save_data($save_path, $save_data);

echo ">>>>>>>>>> end get background json\n";

function get_background_data_type($data) {
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

function get_background_data_lib($save_data, $base_back_dir) {
	$res = array();
	$types = $save_data["type"];
	foreach ($types as $key => $value) {
		$cur_back_path = $base_back_dir . "/" . $value["tag"];
			if(is_dir($cur_back_path)){
			$data = get_background_type_data($cur_back_path, $value["tag"]);
			$res[$value["tag"]] = $data;
		}
	}

	return $res;
}

function get_background_type_data($path, $type) {
	$images_name = get_path_dir_and_file($path, 2);

	$back_str = "_icon.png";
	$back_len = strlen($back_str);
	foreach ($images_name as $key => $value) {
		if(strlen($value) < 4) {
			$error_flag = true;
			echo ERROR_BACKGROUND_TYPE . " type:" . $type . " name:" . $name . "\n";
			continue;
		}
		if(substr($value, -4) != ".png") {
			$error_flag = true;
			echo ERROR_BACKGROUND_TYPE . " type:" . $type . " name:" . $name . "\n";
		}


		if(strlen($value) > $back_len && substr($value, -$back_len) == $back_str){
			$name = substr($value, 0, -$back_len);
			$cur_icon_path = $path . "/" . $value;
			$cur_image_path = $path . "/" . $name . ".png";
			$info = get_image_info($cur_image_path);

			$g_id = $g_id + 1;
			$r = array();
			$r["id"] = $g_id;
			$r["name"] = $name; //去掉 .png
			if (!check_name_vaild($name)) {
				$error_flag = true;
				echo ERROR_BACKGROUND_NAME . " type:" . $type . " name:" . $name . "\n";
			}
			$r["path"] = get_relative_path($cur_image_path, $base_dir);
			$r["icon"] = substr($r["path"], 0, -4) . "_icon.png";
			$r["width"] = $info["width"];
			$r["height"] = $info["height"];
			$r["size"] = $info["size"];

			if ($info["size"] > BACKGROUND_IMAGE_SIZE && DEBUG_WARNING) {
				echo WARNING_BACKGROUND_IMAGE_SIZE . " type:" . $type . " name:" . $name . " size: " . $info["size"] . "\n";
			}

			array_push($res, $r);
		}
	}

	return $res;
}