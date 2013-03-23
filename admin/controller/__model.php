<?php
Doo::loadController('__auth');

class __model extends __auth{
    
    public $mtype = array('1'=>'栏目','2'=>'碎片','3'=>'插件');

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
     * 可用频道列表
     */
    private function _channel(){
        return Lua::get_more("select * from lua_channel where status='1'");
    }
    
    /*
     * 已安装模型列表
     */
    private function home(){
        $mtype = Lua::get('mtype');
        $where = "";
        $url   = "./model.htm";
        if ($mtype){
            $where .= "and mtype='$mtype'";
            $url   = "./model.htm?mtype=$mtype";
        }
        $cid = Lua::get('cid');
        if ($cid){
            $where .= " and cid='$cid' ";
            $url = $mtype ? "./model.htm?mtype=$mtype&cid=$cid" : "./model.htm?cid=$cid" ;
        }
        $count = Doo::db()->count("select count(*) from lua_model where id>0 $where");
        $tpp = 20;
        $limit = (($this->page - 1) * $tpp).','.$tpp;
        $list = Lua::get_more("select * from lua_model where id>0 $where order by id desc limit ".$limit);
        $page = Lua::page($url, $this->page, $count, $tpp);
        $channels = $this->_channel();
        include Lua::display('model', $this->dir);
    }
    
    /*
     * 某个模型下数据表列表
     */
    private function table(){
        Doo::loadHelper('DooFile');
        $id = Lua::get('id');
        $db = $this->_get($id);
        $list = $this->_tree($id);
        include Lua::display('table', $this->dir);
    }
    
    /*
     * 某个模型下添加数据表
     */
    private function table_add(){
        $mid = Lua::get('mid');
        $mdb = $this->_get($mid);
        $act = "save_table_add&mid=$mid";
        $db  = Lua::db_array('lua_model_table');
        $list = $this->_tree($mid);
        include Lua::display('table_add', $this->dir);
    }
    
    /*
     * 某个模型下编辑数据表
     */
    private function table_edit(){
        $model_id = Lua::get('model_id');
        $mdb = $this->_get($model_id);
        $id  = Lua::get('id');
        $act = "save_table_edit&mid=$model_id&id=$id";
        $db = $this->_table($id);
        $list = $this->_tree($model_id);
        include Lua::display('table_add', $this->dir);
    }
    
    /*
     * 获取某个模型下数据表信息
     */
    private function _table($id, $ajax = 0){
        $db = Lua::get_one("select * from lua_model_table where id='$id'");
        if (empty($db)){
            if ($ajax == 1){
                Lua::ajaxmessage('error', '你操作的数据表不存在');
            }else{
                Lua::admin_msg('提示信息', '你操作的数据表不存在');
            }
        }
        return $db;
    }
    
    /*
     * 计算数据表的长度与记录数
     */
    private function _len($table){
        $config = Doo::db()->getDefaultDbConfig();
        $db = Doo::db()->fetchRow("show table status from ".$config[1]." like '$table'");
        $db['free'] = DooFile::formatBytes($db['Data_free']);
        $db['length'] = DooFile::formatBytes($db['Index_length']+$db['Data_length']);
        $db['nums'] = $db['Rows'];
        return $db;
    }
    
    /*
     * 对数据表进行操作
     */
    private function table_do(){
        $checkbox = Lua::post('checkbox');
        if ($checkbox){
            $model_id = Lua::get('model_id');
            $db = $this->_get($model_id, 1);
            $action = array('analyze'=>'分析','optimize'=>'优化','check'=>'检查','repair'=>'修复','truncate'=>'清空');
            $opz = Lua::get('opz');
            if (!array_key_exists($opz, $action)){
                Lua::ajaxmessage('error', '非法操作');
            }
            if ($opz == 'truncate'){
                Doo::db()->query("SET foreign_key_checks = 0;");
                foreach ($checkbox as $table){
                    Doo::db()->query("truncate table $table");
                }
            }else{
                $ime = implode(',',$checkbox);
                Doo::db()->query("$opz table $ime");
            }
            Lua::write_log($this->user, $action[$opz].'数据表', "model_id=$model_id<br />title=".$db['modelname'], SYSNAME);
            Lua::ajaxmessage('success', $action[$opz].'成功', "./model.htm?action=table&id=$model_id");
        }else{
            Lua::ajaxmessage('error', '请选择你要操作的数据表');
        }
    }
    
    /*
     * 删除数据表
     */
    private function table_del(){
        $mid = Lua::get('model_id');
        $mdb = $this->_get($mid);
        $id  = Lua::get('id');
        $db  = $this->_table($id);
        Doo::db()->query("drop table ".$db['tablename']);
        Doo::db()->query("delete from lua_model_table where id='$id' and model_id='$mid'");
        Doo::db()->query("delete from lua_model_field where model_id='$mid' and table_id='$id'");
        Doo::db()->query("update lua_model set tablenum=tablenum-1 where id='$mid'");
        Lua::write_log($this->user, '删除数据表', "tableid=$id<br />model_id=$mid<br />title=".$db['tablename'], SYSNAME);
        Lua::admin_msg('提示信息', '成功删除', "./model.htm?action=table&id=$mid");
    }
    
    /*
     * 某个数据表下字段列表
     */
    private function field(){
        $mid = Lua::get('model_id');
        $mdb = $this->_get($mid);
        $id  = Lua::get('id');
        $db  = $this->_table($id);
        $list = Lua::get_more("select * from lua_model_field where model_id='$mid' and table_id='$id' order by vieworder asc,id desc");
        $types = Lua::form();
        include Lua::display('field', $this->dir);
    }
    
    /*
     * 删除字段
     */
    private function field_del(){
        $model_id = Lua::get('model_id');
        $table_id = Lua::get('table_id');
        $rs = $this->_table($table_id);
        $db = Lua::get_one("select fieldname from lua_model_field where id='".Lua::get('id')."' and model_id='$model_id' and table_id='$table_id'");
        $fieldname = $db['fieldname'];
        if ($fieldname){
            Doo::db()->query("ALTER TABLE ".$rs['tablename']." DROP ".$fieldname);
            Doo::db()->query("delete from lua_model_field where id='".Lua::get('id')."' and model_id='$model_id' and table_id='$table_id'");
        }
        Lua::write_log($this->user, '删除模型字段', "tableid=$table_id<br />model_id=$model_id<br />table=".$rs['tablename']."<br />fieldname=$fieldname", SYSNAME);
        Lua::admin_msg('提示信息', '成功删除', "./model.htm?action=field&model_id=$model_id&id=$table_id");
    }
    
    /*
     * 字段选项
     */
    private function field_option(){
        $model_id = Lua::get('model_id');
        $table_id = Lua::get('table_id');
        $mdb = $this->_get($model_id);
        $tdb = $this->_table($table_id);
        $id = Lua::get('id');
        $db = Lua::get_one("select name,fieldoption from lua_model_field where id='$id' and model_id='$model_id' and table_id='$table_id'");
        $rs = $db['fieldoption'];
        $options = array();
        if ($rs){
            $options = unserialize($rs);
            ksort($options);
        }
        include Lua::display('field_option', $this->dir);
    }
    
    /*
     * 保存字段选项
     */
    private function save_option(){
        $info_new = Lua::post('info_new');
        $info_new = $info_new ? $info_new : array();
        $model_id = Lua::get('model_id');
        $table_id = Lua::get('table_id');
        $tableDB  = $this->_table($table_id);
        $id = Lua::get('id');
        $no_order_new = Lua::post('no_order_new');
        $options = "";
        if ($info_new){
            $serialize = array();
            foreach ($info_new as $k=>$v){
                $serialize[$no_order_new[$k]] = $v;
            }
            $options = serialize($serialize);
        }
        Doo::db()->query("update lua_model_field set fieldoption='$options' where id='$id' and model_id='$model_id' and table_id='$table_id'");
        Lua::write_log($this->user, '更新字段选项', "tableid=$table_id<br />model_id=$model_id<br />table=".$tableDB['tablename']."<br />id=$id", SYSNAME);
        Lua::ajaxmessage('success', '操作成功', "./model.htm?action=field_option&model_id=$model_id&table_id=$table_id&id=$id");
    }
    
    /*
     * 切换字段显示方式
     */
    private function field_change(){
        $db = Lua::get_one("select status from lua_model_field where model_id='".Lua::post('mid')."' and table_id='".Lua::post('tid')."' and id='".Lua::post('id')."'");
        $rt = $db['status'] == 1 ? 0 : 1;
        Doo::db()->query("update lua_model_field set status='$rt' where id='".Lua::post('id')."'");
        Lua::println();
    }
    
    /*
     * 切换字段是否必填
     */
    private function field_must(){
        $db = Lua::get_one("select ismust from lua_model_field where model_id='".Lua::post('mid')."' and table_id='".Lua::post('tid')."' and id='".Lua::post('id')."'");
        $rt = $db['ismust'] == 1 ? 0 : 1;
        Doo::db()->query("update lua_model_field set ismust='$rt' where id='".Lua::post('id')."'");
        Lua::println();
    }
    
    /*
     * 字段排序
     */
    private function field_order(){
        $model_id = Lua::get('model_id');
        $table_id = Lua::get('table_id');
        $no_order = Lua::post('no_order');
        if ($no_order){
            foreach ($no_order as $id=>$value){
                Doo::db()->query("update lua_model_field set vieworder='$value' where id='$id' and model_id='$model_id' and table_id='$table_id'");
            }
            Lua::ajaxmessage('success', '排序成功', "./model.htm?action=field&model_id=$model_id&id=$table_id");
        }
        Lua::ajaxmessage('error', '请先添加字段');
    }
    
    /*
     * 添加字段
     */
    private function field_add(){
        $model_id = Lua::get('model_id');
        $table_id = Lua::get('table_id');
        $mdb = $this->_get($model_id);
        $tdb = $this->_table($table_id);
        $db  = Lua::db_array('lua_model_field');
        $types = Lua::form();
        include Lua::display('field_add', $this->dir);
    }
    
    /*
     * 保存添加字段
     */
    private function save_field(){
        $name = Lua::post('name');
        if (empty($name)){
            Lua::ajaxmessage('error', '字段名称');
        }
        $fieldname = Lua::post('fieldname');
        if (empty($fieldname)){
            Lua::ajaxmessage('error', '字段标识');
        }
        $model_id = Lua::get('model_id');
        $table_id = Lua::get('table_id');
        $mdb = $this->_get($model_id);
        $tdb = $this->_table($table_id);
        $this->_exists($fieldname, $tdb['tablename']);
        $sqlarr = array(
            'fieldname' => $fieldname,
            'fieldtype' => Lua::post('fieldtype'),
            'model_id' => $model_id,
            'name' => $name,
            'table_id' => $table_id,
            'updatetime' => time(),
            'relate_id' => Lua::post('relate_id')
        );
        Lua::insert('lua_model_field', $sqlarr);
        Lua::create_field($tdb['tablename'],Lua::post('fieldtype'),$fieldname);
        Lua::write_log($this->user, '添加模型字段', "tableid=$table_id<br />model_id=$model_id<br />table=".$tdb['tablename']."<br />fieldname=$fieldname", SYSNAME);
        Lua::ajaxmessage('success', '操作成功',"./model.htm?action=field&model_id=$model_id&id=$table_id");
    }
    
    /*
     * 字段是否已存在
     */
    private function _exists($fieldname, $tablename){
        $fields = Doo::db()->fetchAll("SHOW FULL COLUMNS FROM $tablename");
        foreach ($fields as $field){
            $exist[] = $field['Field'];
        }
        if (in_array($fieldname, $exist)){
            Lua::ajaxmessage('error', '此字段标识已存在');
        }
    }
    
    /*
     * 保存添加某个数据表
     */
    private function save_table_add(){
        $modelname = Lua::post('modelname');
        if (empty($modelname)){
            Lua::ajaxmessage('error', '模型名称');
        }
        $tablename = Lua::post('tablename');
        if (empty($tablename)){
            Lua::ajaxmessage('error', '数据表名');
        }
        $subid = Lua::post('subid');
        if (empty($subid)){
            Lua::ajaxmessage('error', '请输入父级标识');
        }
        $mid = Lua::get('mid');
        $mdb = $this->_get($mid, 1);
        $tablename = 'doo_'.$mdb['prefix'].'_'.$tablename;
        $sqlarr = array(
            'createtime' => time(),
            'model_id' => $mid,
            'modelname' => $modelname,
            'upid' => Lua::post('upid'),
            'tablename' => $tablename,
            'model_type' => Lua::post('model_type'),
            'subid' => $subid
        );
        $tid = Lua::insert('lua_model_table', $sqlarr);
        Doo::db()->query("CREATE TABLE `$tablename` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT,`catid` int(10) NOT NULL,`subject` char(100) NOT NULL,`topped` tinyint(3) NOT NULL,`commend` tinyint(3) NOT NULL,`isdel` tinyint(1) NOT NULL,`vieworder` tinyint(1) NOT NULL,`dateline` int(10) NOT NULL,`ip` char(20) NOT NULL,`filename` char(20) NOT NULL,`uid` int(10) NOT NULL,`username` char(20) NOT NULL,`hash` char(20) NOT NULL,`color` char(10) NOT NULL,PRIMARY KEY (`id`),KEY `id` (`id`),KEY `catid` (`catid`),KEY `topped` (`topped`),KEY `commend` (`commend`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        Doo::db()->query("update lua_model set tablenum=tablenum+1 where id='$mid'");
        Lua::write_log($this->user, '添加模型数据表', "tableid=$tid<br />model_id=$mid<br />table=$tablename", SYSNAME);
        Lua::ajaxmessage('success', '操作成功',"./model.htm?action=table&id=$mid");
    }
    
    /*
     * 保存编辑某个数据表
     */
    private function save_table_edit(){
        $modelname = Lua::post('modelname');
        if (empty($modelname)){
            Lua::ajaxmessage('error', '模型名称');
        }
        $sqlarr = array(
            'upid' => Lua::post('upid'),
            'modelname' => $modelname
        );
        $mid = Lua::get('mid');
        $mdb = $this->_get($mid, 1);
        $id  = Lua::get('id');
        $db  = $this->_table($id, 1);
        $where = array(
            'id' => $id
        );
        Lua::update('lua_model_table', $sqlarr, $where);
        Lua::write_log($this->user, '修改数据表', "tableid=$id<br />model_id=$mid<br />modelname=$modelname", SYSNAME);
        Lua::ajaxmessage('success', '操作成功',"./model.htm?action=table&id=$mid");
    }
    
    /*
     * 某个模型下数据表树形结构
     */
    private function _tree($id){
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
     * 删除某个模型
     */
    private function del(){
        $id = Lua::get('id');
        $db = $this->_get($id);
        $tables = Lua::get_more("select tablename from lua_model_table where model_id='$id'");
        if ($tables){
            foreach ($tables as $row){
                Doo::db()->query("drop table ".$row['tablename']);
            }
        }
        Doo::db()->query("delete from lua_model_field where model_id='$id'");
        Doo::db()->query("delete from lua_model_table where model_id='$id'");
        Doo::db()->query("delete from lua_model where id='$id'");
        Lua::write_log($this->user, '删除模型', "model_id=$id<br />modelname=".$db['modelname'], SYSNAME);
        Lua::admin_msg('提示信息', '成功删除', './model.htm');
    }
    
    /*
     * 添加模型
     */
    private function add(){
        $action = 'save_add';
        $db = Lua::db_array('lua_model');
        $channels = $this->_channel();
        include Lua::display('model_add', $this->dir);
    }
    
    /*
     * 编辑模型
     */
    private function edit(){
        $id = Lua::get('id');
        $db = $this->_get($id);
        $action = "save_edit&id=$id";
        include Lua::display('model_add', $this->dir);
    }
    
    /*
     * 保存编辑模型
     */
    private function save_edit(){
        $modelname = Lua::post('modelname');
        if (empty($modelname)){
            Lua::ajaxmessage('error', '模型名称');
        }
        $developer = Lua::post('developer');
        if (empty($developer)){
            Lua::ajaxmessage('error', '开发者');
        }
        $contact = Lua::post('contact');
        if (empty($contact)){
            Lua::ajaxmessage('error', '联系方式');
        }
        $intro = Lua::post('intro');
        if (empty($intro)){
            Lua::ajaxmessage('error', '模型描述');
        }
        $sqlarr = array(
            'contact' => $contact,
            'developer' => $developer,
            'modelname' => $modelname,
            'intro' => $intro
        );
        $id = Lua::get('id');
        $where = array(
            'id' => $id
        );
        Lua::update('lua_model', $sqlarr, $where);
        $db = $this->_get($id, 1);
        Lua::write_log($this->user, '修改模型', "model_id=$id<br />modelname=".$db['modelname'], SYSNAME);
        Lua::ajaxmessage('success', '操作成功', './model.htm');
    }
    
    /*
     * 获取某个模型的信息
     */
    private function _get($id, $ajax = 0){
        $db = Lua::get_one("select * from lua_model where id='$id'");
        if (empty($db)){
            if ($ajax == 1){
                Lua::ajaxmessage('error', '你访问的模型不存在');
            }else{
                Lua::admin_msg('提示信息', '你访问的模型不存在');
            }
        }
        return $db;
    }
    
    /*
     * 更改某个模型的状态
     */
    private function change(){
        $db = Lua::get_one("select status from lua_model where id='".Lua::post('id')."'");
        $rt = $db['status'] == 1 ? 0 : 1;
        Doo::db()->query("update lua_model set status='$rt' where id='".Lua::post('id')."'");
        Lua::println();
    }
    
    /*
     * 保存添加模型
     */
    private function save_add(){
        $cid = Lua::post('cid');
        if (empty($cid)){
            Lua::ajaxmessage('error', '请选择所属频道');
        }
        $modelname = Lua::post('modelname');
        if (empty($modelname)){
            Lua::ajaxmessage('error', '模型名称');
        }
        $developer = Lua::post('developer');
        if (empty($developer)){
            Lua::ajaxmessage('error', '开发者');
        }
        $contact = Lua::post('contact');
        if (empty($contact)){
            Lua::ajaxmessage('error', '联系方式');
        }
        $intro = Lua::post('intro');
        if (empty($intro)){
            Lua::ajaxmessage('error', '模型描述');
        }
        $prefix = Lua::post('prefix');
        if (empty($prefix)){
            Lua::ajaxmessage('error', '模型前缀');
        }
        $sqlarr = array(
            'contact' => $contact,
            'createtime' => time(),
            'developer' => $developer,
            'intro' => $intro,
            'modelname' => $modelname,
            'status' => 1,
            'prefix' => $prefix,
            'mtype' => Lua::post('mtype'),
            'cid' => $cid
        );
        $id = Lua::insert('lua_model', $sqlarr);
        Lua::write_log($this->user, '增加模型', "model_id=$id<br />modelname=$modelname", SYSNAME);
        Lua::ajaxmessage('success', '操作成功', './model.htm');
    }
    
    /*
     * 模型市场
     */
    private function market(){
        include Lua::display('model_market', $this->dir);
    }
    
}