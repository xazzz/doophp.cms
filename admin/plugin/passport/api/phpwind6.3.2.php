<?php

/*
 * $dbconfig["pw6"] = array("localhost", "phpwind", "root", "", "mysql", true, "collate"=>"utf8_unicode_ci", "charset"=>"utf8"); 
 * 把以上代码复制至 admin/config/db.php 里
 */

/*
 * 配置完后把以下代码复制进每个子系统的 controller/auth.php 的 beforeRun() 里即可使用
 * 
 * require_once LUA_ROOT.ADMIN_ROOT.'/plugin/passport/api/phpwind6.3.2.php';
 * $uc = new ucenter();
 * $this->user = $uc->user();
 */

class ucenter extends DooController{
    
    // data/bbscache/config.php 里查找 db_sitehash
    public $db_sitehash = '10XAtRXVMEAVUGUQ4DDFdTVQcPBwUAV1BTUAJSAFtTUlA';
    
    // data/bbscache/config.php 里查找 db_hash
    public $db_hash = '85ep314~We';
    
    // data/sql_config.php 里查找 $PW
    public $tablepre = 'pw_';
    
    public function user(){
        $winduser = $this->GetCookie('winduser');
        if ($winduser){
            list($winduid,$windpwd,)= explode("\t",$this->StrCode($winduser,'DECODE'));
        }
        if(isset($winduid) && is_numeric($winduid) && strlen($windpwd)>=16){
            Doo::db()->reconnect('pw6');
            $query = "SELECT m.uid,m.username,m.password,m.icon,m.gender as sex,md.onlineip FROM pw_members m LEFT JOIN pw_memberdata md ON m.uid=md.uid WHERE m.uid='$winduid'";
            $query = str_replace('pw_', $this->tablepre, $query);
            $user = Lua::get_one($query);
            $onlineip = $this->clientIP();
            if(strpos($user['onlineip'],$onlineip)===false){
                $iparray=explode(".",$onlineip);
                if(strpos($user['onlineip'],$iparray[0].'.'.$iparray[1])===false){
                    return array();
                }
            }else{
                if (empty($user)){
                    return array();
                }else{
                    if ($this->PwdCode($user['password']) != $windpwd){
                        unset($user);
                        return array();
                    }else{
                        unset($user['password']);
                        return $user;
                    }
                }
            }
            Doo::db()->reconnect('dev');
        }        
    }
    
    
    public function GetCookie($Var){
        $Var = $this->CookiePre().'_'.$Var;
        return isset($_COOKIE[$Var]) ? $_COOKIE[$Var] : '';
    } 
    
    public function CookiePre(){
        return substr(md5($this->db_sitehash),0,5);
    }
    
    public function StrCode($string, $action='ENCODE'){
        $key	= substr(md5($_SERVER["HTTP_USER_AGENT"].$this->db_hash),8,18);
        $string	= $action == 'ENCODE' ? $string : base64_decode($string);
        $len	= strlen($key);
        $code	= '';
        for($i=0; $i<strlen($string); $i++){
            $k		= $i % $len;
            $code  .= $string[$i] ^ $key[$k];
        }
        $code = $action == 'DECODE' ? $code : base64_encode($code);
        return $code;
    }
    
    public function PwdCode($pwd){
        return md5($_SERVER["HTTP_USER_AGENT"].$pwd.$this->db_hash);
    }
}