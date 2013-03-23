<?php

/*
 * $dbconfig["dz7"] = array("localhost", "phpwind", "root", "", "mysql", true, "collate"=>"utf8_unicode_ci", "charset"=>"utf8"); 
 * 把以上代码复制至 admin/config/db.php 里
 */

/*
 * 配置完后把以下代码复制进每个子系统的 controller/auth.php 的 beforeRun() 里即可使用
 * 
 * require_once LUA_ROOT.ADMIN_ROOT.'/plugin/passport/api/discuz7.2.php';
 * $uc = new ucenter();
 * $this->user = $uc->user();
 */

class ucenter extends DooController{
    
    // forumdata/cache/cache_settings.php 里查找 authkey
    public $authkey = '772e32g9TQYo7Rxl';
    
    // config.inc.php 里查找 tablepre
    public $tablepre = 'cdb_';
    
    // config.inc.php 里查找 cookiepre
    public $cookiepre = 'bOO_';
    
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
        Doo::db()->reconnect('dz7');
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
    
    public function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        $ckey_length = 4;
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
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
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }
}