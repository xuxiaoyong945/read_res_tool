<?php
require_once("config.php");
require_once("getid3/getid3.php");

// 读取目录下的文件或者文件夹
// 0：所有文件
// 1：文件夹
// 2：文件
function get_path_dir_and_file($path, $tag){
	$r = array();
	$handler = opendir($path);
	while (($filesname = readdir($handler)) !== false) {
		if($filesname != "." && $filesname != ".."){
			if($tag == 0){
				array_push($r, $filesname);
			}elseif($tag == 1){
				if(is_dir($path . "/" . $filesname)){
					array_push($r, $filesname);
				}
			}elseif($tag == 2){
				if(is_file($path . "/" . $filesname)){
					array_push($r, $filesname);
				}
			}
		}
	}
	closedir($handler);

	return $r;
}

function get_sound_info($path){
	$getID3 = new getID3();
	$file_info = $getID3->analyze($path);
	$size = number_format($file_info["filesize"], 2);
	$time = number_format($file_info["playtime_seconds"], 2);
	$bitrate = $file_info["audio"]["bitrate"];
	// $size = size2mb($file_info["filesize"], 2);
	// $time = $ThisFileInfo['playtime_seconds'];
	return array("time"=>$time, "size"=>$size, "bitrate"=>$bitrate);
}


// 这边有兼容性的问题 必须要有com组件以及组件的wmplayer
// function get_sound_info($path){
// 	$player = new COM("WMPlayer.OCX");
// 	$media = $player->newMedia($path);
// 	$time = $media->duration; //double 2.33
// 	// $time = $media->durationString; //string "02:33"
// 	$size = $media->getItemInfo("FileSize"); //"Title" == $media->name,  "FileType"
// 	// $size = size2mb($size, 2);
// 	$time = number_format($time, 2);
// 	$time = (float)($time);
// 	$size = number_format($size, 2);
// 	$size = (float)($size);
// 	return array("time"=>$time, "size"=>$size);
// }

function get_image_info($path){
	$data = getimagesize($path);
	$bit_size = filesize($path);
	$width = $data[0];
	$height = $data[1];
	$size = size2mb($bit_size, 2);
	return array('width'=>$width, 'height'=>$height, 'size'=>$size);
}

function size2mb($size,$digits=2){ //digits，要保留几位小数
    $unit = array('','K','M','G','T','P');//单位数组，是必须1024进制依次的哦。
    $base = 1024;//对数的基数
    $i = floor(log($size,$base));//字节数对1024取对数，值向下取整。
    return round($size/pow($base,$i),$digits);
}

function save_json_file($path, $data){
	if(!$path || !$data){
		return false;
	}

	$fp = fopen($path, "w");
	fwrite($fp, $data);
	fclose($fp);
}

function get_relative_path($full_path, $origin_path){
	$len = strlen($origin_path);
	$relative_path = substr($full_path, $len + 1);
	$arr = str_replace("\\", "/", $relative_path);
	return $arr;
}

function get_armature_info($path){
	$res = array();
	$doc = new DOMDocument();
	$doc->load($path);
	$armatures = $doc->getElementsByTagName("armatures");
	$armature = $armatures->item(0)->getElementsByTagName("armature");

	foreach ($armature as $value) {
		array_push($res, $value->getAttribute("name"));
	}
	return $res;
}

function get_frames_len($path){
	$doc = new DOMDocument();
	$doc->load($path);
	$dict = $doc->getElementsByTagName("dict");
	$length = $dict->length;
	return $length - 3;
}

// function get_next_frame($i){
// 	if($i < 10){
// 		return "000" . $i;
// 	}elseif($i < 100){
// 		return "00" . $i;
// 	}elseif($i < 1000){
// 		return "0" . $i;
// 	}elseif($i < 10000) {
// 		return "" . $i;
// 	}else{
// 		return "";
// 	}
// }

// 从数据库读取到数据进行解析
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

function check_name($name){
	 if(preg_match("/^[a-zA-Z0-9_]+$/", $name)){
	 	// echo "\t right name " . $name . "\n";
	 	return true;
	 }else{
	 	// echo "\terror name " . $name . "\n";
	 	return false;
	 }
}

//检查格式
function check_name_vaild($name) {
	$reg = "/^[a-z]+[a-z0-9_]*$/"; // 字母开头 全小写和下划线数字
	// $reg = "/^[a-z0-9]+[a-z0-9_]*$/"; //字母或者数字开头 全小写和下划线数字

	if(preg_match($reg, $name)){
		return true;
	}
	return false;
}

//保存json文件
function save_json_data($path, $data) {
	if(!$path || !$data){
		return false;
	}

	$save_json =  urldecode(json_encode($data));
	$save_data = stripslashes($save_json);

	$fp = fopen($path, "w");
	fwrite($fp, $save_data);
	fclose($fp);
}