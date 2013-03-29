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
    
    /*
     * 微信
     */
    public function weixin(){
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $msgtype = strtolower($postObj->MsgType);
            $event = $postObj->Event;
            $time = time();  
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>"; 
            if(!empty( $keyword )){
                if ($msgtype == 'text'){
                    if ($keyword == '?'){
                        $query = "目前东邦WEB应用有：";
                        $query .= "\n";
                        $list = Lua::get_more("select * from lua_channel where status='1' order by id asc");
                        foreach ($list as $k=>$v){
                            $query .= $v['id']."-".$v['name'];
                            $query .= "\n";
                        }
                        $query .= "查询应用帮助，请输入如“11.?”";
                        echo sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $query);
                        exit;
                    }else{
                        if (strstr($keyword, '.')){
                            $db = explode('.',$keyword);
                            $rs = Lua::get_one("select * from lua_channel where status='1' and id='".intval($db[0])."'");
                            require_once LUA_ROOT.$rs['path'].'/class/weixin.php';
                            $weixin = new weixin();
                            $weixin->set($textTpl, $fromUsername, $toUsername, $time, $msgtype, $event, $db);
                            echo $weixin->send();
                        }else{
                            echo sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', '请输入“?”获取帮助');
                            exit;
                        }
                    }
                }
            }else{
                echo '?';
                exit;
            }
        }else{
            echo '?';
            exit;
        }
    }  

}