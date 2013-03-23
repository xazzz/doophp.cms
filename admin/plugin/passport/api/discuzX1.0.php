<?php

/*
 * $dbconfig["X10"] = array("localhost", "phpwind", "root", "", "mysql", true, "collate"=>"utf8_unicode_ci", "charset"=>"utf8"); 
 * 把以上代码复制至 admin/config/db.php 里
 */

/*
 * 配置完后把以下代码复制进每个子系统的 controller/auth.php 的 beforeRun() 里即可使用
 * 
 * require_once LUA_ROOT.ADMIN_ROOT.'/plugin/passport/api/discuzX1.0.php';
 * $uc = new ucenter();
 * $this->user = $uc->user();
 */

class ucenter extends DooController{
    
    // 在 config/config_global.php 里查找 cookiepre
    public $cookiepre = 'UBXD_';
    
    // 在 config/config_global.php 里查找 authkey
    public $authkey = 'c51805CRVUNdcCEi';
    
    // 在 config/config_global.php 里查找 tablepre
    public $tablepre = 'pre_';
    
    public function user(){
        $_DCOOKIE = array();
        $prelength = strlen($this->cookiepre);
        foreach($_COOKIE as $key => $val) {
            if(substr($key, 0, $prelength) == $this->cookiepre) {
                $_DCOOKIE[(substr($key, $prelength))] = Lua::clean($val);
            }
        }
        unset($prelength);
        if (isset($_DCOOKIE['auth'])){
            $authkey = md5($this->authkey.$_SERVER['HTTP_USER_AGENT']);
            $auth = Lua::clean(explode("\t", $this->authcode($_DCOOKIE['auth'], 'DECODE', $authkey)));
            list($discuz_pw, $discuz_uid) = empty($auth) || count($auth) < 2 ? array('', '') : $auth;
            if($discuz_uid) {
                Doo::db()->reconnect('X10');
                $query = "select u.uid,u.username,u.password,p.gender from pre_common_member u left join pre_common_member_profile p on p.uid=u.uid where u.uid='$discuz_uid'";
                $query = str_replace('pre_', $this->tablepre, $query);
                $user = Lua::get_one($query);
                Doo::db()->reconnect('dev');
                if(!empty($user) && $user['password'] == $discuz_pw) {
                    unset($user['password']);
                    return $user;
                }
            }
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