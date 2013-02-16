<?php
Doo::loadController('__auth');

class __category extends __auth{
    
    /*
     * 入口
     */
    public function index(){
        $action = Lua::get_post('action');
        $action = $action ? $action : 'home';
        $rs = $this->acl()->process($this->user['perm'], '__category', $action);
        if ($rs){
            return $rs;            
        }
        if (method_exists($this, $action)){
            $this->$action();
        }else{
            Lua::e404();
        }
    }
    
    /*
     * 栏目列表
     */
    private function home(){
        $mods = $this->_models();
        $list = $cate = $this->_tree(0, 0, ' ');
        $cans = array();
        if ($this->user['category_can']){
            $cans = unserialize($this->user['category_can']);
        }
        include Lua::display('category', $this->dir);
    }
    
    /*
     * 删除栏目
     */
    private function del(){
        $id = Lua::get('id');
        $count = Doo::db()->count("select count(*) from lua_category where systemname='".SYSNAME."' and upid='$id'");
        if ($count > 0){
            Lua::admin_msg('提示信息', '有下级栏目');
        }
        $db = Lua::get_one("select * from lua_category where id='$id' and systemname='".SYSNAME."'");
        if ($db){
            $models = $this->_models_tree($db['model_id']);
            if ($models){
                foreach ($models as $row){
                    Doo::db()->query("delete from ".$row['tablename']." where catid='$id'");
                }
            }
        }
        Lua::delete('lua_category', array('systemname'=>SYSNAME,'id'=>$id));
        $this->_cache();
        Lua::admin_msg('操作提示', '成功删除', './category.htm');
    }
    
    /*
     * 栏目排序
     */
    private function order(){
        $no_order_new = Lua::post('no_order_new');
        if ($no_order_new){
            foreach ($no_order_new as $id=>$value){
                Lua::update('lua_category', array('vieworder'=>$value), array('systemname'=>SYSNAME,'id'=>$id));
            }
            $this->_cache();
            Lua::ajaxmessage('success', '排序成功', './category.htm');
        }else{
            Lua::ajaxmessage('error', '请先添加栏目');
        }
    }
    
    /*
     * 更新栏目缓存
     */
    private function _cache(){
        Doo::cache('php')->hashing = false;
        Doo::cache('php')->set('category', $this->_tree(0, 0, ''));
    }
    
    /*
     * 添加栏目
     */
    private function add(){
        $action = 'save';
        $mods = $this->_models();
        $cate = $this->_tree();
        $db = Lua::db_array('lua_category');
        include Lua::display('category_add', $this->dir);
    }
    
    /*
     * 编辑栏目
     */
    private function edit(){
        $id = Lua::get('id');
        $action = "save_edit&id=$id";
        $db = Lua::get_one("select * from lua_category where id='$id' and systemname='".SYSNAME."'");
        $mods = $this->_models();
        $cate = $this->_tree();
        include Lua::display('category_add', $this->dir);
    }
    
    /*
     * 保存编辑栏目
     */
    private function save_edit(){
        $name = Lua::post('name');
        if (empty($name)){
            Lua::ajaxmessage('error', '栏目名称');
        }
        $model_id = Lua::post('model_id');
        if (empty($model_id)){
            Lua::ajaxmessage('error', '请选择模型');
        }
        $filename = Lua::post('filename');
        if (empty($filename)){
            Lua::ajaxmessage('error', '静态名称');
        }
        $id = Lua::get('id');
        $upid = Lua::post('upid');
        if ($id == $upid){
            Lua::ajaxmessage('error', '父级栏目不能是自己');
        }
        $sqlarr = array(
            'filename' => $filename,
            'model_id' => $model_id,
            'name' => $name,
            'upid' => $upid,
            'add_perm' => Lua::post('add_perm'),
            'seoinfo' => Lua::post('seoinfo'),
            'seokey' => Lua::post('seokey'),
            'title' => Lua::post('title'),
            'vieworder' => Lua::post('vieworder'),
        );
        Lua::update('lua_category', $sqlarr, array('id'=>$id));
        $this->_cache();
        Lua::ajaxmessage('success', '操作成功', './category.htm');
    }
    
    /*
     * 保存添加栏目
     */
    private function save(){
        $name = Lua::post('name');
        if (empty($name)){
            Lua::ajaxmessage('error', '栏目名称');
        }
        $model_id = Lua::post('model_id');
        if (empty($model_id)){
            Lua::ajaxmessage('error', '请选择模型');
        }
        $filename = Lua::post('filename');
        if (empty($filename)){
            Lua::ajaxmessage('error', '静态名称');
        }
        $sqlarr = array(
            'add_perm' => Lua::post('add_perm'),
            'name' => $name,
            'seoinfo' => Lua::post('seoinfo'),
            'seokey' => Lua::post('seokey'),
            'title' => Lua::post('title'),
            'vieworder' => Lua::post('vieworder'),
            'filename' => $filename,
            'model_id' => $model_id,
            'systemname' => SYSNAME,
            'upid' => Lua::post('upid')
        );
        Lua::insert('lua_category', $sqlarr);
        $this->_cache();
        Lua::ajaxmessage('success', '操作成功', './category.htm');
    }
    
    /*
     * 模型列表
     */
    private function _models(){
        $list = Lua::get_more("select * from lua_model where status='1' and mtype='1' order by id asc");
        $oute = array();
        if ($list){
            foreach ($list as $v){
                $oute[$v['id']] = $v;
            }
        }
        return $oute;
    }
    
    /*
     * 模型树形
     */
    private function _models_tree($id){
        $list = Lua::get_more("select * from lua_model_table where model_id='$id'");
        $back = array();
        if ($list){
            foreach ($list as $v){
                $out[$v['id']] = $v;
            }
            $tree = new Tree($out);
            $back = $tree->get(0,0,' ');
        }
        return $back;
    }
    
    /*
     * 栏目树形
     */
    private function _tree($upid = 0, $force = 0, $addn = ' '){
        $and  = $force == 1 ? " and upid='$upid' " : "";
        $list = Lua::get_more("select * from lua_category where systemname='".SYSNAME."' $and order by vieworder asc,id desc");
        $back = array();
        if ($list){
            foreach ($list as $v){
                $out[$v['id']] = $v;
            }
            $tree = new Tree($out);
            $tree->field = 'name';
            $back = $tree->get($upid, 0, $addn);
        }
        return $back;
    }

}