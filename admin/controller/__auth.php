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
            $sets = Doo::cache('php')->get('loginset');
            if ($sets && $sets['cardit'] == 1){
                $xxxx = array('A','B','C','D','E','F','G','H','I','J');
                $nums = range(1,9);
                shuffle($xxxx);
                shuffle($nums);
                $aaaa = $xxxx[0].$nums[0];
                $bbbb = $xxxx[1].$nums[1];
                $session->cardcode = $aaaa.'@'.$bbbb;
            }
            include Lua::display('login', $this->dir);
            exit;
        }
        $auth = empty($auth) ? array(0,'') : Lua::clean(explode("\t",Lua::authcode($auth, 'DECODE')), 1);
        $user = Lua::get_one("select * from lua_admin where uid='".intval($auth[0])."' and password='".$auth[1]."' and gid='1'");
        if (empty($user) || ($user && $this->clientIP() != $user['loginip'])){
            $session->auth = '';
            Lua::admin_msg('操作提示', '请先登录','/'.ADMIN_ROOT);
        }
        
        $rs = $this->acl()->process($user['perm'], $resource, $action);
        if ($rs){
            return $rs;
        }
        
        $this->user = $user;
        $this->page = Lua::get_post('p') ? intval(Lua::get_post('p')) : 1;
        
        // 图片识别码, 借鉴自 supesite
        define('FILE_HASH',substr(md5($user['uid'].'/'.time().Lua::random(6)), 8, 16));
    }

}