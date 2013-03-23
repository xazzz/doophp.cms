<?php

/*
 * $dbconfig["pw9"] = array("localhost", "phpwind", "root", "", "mysql", true, "collate"=>"utf8_unicode_ci", "charset"=>"utf8"); 
 * 把以上代码复制至 admin/config/db.php 里
 */

/*
 * 配置完后把以下代码复制进每个子系统的 controller/auth.php 的 beforeRun() 里即可使用
 * 
 * require_once LUA_ROOT.ADMIN_ROOT.'/plugin/passport/api/phpwind9.0.php';
 * $uc = new ucenter();
 * $this->user = $uc->user();
 */

class ucenter extends DooController{
    
    // data/cache/config.php 里查找 cookie.pre
    public $cookiepre = '1fe';
    
    // data/cache/config.php 里查找 hash
    public $hash = 'U6morD5B';
    
    // conf/database.php 里查找 tableprefix
    public $tablepre = 'pw_';
    
    public function user(){
         $userCookie = $this->getCookie('winduser');
         if ($userCookie){
             list($uid, $password) = explode("\t", $this->decrypt($userCookie));
             if ($uid){
                 Doo::db()->reconnect('pw9');
                 $query = "select u.uid,u.username,u.password,i.gender as sex from pw_user u left join pw_user_info i on i.uid=u.uid where u.uid='$uid'";
                 $query = str_replace('pw_', $this->tablepre, $query);
                 $user = Lua::get_one($query);
                 $user['sex'] = $user['sex'] == 1 ? 2: 1;
                 $user['icon'] = '';
                 if ($this->getPwdCode($user['password']) != $password){
                     return array();
                 }else{
                     unset($user['password']);
                     return $user;
                 }
                 Doo::db()->reconnect('dev');
             }
         }
         return array();
    }
    
    public function getPwdCode($pwd) {
        return md5($pwd . $this->hash);
    }
    
    public function decrypt($str, $key = '') {
        $key || $key = $this->hash;
        return $this->_decrypt(base64_decode($str), $key);
    }
    
    public function _decrypt($str, $key){
        if ($str == '') return '';
        $v = $this->str2long($str, false);
        $k = $this->str2long($key, false);
        if (count($k) < 4) {
            for ($i = count($k); $i < 4; $i++) {
                $k[$i] = 0;
            }
        }
        $n = count($v) - 1;
        $z = $v[$n];
        $y = $v[0];
        $delta = 0x9E3779B9;
        $q = floor(6 + 52 / ($n + 1));
        $sum = $this->int32($q * $delta);
        while ($sum != 0) {
            $e = $sum >> 2 & 3;
            for ($p = $n; $p > 0; $p--) {
                $z = $v[$p - 1];
                $mx = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(
                    ($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
                $y = $v[$p] = $this->int32($v[$p] - $mx);
            }
            $z = $v[$n];
            $mx = $this->int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->int32(
                ($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $y = $v[0] = $this->int32($v[0] - $mx);
            $sum = $this->int32($sum - $delta);
        }
        return $this->long2str($v, true);
    }
    
    public function int32($n) {
        while ($n >= 2147483648)
            $n -= 4294967296;
        while ($n <= -2147483649)
            $n += 4294967296;
        return (int) $n;
    }
    
    public function long2str($v, $w) {
        $len = count($v);
        $s = array();
        for ($i = 0; $i < $len; $i++){
            $s[$i] = pack("V", $v[$i]);
        }
        return $w ? substr(join('', $s), 0, $v[$len - 1]) : join('', $s);
    }
    
    public function str2long($s, $w) {
        $v = unpack("V*", $s . str_repeat("\0", (4 - strlen($s) % 4) & 3));
        $v = array_values($v);
        if ($w) $v[count($v)] = strlen($s);
        return $v;
    }
    
    public function getCookie($name, $dencode = false){
        $this->cookiepre && $name = $this->cookiepre . '_' . $name;
        if (isset($_COOKIE[$name])){
            $value = $_COOKIE[$name];
            $value && $dencode && $value = base64_decode($value);
            return $value ? $value : $value;
        }else{
            return '';
        }
    }
}