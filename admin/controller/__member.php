<?php
Doo::loadController('__auth');

class __member extends __auth{
    
    public $regtype = array('0'=>'手工审核', '1'=>'自动审核', '2'=>'Email激活');

    /*
     * 入口
     */
    public function index(){
        $action = Lua::get_post('action');
        $action = $action ? $action : 'home';
        $rs = $this->acl()->process($this->user['perm'], '__member', $action);
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
     * 注册会员列表
     */
    private function home(){
        $url = "./member.htm";
        $txt = Lua::get('txt');
        $and = "";
        if ($txt){
            $and = " where username like binary '%$txt%'";
            $url = "./member.htm?txt=$txt";
        }
        $count = Doo::db()->count("select count(*) from lua_member $and");
        $tpp = 20;
        $limit = (($this->page - 1) * $tpp).','.$tpp;
        $list = Lua::get_more("select * from lua_member $and order by uid desc limit ".$limit);
        $page = Lua::page($url, $this->page, $count, $tpp);
        $mods = $this->_get();
        include Lua::display('member', $this->dir);
    }
    
    /*
     * 会员模型列表
     */
    private function model(){
        $mods = $this->_get();
        include Lua::display('member_model', $this->dir);
    }
    
    /*
     * 更改会员模型状态
     */
    private function change_status(){
        $id = Lua::post('id');
        $db = Lua::get_one("select status from lua_member_model where systemname='".SYSNAME."' and id='$id'");
        $rt = $db['status'] == 1 ? 0 : 1;
        Doo::db()->query("update lua_member_model set status='$rt' where systemname='".SYSNAME."' and id='$id'");
        Lua::println();
    }
    
    /*
     * 更改会员模型的审核方式
     */
    private function change_regtype(){
        $id = Lua::post('id');
        $db = Lua::get_one("select regtype from lua_member_model where systemname='".SYSNAME."' and id='$id'");
        $rt = $db['regtype'];
        $rs = $rt+1;
        if ($rs > 2){
            $rs = 0;
        }
        Doo::db()->query("update lua_member_model set regtype='$rs' where systemname='".SYSNAME."' and id='$id'");
        Lua::println();
    }
    
    /*
     * 会员模型字段列表
     */
    private function model_field(){
        $id = Lua::get('id');
        $db = $this->_model($id);
        $mods = $this->_get();
        $list = Lua::get_more("select * from lua_member_model_field where systemname='".SYSNAME."' and model_id='$id' order by vieworder asc,id desc");
        $type = Lua::form();
        include Lua::display('member_model_field', $this->dir);
    }
    
    /*
     * 字段选项
     */
    private function field_option(){
        $model_id = Lua::get('model_id');
        $model_db = $this->_model($model_id);
        $id = Lua::get('id');
        $db = Lua::get_one("select name,fieldoption from lua_member_model_field where systemname='".SYSNAME."' and model_id='$model_id' and id='$id'");
        if (empty($db)){
            Lua::admin_msg('提示信息', '你要操作的字段不存在');
        }
        $options = array();
        if ($db['fieldoption']){
            $options = unserialize($db['fieldoption']);
            ksort($options);
        }
        include Lua::display('member_model_field_option', $this->dir);
    }
    
    /*
     * 保存字段选项
     */
    private function save_field_option(){
        $model_id = Lua::get('model_id');
        $id = Lua::get('id');
        $db = Lua::get_one("select fieldoption from lua_member_model_field where systemname='".SYSNAME."' and model_id='$model_id' and id='$id'");
        $info_new = Lua::post('info_new');
        $no_order_new = Lua::post('no_order_new');
        $option = "";
        if ($info_new){
            $serialize = array();
            foreach ($info_new as $k=>$v){
                $serialize[$no_order_new[$k]] = $v;
            }
            $option = serialize($serialize);
        }
        Doo::db()->query("update lua_member_model_field set fieldoption='$option' where systemname='".SYSNAME."' and model_id='$model_id' and id='$id'");
        Lua::write_log($this->user, '更新会员字段选项', "model_id=$model_id<br />id=$id", SYSNAME);
        Lua::ajaxmessage('success', '操作成功',"./member.htm?action=field_option&model_id=$model_id&id=$id");
    }
    
    /*
     * 用户组
     */
    private function model_group(){
        $id = Lua::get('id');
        $db = $this->_model($id);
        $list = Lua::get_more("select * from lua_member_model_group where systemname='".SYSNAME."' and model_id='$id' order by vieworder asc,id desc");
        include Lua::display('member_model_group', $this->dir);
    }
    
    /*
     * 用户组排序
     */
    private function group_order(){
        $id = Lua::get('id');
        $db = $this->_model($id);
        $order_new = Lua::post('order_new');
        if ($order_new){
            foreach ($order_new as $k=>$v){
                Doo::db()->query("update lua_member_model_group set vieworder='$v' where id='$k' and systemname='".SYSNAME."' and model_id='$id'");
            }
            Lua::ajaxmessage('success', '排序成功', "./member.htm?action=model_group&id=$id");
        }
        Lua::ajaxmessage('error', '请先添加用户组');
    }
    
    /*
     * 添加用户组
     */
    private function group_add(){
        $model_id = Lua::get('model_id');
        $model_db = $this->_model($model_id);
        $db = Lua::db_array('lua_member_model_group');
        $action = "save_group&model_id=$model_id";
        include Lua::display('member_group_add', $this->dir);
    }
    
    /*
     * 保存添加用户组
     */
    private function save_group(){
        $name = Lua::post('name');
        if (empty($name)){
            Lua::ajaxmessage('error', '用户组名称');
        }
        $sqlarr = array(
            'credit' => Lua::post('credit'),
            'expiry' => Lua::post('expiry'),
            'model_id' => Lua::get('model_id'),
            'name' => $name,
            'systemname' => SYSNAME
        );
        Lua::insert('lua_member_model_group', $sqlarr);
        Lua::write_log($this->user, '增加会员用户组', "model_id=".Lua::get('model_id')."<br />id=$id<br />title=$name", SYSNAME);
        Lua::ajaxmessage('success', '操作成功', "./member.htm?action=model_group&id=".Lua::get('model_id'));
    }
    
    /*
     * 编辑用户组
     */
    private function group_edit(){
        $model_id = Lua::get('model_id');
        $model_db = $this->_model($model_id);
        $id = Lua::get('id');
        $db = Lua::get_one("select * from lua_member_model_group where systemname='".SYSNAME."' and model_id='$model_id' and id='$id'");
        $action = "save_group_edit&model_id=$model_id&id=$id";
        include Lua::display('member_group_add', $this->dir);
    }
    
    /*
     * 保存编辑用户组
     */
    private function save_group_edit(){
        $model_id = Lua::get('model_id');
        $id = Lua::get('id');
        $name = Lua::post('name');
        if (empty($name)){
            Lua::ajaxmessage('error', '用户组名称');
        }
        $sqlarr = array(
            'credit' => Lua::post('credit'),
            'expiry' => Lua::post('expiry'),
            'name' => $name
        );
        $where = array(
            'systemname' => SYSNAME,
            'model_id' => $model_id,
            'id' => $id
        );
        Lua::update('lua_member_model_group', $sqlarr, $where);
        Lua::write_log($this->user, '修改会员用户组', "model_id=$model_id<br />id=$id<br />title=$name", SYSNAME);
        Lua::ajaxmessage('success', '修改成功', "./member.htm?action=model_group&id=$model_id");
    }
    
    /*
     * 删除用户组
     */
    private function group_del(){
        $model_id = Lua::get('model_id');
        $model_db = $this->_model($model_id);
        $id = Lua::get('id');
        $db = Lua::get_one("select name from lua_member_model_group where systemname='".SYSNAME."' and model_id='$model_id' and id='$id'");
        Lua::delete('lua_member_model_group', array('systemname'=>SYSNAME,'model_id'=>$model_id,'id'=>$id));
        Lua::write_log($this->user, '删除会员用户组', "model_id=$model_id<br />id=$id<br />title=".$db['name'], SYSNAME);
        Lua::admin_msg('提示信息', '成功删除', "./member.htm?action=model_group&id=$model_id");
    }
    
    /*
     * 删除会员模型
     */
    private function del_model(){
        $id = Lua::get('id');
        $db = $this->_model($id);
        Doo::db()->query("DROP TABLE `".$db['tablename']."`");
        Lua::delete('lua_member_model', array('systemname'=>SYSNAME,'id'=>$id));
        Lua::delete('lua_member_model_field', array('systemname'=>SYSNAME,'model_id'=>$id));
        Lua::delete('lua_member_model_group', array('systemname'=>SYSNAME,'model_id'=>$id));
        Lua::write_log($this->user, '删除会员模型', "id=$id<br />title=".$db['modelname'], SYSNAME);
        Lua::admin_msg('提示信息', '成功删除', './member.htm?action=model');
    }
    
    /*
     * 会员模型里的会员列表
     */
    private function user(){
        $id = Lua::get('id');
        $db = $this->_model($id);
        $url = "./member.htm?action=user&id=$id";
        $txt = Lua::get('txt');
        $and = "";
        if ($txt){
            $and = " where username like binary '%$txt%' ";
            $url = "./member.htm?action=user&id=$id&txt=$txt";
        }
        $show = Lua::get_more("select name,fieldname from lua_member_model_field where systemname='".SYSNAME."' and model_id='$id' and status='1' order by vieworder asc,id desc");
        $count = Doo::db()->count("select count(*) from ".$db['tablename']." $and");
        $tpp = 20;
        $limit = (($this->page - 1) * $tpp).','.$tpp;
        $list = Lua::get_more("select * from ".$db['tablename']." $and order by uid desc limit ".$limit);
        $page = Lua::page($url, $this->page, $count, $tpp);
        include Lua::display('member_user', $this->dir);
    }
    
    /*
     * 删除会员模型里的会员
     */
    private function user_del(){
        $mid = Lua::get('mid');
        $mdb = $this->_model($mid);
        $uid = Lua::get('uid');
        $db = Lua::get_one("select username from ".$mdb['tablename']." where uid='$uid'");
        Lua::delete($mdb['tablename'], array('uid'=>$uid));
        Lua::write_log($this->user, '删除模型会员', "model_id=$mid<br />uid=$uid<br />title=".$db['username'], SYSNAME);
        Lua::admin_msg('提示信息', '成功删除', "./member.htm?action=user&id=$mid");
    }
    
    /*
     * 切换会员模型里的会员状态
     */
    private function change_user(){
        $uid = Lua::post('uid');
        $mid = Lua::post('mid');
        $mdb = $this->_model($mid);
        $udb = Lua::get_one("select status from ".$mdb['tablename']." where uid='$uid'");
        $urs = $udb['status'] == 1 ? 0 : 1;
        Doo::db()->query("update ".$mdb['tablename']." set status='$urs' where uid='$uid'");
        Lua::println();
    }
    
    /*
     * 添加会员模型里的会员
     */
    private function user_add(){
        $model_id = Lua::get('model_id');
        $model_db = $this->_model($model_id);
        $db = Lua::db_array($model_db['tablename']);
        $action = "save_user&model_id=$model_id";
        $groups = Lua::get_more("select * from lua_member_model_group where systemname='".SYSNAME."' and model_id='$model_id' order by vieworder asc,id desc");
        $list = Lua::get_more("select * from lua_member_model_field where systemname='".SYSNAME."' and model_id='$model_id' order by vieworder asc,id desc");
        include Lua::display('member_user_add', $this->dir);
    }
    
    /*
     * 编辑会员模型里的会员
     */
    private function user_edit(){
        $model_id = Lua::get('model_id');
        $model_db = $this->_model($model_id);
        $uid = Lua::get('uid');
        $db = Lua::get_one("select * from ".$model_db['tablename']." where uid='$uid'");
        $action = "save_user_edit&model_id=$model_id&uid=$uid";
        $groups = Lua::get_more("select * from lua_member_model_group where systemname='".SYSNAME."' and model_id='$model_id' order by vieworder asc,id desc");
        $list = Lua::get_more("select * from lua_member_model_field where systemname='".SYSNAME."' and model_id='$model_id' order by vieworder asc,id desc");
        include Lua::display('member_user_add', $this->dir);
    }
    
    /*
     * 保存会员模型里的会员编辑
     */
    private function save_user_edit(){
        $sqlarr = array();
        $password = Lua::post('password');
        if ($password){
            if ($password != Lua::post('confirm_password')){
                Lua::ajaxmessage('error', '二次密码不相同');
            }
            $sqlarr['password'] = md5($password);
        }
        $sqlarr = array(
            'email' => Lua::post('email'),
            'gid' => Lua::post('gid')
        );
        $model_id = Lua::get('model_id');
        $model_db = $this->_model($model_id, 1);
        $list = Lua::get_more("select * from lua_member_model_field where systemname='".SYSNAME."' and model_id='$model_id' order by vieworder asc,id desc");
        $custom = array();
        if ($list){
            foreach ($list as $rs){
                $field = $rs['fieldname'];
                $value = Lua::post($field);
                if ($rs['ismust'] == 1){
                    if ($value == ''){
                        Lua::ajaxmessage('error', $rs['name']);
                    }
                }
                if ($rs['fieldtype'] == 'checkbox'){
                    if ($value){
                        $custom[$field] = implode(',', $value);
                    }else{
                        $custom[$field] = '';
                    }
                }else{
                    $custom[$field] = $value;
                }
            }
        }
        $sqlarr = array_merge_recursive($sqlarr, $custom);
        $uid = Lua::get('uid');
        $udb = Lua::get_one("select username from ".$model_db['tablename']." where uid='$uid'");
        Lua::update($model_db['tablename'], $sqlarr, array('uid'=>$uid));
        Lua::write_log($this->user, '修改模型会员', "model_id=$model_id<br />uid=$uid<br />title=".$udb['username'], SYSNAME);
        Lua::ajaxmessage('success', '操作成功', "./member.htm?action=user&id=$model_id");
    }
    
    /*
     * 保存会员模型里添加会员
     */
    private function save_user(){
        $username = Lua::post('username');
        if (empty($username)){
            Lua::ajaxmessage('error', '用户名');
        }
        $password = Lua::post('password');
        if (empty($password)){
            Lua::ajaxmessage('error', '登录密码');
        }
        if ($password != Lua::post('confirm_password')){
            Lua::ajaxmessage('error', '二次密码不相同');
        }
        $model_id = Lua::get('model_id');
        $model_db = $this->_model($model_id, 1);
        $count = Doo::db()->count("select count(*) from ".$model_db['tablename']." where username='$username'");
        if ($count > 0){
            Lua::ajaxmessage('error', '此用户名已存在');
        }
        $list = Lua::get_more("select * from lua_member_model_field where systemname='".SYSNAME."' and model_id='$model_id' order by vieworder asc,id desc");
        $custom = array();
        if ($list){
            foreach ($list as $rs){
                $field = $rs['fieldname'];
                $value = Lua::post($field);
                if ($rs['ismust'] == 1){
                    if ($value == ''){
                        Lua::ajaxmessage('error', $rs['name']);
                    }
                }
                if ($rs['fieldtype'] == 'checkbox'){
                    if ($value){
                        $custom[$field] = implode(',', $value);
                    }else{
                        $custom[$field] = '';
                    }
                }else{
                    $custom[$field] = $value;
                }
            }
        }
        $sqlarr = array(
            'username' => $username,
            'regtime' => time(),
            'regip' => $this->clientIP(),
            'status' => 1,
            'logs' => 0,
            'lasttime' => time(),
            'password' => md5($password),
            'lastip' => $this->clientIP(),
            'email' => Lua::post('email'),
            'gid' => Lua::post('gid')
        );
        $sqlarr = array_merge_recursive($sqlarr, $custom);
        $lastid = Lua::insert($model_db['tablename'], $sqlarr);
        Lua::write_log($this->user, '增加模型会员', "model_id=$model_id<br />uid=$lastid<br />title=$username", SYSNAME);
        Lua::ajaxmessage('success', '操作成功', "./member.htm?action=user&id=$model_id");
    }
    
    /*
     * 添加字段
     */
    private function field_add(){
        $id = Lua::get('id');
        $db = $this->_model($id);
        $type = Lua::form();
        include Lua::display('member_model_field_add', $this->dir);
    }
    
    /*
     * 保存字段
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
        $id = Lua::get('id');
        $db = $this->_model($id, 1);
        $this->_check($fieldname, $db['tablename']);
        $sqlarr = array(
            'fieldname' => $fieldname,
            'fieldtype' => Lua::post('fieldtype'),
            'name' => $name,
            'systemname' => SYSNAME,
            'updatetime' => time(),
            'model_id' => $id,
            'relate_id' => Lua::post('relate_id')
        );
        Lua::insert('lua_member_model_field', $sqlarr);
        Lua::create_field($db['tablename'], Lua::post('fieldtype'), $fieldname);
        Lua::write_log($this->user, '增加会员字段', "model_id=$id<br />table=".$db['tablename']."<br />field=$fieldname", SYSNAME);
        Lua::ajaxmessage('success', '操作成功', "./member.htm?action=model_field&id=$id");
    }
    
    /*
     * 切换字段显示状态
     */
    private function field_change_status(){
        $id = Lua::post('id');
        $db = Lua::get_one("select status from lua_member_model_field where systemname='".SYSNAME."' and id='$id'");
        $rt = $db['status'] == 1 ? 0 : 1;
        Doo::db()->query("update lua_member_model_field set status='$rt' where systemname='".SYSNAME."' and id='$id'");
        Lua::println();
    }
    
    /*
     * 切换字段是否必填
     */
    private function field_ismust(){
        $id = Lua::post('id');
        $db = Lua::get_one("select ismust from lua_member_model_field where systemname='".SYSNAME."' and id='$id'");
        $rt = $db['ismust'] == 1 ? 0 : 1;
        Doo::db()->query("update lua_member_model_field set ismust='$rt' where systemname='".SYSNAME."' and id='$id'");
        Lua::println();
    }
    
    /*
     * 字段排序
     */
    private function field_order_by(){
        $id = Lua::get('model_id');
        $no_order = Lua::post('no_order');
        if ($no_order){
            foreach ($no_order as $k=>$value){
                Doo::db()->query("update lua_member_model_field set vieworder='$value' where systemname='".SYSNAME."' and model_id='$id' and id='$k'");
            }
            Lua::ajaxmessage('success', '排序成功', "./member.htm?action=model_field&id=$id");
        }
        Lua::ajaxmessage('error', '请先添加字段');
    }
    
    /*
     * 删除字段
     */
    private function field_del(){
        $mid = Lua::get('model_id');
        $id  = Lua::get('id');
        $mdb = $this->_model($mid);
        Doo::db()->query("ALTER TABLE ".$mdb['tablename']." DROP ".$id);
        Doo::db()->query("delete from lua_member_model_field where model_id='$mid' and fieldname='$id'");
        Lua::write_log($this->user, '删除会员字段', "model_id=$mid<br />table=".$mdb['tablename']."<br />field=$id", SYSNAME);
        Lua::admin_msg('提示信息', '成功删除', "./member.htm?action=model_field&id=$mid");
    }
    
    /*
     * 检查字段是否存在
     */
    private function _check($fieldname, $tablename){
        $exist = array();
        $fields = Doo::db()->fetchAll("SHOW FULL COLUMNS FROM $tablename");
        foreach ($fields as $field){
            $exist[] = $field['Field'];
        }
        if (in_array($fieldname, $exist)){
            Lua::ajaxmessage('error', '此字段标识已存在');
        }
    }
    
    /*
     * 获取会员模型信息
     */
    private function _model($id, $ajax = 0){
        $db = Lua::get_one("select * from lua_member_model where systemname='".SYSNAME."' and id='$id'");
        if (empty($db)){
            if ($ajax == 1){
                Lua::ajaxmessage('error', '你操作的会员模型不存在');
            }else{
                Lua::admin_msg('提示信息', '你操作的会员模型不存在');
            }
        }
        return $db;
    }
    
    /*
     * 添加会员模型
     */
    private function model_add(){
        include Lua::display('member_model_add', $this->dir);
    }
    
    /*
     * 保存添加会员模型
     */
    private function save_model_add(){
        $modelname = Lua::post('modelname');
        $tablename = Lua::post('tablename');
        if (empty($modelname)){
            Lua::ajaxmessage('error', '模型名称');
        }
        if (empty($tablename)){
            Lua::ajaxmessage('error', '数据表名');
        }
        $tablename = 'lua_member_'.SYSNAME.'_'.$tablename;
        $sqlarr = array(
            'createtime' => time(),
            'modelname' => $modelname,
            'status' => 1,
            'tablename' => $tablename,
            'systemname' => SYSNAME
        );
        Lua::insert('lua_member_model', $sqlarr);
        $default_create_table = Doo::db()->fetchRow("SHOW CREATE TABLE `lua_member`");
        $default_create_table = $default_create_table['Create Table'];
        $default_create_table = str_replace('lua_member', $tablename, $default_create_table);
        Doo::db()->query($default_create_table);
        Doo::db()->query("TRUNCATE TABLE `".$tablename."`");
        Lua::write_log($this->user, '增加会员模型', "model_id=$mid<br />table=$tablename<br />modelname=$modelname", SYSNAME);
        Lua::ajaxmessage('success', '操作成功', './member.htm?action=model');
    }
    
    /*
     * 删除注册会员
     */
    private function del(){
        $uid = Lua::get('uid');
        $udb = Lua::get_one("select username from lua_member where uid='$uid'");
        Doo::db()->query("delete from lua_member where uid='$uid'");
        Lua::write_log($this->user, '删除注册会员', "uid=$uid<br />username=".$udb['username'], SYSNAME);
        Lua::admin_msg('提示信息', '成功删除', './member.htm');
    }
    
    /*
     * 更改注册会员状态
     */
    private function change(){
        $id = Lua::post('uid');
        $db = Lua::get_one("select status from lua_member where uid='$id'");
        $rt = $db['status'] == 1 ? 0 : 1;
        Doo::db()->query("update lua_member set status='$rt' where uid='$id'");
        Lua::println();
    }
    
    /*
     * 编辑注册会员
     */
    private function edit(){
        $uid = Lua::get('uid');
        $db  = Lua::get_one("select * from lua_member where uid='$uid'");
        $action = "save_edit&uid=$uid";
        include Lua::display('member_add', $this->dir);
    }
    
    /*
     * 保存编辑注册会员
     */
    private function save_edit(){
        $uid = Lua::get('uid');
        $sqlarr['email'] = Lua::post('email');
        $password = Lua::post('password');
        if ($password && $password != Lua::post('confirm_password')){
            Lua::ajaxmessage('error', '二次密码不相同');
            $sqlarr['password'] = md5($password);
        }
        Lua::update('lua_member', $sqlarr, array('uid'=>$uid));
        $udb = Lua::get_one("select username from lua_member where uid='$uid'");
        Lua::write_log($this->user, '修改注册会员', "uid=$uid<br />username=".$udb['username'], SYSNAME);
        Lua::ajaxmessage('success', '操作成功','./member.htm');
    }
    
    /*
     * 添加注册会员
     */
    private function add(){
        $db = Lua::db_array('lua_member');
        $action = 'save_add';
        include Lua::display('member_add', $this->dir);
    }
    
    /*
     * 保存注册会员
     */
    private function save_add(){
        $username = Lua::post('username');
        if (empty($username)){
            Lua::ajaxmessage('error', '用户名');
        }
        $password = Lua::post('password');
        if (empty($password)){
            Lua::ajaxmessage('error', '登录密码');
        }
        if ($password != Lua::post('confirm_password')){
            Lua::ajaxmessage('error', '二次密码不相同');
        }
        $count  = Doo::db()->count("select count(*) from lua_member where username='$username'");
        if ($count > 0){
            Lua::ajaxmessage('error', '此用户名已被使用');
        }
        $sqlarr = array(
            'email' => Lua::post('email'),
            'lastip' => $this->clientIP(),
            'lasttime' => time(),
            'password' => md5($password),
            'regip' => $this->clientIP(),
            'regtime' => time(),
            'status' => 1,
            'username' => $username
        );
        $uid = Lua::insert('lua_member', $sqlarr);
        Lua::write_log($this->user, '增加注册会员', "uid=$uid<br />username=$username", SYSNAME);
        Lua::ajaxmessage('success', '操作成功','./member.htm');
    }
    
    /*
     * 获取会员模型列表
     */
    private function _get(){
        return Lua::get_more("select * from lua_member_model where systemname='".SYSNAME."'");
    }

}