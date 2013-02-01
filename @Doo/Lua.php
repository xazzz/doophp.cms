<?php

header("Content-type: text/html; charset=utf-8");

define('LUA_VER','Alpine');
define('LUA_ROOT', substr(dirname(__FILE__), 0, -4));
define('ADMIN_ROOT', 'admin');
define('PROJECT_ROOT', substr($_SERVER['SCRIPT_FILENAME'], 0, -9));
define('SUPER_MAN', 'admin');

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Kuala_Lumpur');

/**
 * 系统初始参数配置
 */
$config['START_TIME'] = microtime(true); 
$config['SITE_PATH'] = PROJECT_ROOT;
$config['BASE_PATH'] = LUA_ROOT.'@Doo/'; 
$config['PROTECTED_FOLDER'] = '';
$config['APP_MODE'] = 'dev'; 
$config['SUBFOLDER'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\','/',$config['SITE_PATH']));
if(strpos($config['SUBFOLDER'], '/')!==0){
    $config['SUBFOLDER'] = '/'.$config['SUBFOLDER'];
}
$config['APP_URL'] = 'http://'.$_SERVER['HTTP_HOST'].$config['SUBFOLDER'];
$config['DEBUG_ENABLED'] = FALSE;
$config['ERROR_404_ROUTE'] = '/404.htm';
$config['AUTOROUTE'] = true;

require_once $config['BASE_PATH'].'Doo.php';
require_once $config['BASE_PATH'].'app/DooConfig.php';

include PROJECT_ROOT.'config/route.php';
if (file_exists(PROJECT_ROOT.'config/db.php')){
    require_once PROJECT_ROOT.'config/db.php';
}else{
    require_once LUA_ROOT.ADMIN_ROOT.'/config/db.php';
}

if (file_exists(PROJECT_ROOT.'config/acl.php')){
    require_once PROJECT_ROOT.'config/acl.php';
}else{
    require_once LUA_ROOT.ADMIN_ROOT.'/config/acl.php';
}
Doo::acl()->rules = $acl;
Doo::acl()->defaultFailedRoute = '/'.SYSNAME.'/401.htm';

Doo::conf()->set($config);

Doo::db()->setDb($dbconfig, $config['APP_MODE']);
Doo::db()->sql_tracking = true;

Doo::app()->route = $route;

Doo::loadClass('Lua',false,ADMIN_ROOT);

Doo::app()->run();