<?php

// 通行证 by Lua

class passport extends __auth{
    
    public $plugin_dir;
    public $dir;
    public $img;
    public $user;
    public $cache;

    public function _set($plugin_dir, $dir, $img, $user){
        $this->plugin_dir = $plugin_dir;
        $this->tpl = SYSNAME.'/plugin/passport/tpl/';
        $this->dir = $dir;
        $this->img = $img;
        $this->user = $user;
        $this->cache = $plugin_dir.'passport/cache/';
    }
    
    public function _home(){
        include Lua::display('home', $this->tpl);
    }

}