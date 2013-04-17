<?php 
/*
 * 支持盛大云引擎
 */
/*
$services = getenv("VCAP_SERVICES");
$services_json = json_decode($services,true);
$mysql_config = $services_json["mysql-5.1"][0]["credentials"];

$dbconfig["dev"] = array($mysql_config["hostname"], $mysql_config["name"], $mysql_config["user"], $mysql_config["password"], "mysql", false, "collate"=>"utf8_unicode_ci", "charset"=>"utf8"); 
*/
$dbconfig["dev"] = array("localhost", "d20130409", "root", "", "mysql", false, "collate"=>"utf8_unicode_ci", "charset"=>"utf8"); 
