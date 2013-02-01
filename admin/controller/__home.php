<?php
Doo::loadController('__auth');

class __home extends __auth{

    /*
     * 后台首页
     */
    public function index(){
        if ($this->user['perm'] != SUPER_MAN && SYSNAME != $this->user['channel']){
            header("Location:/".$this->user['channel'].'/admin/');
            exit;
        }
        $list = Lua::get_more("select * from lua_channel where status='1'");
    	include Lua::display('frame', $this->dir);
    }
    
    /*
     * 系统信息
     */
    public function info(){
        $ip = $this->clientIP();
        $my = Doo::db()->fetchRow("select VERSION()",null,PDO::FETCH_COLUMN);
        include Lua::display('info', $this->dir);
    }

}