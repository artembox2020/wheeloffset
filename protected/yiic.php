<?php
function d($d) {
	print_r($d);
	die();
}

defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
defined('YII_DEBUG_SHOW_PROFILER') or define('YII_DEBUG_SHOW_PROFILER',true);
defined('YII_DEBUG_PROFILING') or define('YII_DEBUG_PROFILING',true);
defined('YII_DEBUG_DISPLAY_TIME') or define('YII_DEBUG_DISPLAY_TIME',true);

include(dirname(__FILE__).'/../simple_html_dom.php');
$yiic=dirname(__FILE__).'/../yii/framework/yiic.php';
$config=dirname(__FILE__).'/config/console.php';

require_once($yiic);
