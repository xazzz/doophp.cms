<?php
Doo::loadController('__auth');

class __tpl extends __auth{
    
    private $tpl_path;
    
    /*
     * 入口
     */
    public function index(){
        $action = Lua::get_post('action');
        $action = $action ? $action : 'home';
        $rs = $this->acl()->process($this->user['perm'], '__tpl', $action);
        if ($rs){
            return $rs;            
        }
        if (method_exists($this, $action)){
            $this->tpl_path = PROJECT_ROOT.'moban/';
            $this->$action();
        }else{
            Lua::e404();
        }
    }
    
    /*
     * 列表模板
     */
    private function home(){
        $kindof = 1;
        $list = $this->_list($kindof);
        include Lua::display('tpl', $this->dir);
    }
    
    /*
     * 内容模板
     */
    private function content(){
        $kindof = 2;
        $list = $this->_list($kindof);
        include Lua::display('tpl', $this->dir);
    }
    
    /*
     * 编辑模板
     */
    private function edit(){
        $id = intval(Lua::get('id'));
        $db = Lua::get_one("select * from lua_tpls where id='$id'");
        $action = "save_edit&id=$id";
        $html = '';
        if (file_exists($this->tpl_path.$db['tplfile'])){
            $html = file_get_contents($this->tpl_path.$db['tplfile']);
        }
        include Lua::display('tpl_add', $this->dir);
    }
    
    /*
     * 模板帮助
     */
    private function help(){
        $tag = '&lt;!--{template 文件名.@this->dir}--&gt;';
        include Lua::display('tpl_help', $this->dir);
    }
    
    /*
     * 保存编辑
     */
    private function save_edit(){
        $name = Lua::post('name');
        $kindof = Lua::post('kindof');
        $tplfile = Lua::post('tplfile');
        if (empty($name)){
            Lua::ajaxmessage('error', '模板名称');
        }
        if (empty($kindof)){
            Lua::ajaxmessage('error', '模板类型');
        }
        if (empty($tplfile)){
            Lua::ajaxmessage('error', '模板文件名');
        }
        $id = intval(Lua::get('id'));
        $db = Lua::get_one("select * from lua_tpls where id='$id'");
        if ($db['tplfile'] != $tplfile){
            if (file_exists($this->tpl_path.$tplfile)){
                Lua::ajaxmessage('error', $tplfile.' 已存在');
            }
        }
        $query = array(
            'name' => $name,
            'kindof' => $kindof,
            'lasttime' => TIMESTAMP,
            'uid' => $this->user['uid'],
            'username' => $this->user['username'],
            'tplfile' => $tplfile
        );
        Lua::update('lua_tpls', $query, array('id'=>$id));
        $html = $_POST['tplhtml'];
        file_put_contents($this->tpl_path.$tplfile, $html);
        Lua::ajaxmessage('success', '操作成功', $this->_url($kindof));    
    }
    
    /*
     * 删除模板
     */
    private function del(){
        $id = intval(Lua::get('id'));
        $rs = Lua::get_one("select id from lua_category where tpl_id='$id'");
        if ($rs){
            Lua::admin_msg('信息提示', '此模板已关联至某栏目下，不可删除');
        }
        $db = Lua::get_one("select * from lua_tpls where id='$id'");
        $file = $db['tplfile'];
        list($tpl,) = explode('.',$file);
        if (file_exists($this->tpl_path.$file)){
            unlink($this->tpl_path.$file);
        }
        if (file_exists($this->tpl_path.'cache/'.$tpl.'.tpl.php')){
            unlink($this->tpl_path.'cache/'.$tpl.'.tpl.php');
        }
        Lua::delete('lua_tpls', array('id'=>$id));
        Lua::admin_msg('信息提示', '操作成功', $this->_list($db['kindof']));
    }
    
    /*
     * 公共模板
     */
    private function common(){
        $kindof = 3;
        $list = $this->_list($kindof);
        include Lua::display('tpl', $this->dir);
    }
    
    /*
     * 读取模板列表
     */
    private function _list($kindof){
        return Lua::get_more("select * from lua_tpls where kindof='$kindof' and systemname='".SYSNAME."'");
    }
    
    /*
     * 添加模板
     */
    private function add(){
        $db = Lua::db_array('lua_tpls');
        $action = 'save';
        include Lua::display('tpl_add', $this->dir);
    }
    
    /*
     * 添加保存
     */
    private function save(){
        $name = Lua::post('name');
        $kindof = Lua::post('kindof');
        $tplfile = Lua::post('tplfile');
        if (empty($name)){
            Lua::ajaxmessage('error', '模板名称');
        }
        if (empty($kindof)){
            Lua::ajaxmessage('error', '模板类型');
        }
        if (empty($tplfile)){
            Lua::ajaxmessage('error', '模板文件名');
        }
        if (file_exists($this->tpl_path.$tplfile)){
            Lua::ajaxmessage('error', $tplfile.' 已存在');
        }
        $query = array(
            'systemname' => SYSNAME,
            'name' => $name,
            'kindof' => $kindof,
            'dateline' => TIMESTAMP,
            'lasttime' => TIMESTAMP,
            'uid' => $this->user['uid'],
            'username' => $this->user['username'],
            'tplfile' => $tplfile
        );
        Lua::insert('lua_tpls', $query);
        Lua::ajaxmessage('success', '操作成功', $this->_url($kindof));
    }
    
    /*
     * 跳转地址
     */
    private function _url($kindof){
        switch ($kindof){
            case 1:
                $url = './tpl.htm';
                break;
            case 2:
                $url = './tpl.htm?action=content';
                break;
            case 3:
                $url = './tpl.htm?action=common';
                break;
        }
        return $url;
    }
}