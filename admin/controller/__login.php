<?php

class __login extends DooController{
    
    /*
     * 后台登录
     */
    public function index(){
        $thisip = $this->clientIP();
        Lua::adminfail($thisip, 1);
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
            Lua::adminfail($thisip);
            Lua::admin_msg('信息提示', '用户名或密码错误');
        }
        $auth = Lua::authcode($user['uid']."\t".$user['password'], 'ENCODE');
        $session = Doo::session('Lua');
        // 口令卡验证
        $sets = Doo::cache('php')->get('loginset');
        if ($sets && $sets['cardit'] == 1){
            $cardcode = $session->get('cardcode');
            $cardit = intval(Lua::post('cardit'));
            if (empty($cardit)){
                Lua::admin_msg('信息提示', '请输入口令卡');
            }
            $cardex = explode('@',$cardcode);
            $b1 = $cardex[0]{1};
            $b2 = $cardex[1]{1};
            $secureid = $user['secureid'];
            $sdb = Lua::get_one("select * from lua_secure where id='$secureid' and uid='".$user['uid']."'");
            if (empty($sdb)){
                Lua::admin_msg('信息提示', '请先绑定口令卡后再登录');
            }
            $securekey = unserialize($sdb['securekey']);
            $x = array('A','B','C','D','E','F','G','H','I','J');
            $k1 = array_search($cardex[0]{0}, $x);
            $k2 = array_search($cardex[1]{0}, $x);
            $truekey = $securekey[$b1][$k1].$securekey[$b2][$k2];
            $truekey = intval($truekey);
            if ($truekey != $cardit){
                Lua::adminfail($thisip);
                Lua::admin_msg('信息提示', '输入的口令卡错误','/'.ADMIN_ROOT.'/');
            }
        }
        // end
        $session->auth = $auth;
        Doo::db()->query("update lua_admin set logintime='".time()."',logs=logs+1,loginip='".$this->clientIP()."' where uid='".$user['uid']."'");
        Lua::delete('lua_admin_fails', array('ip'=>$thisip));
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