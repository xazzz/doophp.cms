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

$route["*"]["/'.ADMIN_ROOT.'"] = array("__home", "index");
$route["*"]["/'.ADMIN_ROOT.'/info.htm"] = array("__home", "info");
$route["*"]["/404.htm"] = array("__login", "E404");
$route["*"]["/401.htm"] = array("__login", "E401");
$route["post"]["/'.ADMIN_ROOT.'/login.htm"] = array("__login", "index");
$route["*"]["/'.ADMIN_ROOT.'/logout.htm"] = array("__login", "logout");
$route["*"]["/'.ADMIN_ROOT.'/member.htm"] = array("__member", "index");
$route["*"]["/'.ADMIN_ROOT.'/category.htm"] = array("__category", "index");
$route["*"]["/'.ADMIN_ROOT.'/content.htm"] = array("__content", "index");
$route["*"]["/'.ADMIN_ROOT.'/file.htm"] = array("__file", "index");
$route["*"]["/'.ADMIN_ROOT.'/piece.htm"] = array("__piece", "index");
$route["*"]["/'.ADMIN_ROOT.'/plugin.htm"] = array("__extend", "admin");

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
        $this->img = "/".SYSNAME."/static/";
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
        $db = Lua::get_one("select * from lua_channel where id='$id'");
        Lua::delete('lua_channel', array('id'=>$id));
        Lua::delete('lua_category', array('systemname'=>$db['path']));
        Lua::delete('lua_piece', array('systemname'=>$db['path']));
        $list = Lua::get_more("select * from lua_model where cid='$id'");
        if ($list){
            foreach ($list as $v){
                $table = Lua::get_more("select * from lua_model_table where model_id='".$v['id']."'");
                if ($table){
                    foreach ($table as $t){
                        Doo::db()->query("drop table `".$t['tablename']."`");
                    }
                }
                Lua::delete('lua_model_table', array('model_id'=>$v['id']));
                Lua::delete('lua_model_field', array('model_id'=>$v['id']));
            }
        }
        Lua::delete('lua_model', array('cid'=>$id));
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
    
    /*
     * 导出频道, 即打包
     */
    private function export(){
        set_time_limit(0);
        $id = Lua::get('id');
        $id = intval($id);
        $db = Lua::get_one("select * from lua_channel where id='$id'");
        if (empty($db)){
            Lua::admin_msg('错误提示', '所要导出的频道不存在');
        }
        Doo::loadHelper('DooFile');
        $fileManager = new DooFile(0777);
        Doo::cache('php')->hashing = false;
        $old_dir = LUA_ROOT.ADMIN_ROOT.'/cache/';
        // 第一步 频道数据
        Doo::cache('php')->set('channel', $db);
        // 第二步 模型数据
        $list = Lua::get_more("select * from lua_model where cid='$id'");
        Doo::cache('php')->set('model', $list);
        // 第三步 数据表数据
        if ($list){
            $dumpsql = '';
            Doo::db()->query("SET SQL_QUOTE_SHOW_CREATE=1");
            foreach ($list as $v){
                $table = Lua::get_more("select * from lua_model_table where model_id='".$v['id']."'");
                Doo::cache('php')->set('model.'.$v['id'], $table);
                // 第四步 字段数据
                if ($table){
                    foreach ($table as $k){
                        $field = Lua::get_more("select * from lua_model_field where model_id='".$v['id']."' and table_id='".$k['id']."'");
                        Doo::cache('php')->set('field.'.$v['id'].'.'.$k['id'], $field);
                        // 第五步 创建数据表
                        $r = Doo::db()->fetchRow("SHOW CREATE TABLE `".$k['tablename']."`;");
                        $create = str_replace("\"","\\\"",$r['Create Table']);
                        $dumpsql .= "\r\nDoo::db()->query(\"".$create."\");\r\n";
                        // 第六步 导出数据
                        $data = Lua::get_more("select * from ".$k['tablename']);
                        Doo::cache('php')->set('data.'.$k['id'], $data);
                    }
                }
            }
            $fileManager->create($old_dir.'create.php', '<?php'.$dumpsql.'?>');
        }
        // 第七步 栏目数据
        $list = Lua::get_more("select * from lua_category where systemname='".$db['path']."'");
        Doo::cache('php')->set('cate', $list);
        $list = Lua::get_more("select * from lua_piece where systemname='".$db['path']."'");
        Doo::cache('php')->set('piece', $list);
        // 第八步 打包数据
        $new_dir = LUA_ROOT.$db['path'].'/cache/update/';
        $fileManager->copy($old_dir, $new_dir);
        // 第九步 删除数据
        $fileManager->delete($old_dir, false);
        Lua::admin_msg('提示信息', '导出成功', './channel.htm');
    }
    
    /*
     * 扫描可安装目录
     */
    private function scan(){
        Doo::loadHelper('DooFile');
        $fileManager = new DooFile(0777);
        $list = $fileManager->getList(LUA_ROOT);
        $dir = array();
        $out = array('.git', '@Doo', 'admin', 'nbproject');
        if ($list){
            foreach ($list as $v){
                if ($v['folder'] == 1){
                    if (!in_array($v['name'], $out) && file_exists($v['path'].'/cache/update/')){
                        $dir[] = $v;
                    }
                }
            }
        }
        include Lua::display('channel_scan', $this->dir);
    }
    
    /*
     * 导入数据
     */
    private function import(){
        set_time_limit(0);
        $path = Lua::get('path');
        $path = LUA_ROOT.$path.'/cache/update/';
        if (!file_exists($path)){
            Lua::admin_msg('错误提示', '安装目录不存在');
        }
        // 第一步 频道数据
        $file = $path.'channel.php';
        include $file;
        $data = $data[1];
        unset($data['id']);
        $data['createtime'] = time();
        $data['isdefault'] = 0;
        $channel_id = Lua::insert('lua_channel', $data);
        // 第二步 导入数据结构
        $file = $path.'create.php';
        include $file;
        // 第三步 模型数据
        unset($data);
        $file = $path.'model.php';
        include $file;
        $models = $data[1];
        $new_models = array();
        $new_tables_1 = array();// 栏目
        $new_tables_2 = array();// 碎片
        $new_tables_3 = array();// 插件
        unset($data);
        foreach ($models as $v){
            $model_id = $v['id'];
            unset($v['id']);
            $v['createtime'] = time();
            $v['cid'] = $channel_id;
            $lastid = Lua::insert('lua_model', $v);
            $new_models[$model_id] = $lastid;
            // 第四步 数据表数据
            $file = $path.'model.'.$model_id.'.php';
            include $file;
            $table = $data[1];
            switch ($v['mtype']){
                case 1:
                    $new_tables_1 = array_merge($new_tables_1, $table);
                    break;
                case 2:
                    $new_tables_2 = array_merge($new_tables_2, $table);
                    break;
                case 3:
                    $new_tables_3 = array_merge($new_tables_3, $table);
                    break;
            }            
            $this->__import($model_id, $lastid, $table, $path);
        }
        // 第七步 栏目数据
        $file = $path.'cate.php';
        include $file;
        $cate = $data[1];
        $this->__cate(1, $new_models, $cate, $new_tables_1);
        unset($data);
        $file = $path.'piece.php';
        include $file;
        $cate = $data[1];
        $this->__cate(2, $new_models, $cate, $new_tables_2);
        Doo::loadHelper('DooFile');
        $fileManager = new DooFile(0777);
        $fileManager->delete($path);
        Lua::admin_msg('信息提示', '导入成功', './channel.htm');
    }
    
    // 导入时处理栏目
    private function __cate($mtype, $new_models, $data, $new_tables, $id = 0, $new_id = 0){
        $table = $mtype == 1 ? 'lua_category' : 'lua_piece';
        foreach ($data as $v){
            if ($v['upid'] == $id){
                $old_id = $v['id'];
                unset($v['id']);
                $v['model_id'] = $new_models[$v['model_id']];
                $v['upid'] = $new_id;
                $catid = Lua::insert($table, $v);
                foreach ($new_tables as $t){
                    $tablename = $t['tablename'];
                    Doo::db()->query("update $tablename set catid='$catid' where catid='$old_id'");
                }
                $this->__cate($mtype, $new_models, $data, $new_tables, $old_id, $catid);
            }
        }
    }
    
    // 导入时处理数据
    private function __import($model_id, $lastid, $table, $path, $id = 0, $new_id = 0, $subid = '', $subval = array()){
        foreach ($table as $t){
            if ($t['upid'] == $id){
                $old_id = $t['id'];
                unset($t['id']);
                $t['upid'] = $new_id;
                $t['createtime'] = time();
                $t['model_id'] = $lastid;
                $table_id = Lua::insert('lua_model_table', $t);
                // 第五步 导入数据表结构
                $file = $path.'field.'.$model_id.'.'.$old_id.'.php';
                include $file;
                $data = $data[1];
                foreach ($data as $v){
                    unset($v['id']);
                    $v['updatetime'] = time();
                    $v['model_id'] = $lastid;
                    $v['table_id'] = $table_id;
                    Lua::insert('lua_model_field', $v);
                }
                unset($data);
                // 第六步 导入数据                
                $file = $path.'data.'.$old_id.'.php';
                include $file;
                $data = $data[1];
                $out = array();
                if ($data){
                    foreach ($data as $v){
                        $vid = $v['id'];
                        unset($v['id']);
                        if (isset($v[$subid])){
                            $v[$subid] = $subval[$v[$subid]];
                        }
                        $nid = Lua::insert($t['tablename'], $v);
                        $out[$vid] = $nid;
                    }
                }
                unset($data);
                $this->__import($model_id, $lastid, $table, $path, $old_id, $table_id, $t['subid'], $out);
            }
        }
    }
}