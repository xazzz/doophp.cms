<?php 

$route['*']['/'] = array('__home', 'index');
$route['*']['/info.htm'] = array('__home', 'info');

$route['*']['/404.htm'] = array('__login', 'E404');
$route['*']['/401.htm'] = array('__login', 'E401');
$route['post']['/login.htm'] = array('__login', 'index');
$route['*']['/logout.htm'] = array('__login', 'logout');

$route['*']['/admin.htm'] = array('__admin', 'index');

$route['*']['/plugin.htm'] = array('__plugin', 'index');

$route['*']['/model.htm'] = array('__model', 'index');

$route['*']['/channel.htm'] = array('__channel', 'index');

$route['*']['/log.htm'] = array('__log', 'index');