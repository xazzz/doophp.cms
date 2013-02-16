<?php

// 执行SQL语句 by Lua

class sql extends __auth{
    
    public $plugin_dir;
    public $dir;
    public $img;
    public $user;
    public $cache;

    public function _set($plugin_dir, $dir, $img, $user){
        $this->plugin_dir = $plugin_dir;
        $this->tpl = SYSNAME.'/plugin/sql/tpl/';
        $this->dir = $dir;
        $this->img = $img;
        $this->user = $user;
        $this->cache = $plugin_dir.'sql/cache/';
    }
    
    public function _home(){
        include Lua::display('home', $this->tpl);
    }
    
    public function _do(){
        $sql = Lua::post('content');
        Doo::db()->query($sql);
        Lua::ajaxmessage('success', '成功执行', './plugin.htm?action=sql');   
    }
    
}