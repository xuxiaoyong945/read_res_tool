<?php
require_once("./common_func.php");
echo ">>>>>> start to check sound name\n";
$base_dir = dirname(__FILE__);
$sound_base_dir = dirname($base_dir) . "/sound";

$sound_dir = get_path_dir_and_file($sound_base_dir, 1);

$sound_names = array();
foreach ($sound_dir as $key => $value) {
	if($value != "bg" && $value != "effect" && $value != "word") {
		$path = $sound_base_dir . "/" . $value;
		$tmp_names = get_path_dir_and_file($path, 2);
		foreach ($tmp_names as $k => $v) {
			if(array_key_exists($v, $sound_names)) {
				echo "err: " . $v . " " . $value . " : " . $sound_names[$v] . "\n";
			}else{
				$sound_names[$v] = $value;
			}
		}
	}
}


echo ">>>>>> end to check sound name\n";