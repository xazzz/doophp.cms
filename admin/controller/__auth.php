<?php

class __auth extends DooController{
    
    public $dir = '';
    public $img = '';
    public $user = array();
    public $page;

    /*
     * 初始化
     */
    public function beforeRun($resource, $action) {
        
        $this->dir = ADMIN_ROOT.'/moban/';
        $this->img = '/'.ADMIN_ROOT.'/static/';
        
        $session = Doo::session('Lua');
        $auth = $session->get('auth');
        if (empty($auth)){
            include Lua::display('login', $this->dir);
            exit;
        }
        $auth = empty($auth) ? array(0,'') : Lua::clean(explode("\t",Lua::authcode($auth, 'DECODE')), 1);
        $user = Lua::get_one("select * from lua_admin where uid='".intval($auth[0])."' and password='".$auth[1]."' and gid='1'");
        if (empty($user)){
            $session->auth = '';
            Lua::admin_msg('操作提示', '请先登录','/'.ADMIN_ROOT);
        }
        
        $rs = $this->acl()->process($user['perm'], $resource, $action);
        if ($rs){
            return $rs;
        }
        
        $this->user = $user;
        $this->page = Lua::get_post('p') ? intval(Lua::get_post('p')) : 1;
    }

}