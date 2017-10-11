<?php
//数据库的变量
define("HOST", "192.168.2.22");
define("USER", "bbm");
define("PASSWORD", "123456");
define("TABLE", "bbm");

define("BACKGROUND_IMAGE_SIZE", 999); //300 背景图片大小的warning
define("SOUND_SIZE", 999); //500 声音大小的warning
define("SOUND_LEN", 999); //60 声音长度的warning

define("DEBUG_WARNING", false);
// define("DEBUG_ERROR", true);

define("DEBUG_TEST", true); // 这边是数据库数据不改的时候的测试


define("ERROR_MYSQL_CONNECT", "error!!! connect mysql error");//数据库连接错误


define("ERROR_BACKGROUND", "error!!! background has error");//背景图含有错误
define("ERROR_BACKGROUND_NAME", "error!!! background name");//背景图名字错误
define("ERROR_BACKGROUND_TYPE", "error!!! background type");//背景图类型错误
define("ERROR_BACKGROUND_RENAMED", "error!!! background renamed");//背景图重名错误

// define("ERROR_MYSQL_CONNECT", "error!!! connect mysql error");



define("WARNING_BACKGROUND_IMAGE_SIZE", "warning!!! background image size");