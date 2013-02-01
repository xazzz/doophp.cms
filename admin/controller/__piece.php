<?php
Doo::loadController('__auth');

class __piece extends __auth{

    /*
     * 入口
     */
    public function index(){
        $action = Lua::get_post('action');
        $action = $action ? $action : 'home';
        $rs = $this->acl()->process($this->user['perm'], '__piece', $action);
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
     * 碎片管理首页
     */
    private function home(){
        $mods = $this->_models();
        $list = $cate = $this->_tree(0, 0, ' ');
        include Lua::display('piece', $this->dir);
    }
    
    /*
     * 添加碎片栏目
     */
    private function add(){
        $action = 'save';
        $mods = $this->_models();
        $cate = $this->_tree();
        $db = Lua::db_array('lua_piece');
        include Lua::display('piece_add', $this->dir);
    }
    
    /*
     * 编辑碎片栏目
     */
    private function edit(){
        $id = Lua::get('id');
        $action = "save_edit&id=$id";
        $db = Lua::get_one("select * from lua_piece where id='$id' and systemname='".SYSNAME."'");
        $mods = $this->_models();
        $cate = $this->_tree();
        include Lua::display('piece_add', $this->dir);
    }
    
    /*
     * 保存编辑碎片栏目
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
        $id = Lua::get('id');
        $upid = Lua::post('upid');
        if ($id == $upid){
            Lua::ajaxmessage('error', '父级栏目不能是自己');
        }
        $sqlarr = array(
            'upid' => $upid,
            'add_perm' => Lua::post('add_perm'),
            'model_id' => $model_id,
            'name' => $name,
            'vieworder' => Lua::post('vieworder')
        );
        Lua::update('lua_piece', $sqlarr, array('id'=>$id));
        Lua::ajaxmessage('success', '操作成功', './piece.htm');
    }
    
    /*
     * 碎片栏目排序
     */
    private function order(){
        $no_order_new = Lua::post('no_order_new');
        if ($no_order_new){
            foreach ($no_order_new as $id=>$value){
                Lua::update('lua_piece', array('vieworder'=>$value), array('systemname'=>SYSNAME,'id'=>$id));
            }
            Lua::ajaxmessage('success', '排序成功', './piece.htm');
        }else{
            Lua::ajaxmessage('error', '请先添加栏目');
        }
    }
    
    /*
     * 栏目删除
     */
    private function del(){
        $id = Lua::get('id');
        $count = Doo::db()->count("select count(*) from lua_piece where systemname='".SYSNAME."' and upid='$id'");
        if ($count > 0){
            Lua::admin_msg('提示信息', '有下级栏目');
        }
        $db = Lua::get_one("select * from lua_piece where id='$id' and systemname='".SYSNAME."'");
        if ($db){
            $models = $this->_models_tree($db['model_id']);
            if ($models){
                foreach ($models as $row){
                    Doo::db()->query("delete from ".$row['tablename']." where catid='$id'");
                }
            }
        }
        Lua::delete('lua_piece', array('systemname'=>SYSNAME,'id'=>$id));
        Lua::admin_msg('操作提示', '成功删除', './piece.htm');
    }
    
    /*
     * 保存添加碎片栏目
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
        $sqlarr = array(
            'upid' => Lua::post('upid'),
            'systemname' => SYSNAME,
            'add_perm' => Lua::post('add_perm'),
            'model_id' => $model_id,
            'name' => $name,
            'vieworder' => Lua::post('vieworder')
        );
        Lua::insert('lua_piece', $sqlarr);
        Lua::ajaxmessage('success', '操作成功', './piece.htm');
    }
    
    /*
     * 模型列表
     */
    private function _models(){
        $list = Lua::get_more("select * from lua_model where status='1' and mtype='2' order by id asc");
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
        $list = Lua::get_more("select * from lua_piece where systemname='".SYSNAME."' $and order by vieworder asc,id desc");
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
    
    /*
     * 添加任意数据
     */
    private function add_any(){
        $tableid = Lua::get('tableid');
        $db = $this->_table($tableid);
        $fields = $this->_fields($db['tablename']);
        $pri = $this->_pri($fields);
        $rs = Lua::db_array($db['tablename']);
        $action = "save_any_add&tableid=$tableid";
        include Lua::display('piece_any_edit', $this->dir);
    }
    
    /*
     * 编辑任意数据
     */
    private function edit_any(){
        $tableid = Lua::get('tableid');
        $db = $this->_table($tableid);
        $fields = $this->_fields($db['tablename']);
        $pri = $this->_pri($fields);
        $var = Lua::get($pri);
        $rs = Lua::get_one("select * from ".$db['tablename']." where $pri='$var'");
        $action = "save_any_edit&tableid=$tableid&$pri=$var";
        include Lua::display('piece_any_edit', $this->dir);
    }
    
    /*
     * 保存添加任意数据
     */
    private function save_any_add(){
        $tableid = Lua::get('tableid');
        $db = $this->_table($tableid);
        $fields = $this->_fields($db['tablename']);
        $pri = $this->_pri($fields);
        $var = Lua::get($pri);
        $post = Lua::post('post');
        Lua::insert($db['tablename'], $post);
        Lua::ajaxmessage('success', '操作成功', "./piece.htm?action=any&tableid=$tableid");
    }
    
    /*
     * 保存编辑任意数据
     */
    private function save_any_edit(){
        $tableid = Lua::get('tableid');
        $db = $this->_table($tableid);
        $fields = $this->_fields($db['tablename']);
        $pri = $this->_pri($fields);
        $var = Lua::get($pri);
        $post = Lua::post('post');
        Lua::insert($db['tablename'], $post, 1);
        Lua::ajaxmessage('success', '操作成功', "./piece.htm?action=any&tableid=$tableid");
    }
    
    /*
     * 删除任意数据
     */
    private function del_any(){
        $tableid = Lua::get('tableid');
        $db = $this->_table($tableid);
        $fields = $this->_fields($db['tablename']);
        $pri = $this->_pri($fields);
        $var = Lua::get($pri);
        Doo::db()->query("delete from ".$db['tablename']." where $pri='$var'");
        Lua::admin_msg('提示信息', '成功删除', "./piece.htm?action=any&tableid=$tableid");
    }
    
    /*
     * 获取数据表信息
     */
    private function _table($tableid){
        $db = Lua::get_one("select * from lua_model_table where id='$tableid'");
        if (empty($db)){
            Lua::admin_msg('提示信息', '数据表不存在');
        }
        return $db;
    }
    
    /*
     * 获取数据表字段
     */
    private function _fields($tablename){
        return Lua::get_more("SHOW FIELDS FROM ".$tablename);
    }
    
    /*
     * 获取主键ID
     */
    private function _pri($fields){
        $pri = '';
        foreach ($fields as $v){
            if ($v['Key'] == 'PRI'){
                $pri = $v['Field'];
                break;
            }
        }
        if (empty($pri)){
            Lua::admin_msg('提示信息', '此数据表没有主键');
        }
        return $pri;
    }
    
    /*
     * 只要输入数据表ID即可管理数据
     */
    private function any(){
        $tableid = Lua::get('tableid');
        $db = array();
        if ($tableid){
            $db = $this->_table($tableid);
        }        
        if ($db){
            $url = "./piece.htm?action=any&tableid=$tableid";
            $fields = $this->_fields($db['tablename']);
            $count = Doo::db()->count("select count(*) from ".$db['tablename']);
            $tpp = 30;
            $limit = (($this->page - 1) * $tpp).','.$tpp;
            $pri = $this->_pri($fields);
            $list = Lua::get_more("select * from ".$db['tablename']." order by $pri desc limit ".$limit);
            $page = Lua::page($url, $this->page, $count, $tpp);
        }
        include Lua::display('piece_any', $this->dir);
    }
    
}