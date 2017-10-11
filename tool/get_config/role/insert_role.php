<?php
require_once('./../common_func.php');
echo "insert role in sql\n";

$host = "192.168.2.22";
$user = "bbm";
$password = "123456";
$bbm = "bbm";

$db = new mysqli($host, $user, $password, $bbm);

if(mysqli_connect_error()){
	echo "\terror!!! connect mysql error\n";
	exit;
}else{
	echo "\tconnect mysql succ\n";
}

mysqli_query($db,'set names utf8');

$role_result = mysqli_query($db, "select * from role");
$role_res = get_mysql_data_by_result($role_result);

$role_info_result = mysqli_query($db, "select name from role_info");
$role_names = get_mysql_role_names($role_info_result);

$base_dir = dirname(__FILE__);
$file_names = get_path_dir_and_file($base_dir, 1);

$copy_paths = array();
$copy_paths["tag"] = array();
$copy_paths["name"] = array();

$sql_arr = array();

foreach ($file_names as $key => $value) {
	$cur_path = $base_dir . "/" . $value;
	$cur_type = get_role_type($value);

	if($cur_type == -1){
		echo "\t error !!!! get data error\n";
		exit;
	}

	$cur_files = analyse_single_file($cur_path);

	foreach ($cur_files as $key => $v) {
		$r = array();
		$r["type"] = (int)$cur_type;
		$r["name"] = $v;
		array_push($sql_arr, $r);

		echo "copy : " . $value . " v: " . $v . "\n";
		array_push($copy_paths["tag"], $value);
		array_push($copy_paths["name"], $v);
	}
}

if(!check_role_renamed($role_names, $sql_arr)) {
	echo "has renamed names\n";
	exit();
}

$insert_sql = "insert into role_info (`type`, `name`) values ";
$cur_str = "";
foreach ($sql_arr as $key => $value) {
	echo "\t type " . $value["type"] . " name " . $value["name"] . "\n";

	if($key == 0){
		$cur_str = "(" . $value["type"] . ", \"" . $value["name"] . "\")";
	}else{
		$cur_str = $cur_str . ", " . "(" . $value["type"] . ", \"" . $value["name"] . "\")";
	}
}

$insert_sql = $insert_sql . $cur_str;

$res = mysqli_query($db, $insert_sql);

if(!$res) {
	echo "\tinsert fail!!!\n";
}
echo "\tinsert succ\n";
// $r = copy_files($copy_paths);
// echo "\tcopy file succ\n";
// delete_dirs($copy_paths);\
// echo "\tdelete file succ\n";
// echo "\tall succ\n";



function check_role_renamed($name_all, $name_add) {
	$tag = true;
	foreach ($name_add as $key => $value) {
		if(array_key_exists($value["name"], $name_all)) {
			echo "error!!! renamed file: " . $value["name"] . "\n";
			$tag = false;
		}
	}
	return $tag;
}

function get_mysql_role_names($result) {
	$res = array();
	if($result){
		for ($i=0; $i < $result->num_rows; $i++) { 
			$row = $result->fetch_assoc();
			$res[$row["name"]] = $row["name"];
		}
	}
	return $res;
}

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

function analyse_single_file($path){
	$cur_files = get_path_dir_and_file($path, 1);

	$res = array();
	foreach ($cur_files as $key => $value) {
		echo "\t analyse_single_file " . $value . "\n";
		array_push($res, $value);
	}

	return $res;
}

function get_role_type($name){
	global $role_res;
	foreach ($role_res as $key => $value) {
		if($name == $value["tag"]){
			return (int)$value["type"];
		}
	}

	return -1;
}

function copy_files($source) {
	global $base_dir;
	$base_dest = $base_dir . "/../../role/";
	$tags = $source["tag"];
	$names = $source["name"];

	foreach ($tags as $k => $v) {
		$n = $names[$k];
		copy_dir($base_dir . "/" . $v . "/" . $n, $base_dest . $n);
	}
}

function copy_dir($src,$dst) {
  $dir = opendir($src);
  @mkdir($dst);
  while(false !== ( $file = readdir($dir)) ) {
    if (( $file != '.' ) && ( $file != '..' )) {
      if ( is_dir($src . '/' . $file) ) {
        copy_dir($src . '/' . $file,$dst . '/' . $file);
        continue;
      }
      else {
        copy($src . '/' . $file,$dst . '/' . $file);
      }
    }
  }
  closedir($dir);
}

function delete_dirs($source) {
	global $base_dir;
	$base_dest = $base_dir . "/../../role/";
	$tags = $source["tag"];
	$names = $source["name"];
	foreach ($tags as $k => $v) {
		$n = $names[$k];
		delete_dir($base_dir . "/" . $v . "/" . $n);
	}
}

function delete_dir($path) {
    $op = dir($path);
    while(false != ($item = $op->read())) {
        if($item == '.' || $item == '..') {
            continue;
        }
        if(is_dir($op->path.'/'.$item)) {
            delete_dir($op->path.'/'.$item);
        } else {
            unlink($op->path.'/'.$item);
        }
    
    }
    $op->close();
    rmdir($path);   
}