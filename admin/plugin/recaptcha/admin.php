<?php

// recaptcha 验证码 by Lua

class recaptcha extends __auth{
    
    public $plugin_dir;
    public $dir;
    public $img;
    public $user;
    public $cache;

    public function _set($plugin_dir, $dir, $img, $user){
        $this->plugin_dir = $plugin_dir;
        $this->tpl = SYSNAME.'/plugin/recaptcha/tpl/';
        $this->dir = $dir;
        $this->img = $img;
        $this->user = $user;
        $this->cache = $plugin_dir.'recaptcha/cache/';
    }
    
    public function _home(){
        $key = array('a' => '', 'b' => '');
        $code = '';
        $configFile = $this->cache.'config.php';
        if (file_exists($configFile)){
            $key = file_get_contents($configFile);
            $key = unserialize($key);
            require_once $this->plugin_dir.'recaptcha/recaptchalib.php';
            $publickey = $key['a'];
            $privatekey = $key['b'];
            $error = null;
            $code = recaptcha_get_html($publickey, $error);
        }
        include Lua::display('home', $this->tpl);
    }
    
    public function _do(){
        $key = Lua::post('key');
        if (empty($key['a'])){
            Lua::ajaxmessage('error', '请输入公共密钥');
        }
        if (empty($key['b'])){
            Lua::ajaxmessage('error', '请输入私有密钥');
        }
        $key = serialize($key);
        $configFile = $this->cache.'config.php';
        Doo::loadHelper('DooFile');
        $fileManager = new DooFile(0777);
        $fileManager->create($configFile, $key);
        Lua::ajaxmessage('success', '保存成功','./plugin.htm?action=recaptcha');
    }
    
}