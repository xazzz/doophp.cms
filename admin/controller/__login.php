<?php

class __login extends DooController{
    
    /*
     * 后台登录
     */
    public function index(){
        $username = Lua::post('username');
        $password = Lua::post('password');
        if (empty($username)){
            Lua::admin_msg('信息提示', '请输入用户名');
        }
        if (empty($password)){
            Lua::admin_msg('信息提示', '请输入密码');
        }
        $user = Lua::get_one("select * from lua_admin where username='$username' and password='".md5($password)."' and gid='1'");
        if (empty($user)){
            Lua::admin_msg('信息提示', '用户名或密码错误');
        }
        $auth = Lua::authcode($user['uid']."\t".$user['password'], 'ENCODE');
        $session = Doo::session('Lua');
        $session->auth = $auth;
        Doo::db()->query("update lua_admin set logintime='".time()."',logs=logs+1,loginip='".$this->clientIP()."' where uid='".$user['uid']."'");
        Lua::write_log($user, '登录后台', '---', $user['channel']);
        Lua::admin_msg('操作提示', '登录成功','/'.ADMIN_ROOT);
    }
    
    /*
     * 退出后台
     */
    public function logout(){
        $session = Doo::session('Lua');
        $auth = $session->get('auth');
        $auth = empty($auth) ? array(0,'') : Lua::clean(explode("\t",Lua::authcode($auth, 'DECODE')), 1);
        $user = Lua::get_one("select * from lua_admin where uid='".intval($auth[0])."' and password='".$auth[1]."' and gid='1'");
        if ($user){
            Lua::write_log($user, '退出系统', '---', $user['channel']);
        }
        $session->auth = '';
        Lua::admin_msg('操作提示', '成功退出系统','/'.ADMIN_ROOT);
    }
    
    /*
     * 404错误提示页面
     */
    public function E404(){
    	Lua::e404();
    }    
    
    /*
     * 401权限提示页面
     */
    public function E401(){
        Lua::admin_msg('权限提示', '你无权操作');
    }

}