<?php
Doo::loadController('__auth');

class __channel extends __auth{

    /*
     * 入口
     */
    public function index(){
        $action = Lua::get_post('action');
        $action = $action ? $action : 'home';
        if (method_exists($this, $action)){
            $this->$action();
        }else{
            Lua::e404();
        }
    }
    
    /*
     * 频道列表
     */
    private function home(){
        $list = Lua::get_more("select * from lua_channel order by id asc");
        include Lua::display('channel', $this->dir);
    }
    
    /*
     * 添加频道
     */
    private function add(){
        $db = Lua::db_array('lua_channel');
        $action = "save_add";
        include Lua::display('channel_add', $this->dir);
    }
    
    /*
     * 编辑频道
     */
    private function edit(){
        $id = Lua::get('id');
        $db = Lua::get_one("select * from lua_channel where id='$id'");
        $action = "save_edit&id=$id";
        include Lua::display('channel_add', $this->dir);
    }
    
    /*
     * 保存添加
     */
    private function save_add(){
        $sqlarr = $this->_check(1);
        $sqlarr['createtime'] = time();
        $id = Lua::insert('lua_channel', $sqlarr);
        Lua::write_log($this->user, '增加频道', "id=$id<br />title=".$sqlarr['name'], SYSNAME);
        Lua::ajaxmessage('success', '操作成功', './channel.htm');
    }
    
    /*
     * 保存编辑
     */
    private function save_edit(){
        $id = Lua::get('id');
        $sqlarr = $this->_check(0);
        $where  = array(
            'id' => $id
        );
        Lua::update('lua_channel', $sqlarr, $where);
        Lua::write_log($this->user, '修改频道', "id=$id<br />title=".$sqlarr['name'], SYSNAME);
        Lua::ajaxmessage('success', '操作成功', './channel.htm');
    }
    
    /*
     * 表单验证
     */
    private function _check($mkdir=0){
        $name = Lua::post('name');
        if (empty($name)){
            Lua::ajaxmessage('error', '频道名称');
        }
        $path = Lua::post('path');
        if (empty($path)){
            Lua::ajaxmessage('error', '系统目录');
        }
        $groupname = Lua::post('groupname');
        if (empty($groupname)){
            Lua::ajaxmessage('error', '频道管理组');
        }
        if ($mkdir == 1){
            // 创建子系统
            $__Path = LUA_ROOT.$path.'/';
            mkdir($__Path);
            mkdir($__Path.'cache/',0777);
            mkdir($__Path.'class/',0777);
            mkdir($__Path.'config/');
            $__route = '<?php 

$route["*"]["/admin"] = array("__home", "index");
$route["*"]["/admin/info.htm"] = array("__home", "info");

$route["*"]["/404.htm"] = array("__login", "E404");
$route["*"]["/401.htm"] = array("__login", "E401");
$route["post"]["/admin/login.htm"] = array("__login", "index");
$route["*"]["/admin/logout.htm"] = array("__login", "logout");

$route["*"]["/admin/member.htm"] = array("__member", "index");

$route["*"]["/admin/category.htm"] = array("__category", "index");

$route["*"]["/admin/content.htm"] = array("__content", "index");

$route["*"]["/admin/file.htm"] = array("__file", "index");

$route["*"]["/admin/piece.htm"] = array("__piece", "index");

$route["*"]["/admin/plugin.htm"] = array("__extend", "admin");

$route["*"]["/plugin.htm"] = array("__extend", "front");

// 以上为后台路由地址，不可更改！

$route["*"]["/"] = array("home", "index");
                ';
            file_put_contents($__Path.'config/route.php', $__route);
            mkdir($__Path.'controller/');
            $__auth = '<?php

class auth extends DooController{

    public $dir = "";
    public $img = "";
    public $user = array();
    public $page;
    
    /*
     * 初始化
     */
    public function beforeRun($resource, $action) {
        $this->dir = SYSNAME."/moban/";
        $this->img = "/static/";
        $this->user = array();
        $this->page = Lua::get_post("p") ? intval(Lua::get_post("p")) : 1;
    }
}
                ';
            file_put_contents($__Path.'controller/auth.php', $__auth);
            $__home = '<?php
Doo::loadController("auth");

class home extends auth{

    public function index(){
        include Lua::display("index", $this->dir);
    }
}
                ';
            file_put_contents($__Path.'controller/home.php', $__home);
            mkdir($__Path.'files/',0777);
            mkdir($__Path.'moban/');
            file_put_contents($__Path.'moban/index.htm', $name);
            mkdir($__Path.'moban/cache/',0777);
            mkdir($__Path.'plugin/');
            mkdir($__Path.'static/');
            mkdir($__Path.'static/img/');
            copy(LUA_ROOT.ADMIN_ROOT.'/static/img/water.png', $__Path.'static/img/water.png');
            copy(LUA_ROOT.ADMIN_ROOT.'/icon.png',$__Path.'icon.png');
            $__htaccess = '
RewriteEngine On

# if a directory or a file exists, use it directly
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# otherwise forward it to index.php
RewriteRule .* /'.$path.'/index.php
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]
                ';
            file_put_contents($__Path.'.htaccess', $__htaccess);
            $__index = '<?php 
define("SYSNAME", "'.$path.'");
require_once "../@Doo/Lua.php";
                ';
            file_put_contents($__Path.'index.php', $__index);
        }
        return array(
            'domain' => Lua::post('domain'),
            'groupname' => $groupname,
            'name' => $name,
            'path' => $path
        );
    }
    
    /*
     * 删除整个频道
     */
    private function del(){
        $id = Lua::get('id');
        $db = Lua::get_one("select name from lua_channel where id='$id'");
        Lua::delete('lua_channel', array('id'=>$id));
        Lua::write_log($this->user, '删除频道', "id=$id<br />title=".$db['name'], SYSNAME);
        Lua::admin_msg('提示信息', '操作成功', './channel.htm');
    }
    
    /*
     * 更改频道状态
     */
    private function change(){
        $id = Lua::post('id');
        $db = Lua::get_one("select status from lua_channel where id='$id'");
        $rt = $db['status'] == 1 ? 0 : 1;
        Doo::db()->query("update lua_channel set status='$rt' where id='$id'");
        Lua::println();
    }
    
    /*
     * 设为默认显示
     */
    private function isdefault(){
        $id = Lua::post('id');
        $db = Lua::get_one("select isdefault from lua_channel where id='$id'");
        $rt = $db['isdefault'] == 1 ? 0 : 1;
        Doo::db()->query("update lua_channel set isdefault='0'");
        Doo::db()->query("update lua_channel set isdefault='$rt' where id='$id'");
        $_index_db = Doo::db()->fetchRow("select * from lua_channel where isdefault='1' order by id desc limit 1");
        $__install = '
        if (file_exists("no.install")){
            header("Location:/@install/");
            exit;
        }
            ';
        if ($_index_db){
            $__php = '<?php
                '.$__install.'
                header("Location:/'.$_index_db['path'].'/");
                exit;
                ';
        }else{
            $__php = '<?php
                '.$__install.'
                header("Location:/'.ADMIN_ROOT.'/");
                exit;';
        }
        file_put_contents(LUA_ROOT.'index.php',$__php);
        Lua::println();
    }
}