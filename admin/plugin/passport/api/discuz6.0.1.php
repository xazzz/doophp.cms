<?php

/*
 * $dbconfig["dz6"] = array("localhost", "phpwind", "root", "", "mysql", true, "collate"=>"utf8_unicode_ci", "charset"=>"utf8"); 
 * 把以上代码复制至 admin/config/db.php 里
 */

/*
 * 配置完后把以下代码复制进每个子系统的 controller/auth.php 的 beforeRun() 里即可使用
 * 
 * require_once LUA_ROOT.ADMIN_ROOT.'/plugin/passport/api/discuz6.0.1.php';
 * $uc = new ucenter();
 * $this->user = $uc->user();
 */

class ucenter extends DooController{
    
    // forumdata/cache/cache_settings.php 里查找 authkey
    public $authkey = '93f048c291Z9bFMQ';
    
    // config.inc.php 里查找 tablepre
    public $tablepre = 'cdb_';
    
    // config.inc.php 里查找 cookiepre
    public $cookiepre = 'PGY_';
    
    public function user(){
        $_DCOOKIE = array();
        $prelength = strlen($this->cookiepre);
        foreach($_COOKIE as $key => $val) {
            if(substr($key, 0, $prelength) == $this->cookiepre) {
                $_DCOOKIE[(substr($key, $prelength))] = Lua::clean($val);
            }
        }
        unset($prelength);
        $discuz_auth_key = md5($this->authkey.$_SERVER['HTTP_USER_AGENT']);
        list($discuz_pw, $discuz_secques, $discuz_uid) = isset($_DCOOKIE['auth']) ? Lua::clean(explode("\t", $this->authcode($_DCOOKIE['auth'], 'DECODE', $discuz_auth_key)), 1) : array('', '', 0);
        $discuz_uid = intval($discuz_uid);
        Doo::db()->reconnect('dz6');
        $query = "select m.uid,m.username,m.password,m.gender as sex,mf.avatar as icon from cdb_members m left join cdb_memberfields mf on mf.uid=m.uid where m.uid='$discuz_uid'";
        $query = str_replace('cdb_', $this->tablepre, $query);
        $user = Lua::get_one($query);
        Doo::db()->reconnect('dev');
        if ($user && $discuz_pw == $user['password']){
            unset($user['password']);
            return $user;
        }
        return array();
    }
    
    public function authcode ($string, $operation, $key = '') {
        $key = md5($key);
        $key_length = strlen($key);
        $string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string.$key), 0, 8).$string;
        $string_length = strlen($string);
        $rndkey = $box = array();
        $result = '';
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($key[$i % $key_length]);
            $box[$i] = $i;
        }
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            if(substr($result, 0, 8) == substr(md5(substr($result, 8).$key), 0, 8)) {
                return substr($result, 8);
            } else {
                return '';
            }
        } else {
            return str_replace('=', '', base64_encode($result));
        }

    }
}