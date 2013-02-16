<?php
Doo::loadController('__auth');

class __admin extends __auth{

    /*
     * 入口
     */
    public function index(){
        $action = Lua::get_post('action');
        $action = $action ? $action : 'home';
        $rs = $this->acl()->process($this->user['perm'], '__admin', $action);
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
     * 栏目权限设置
     */
    private function perm_category(){
        $channels = Lua::get_more("select * from lua_channel where status='1'");
        if (empty($channels)){
            Lua::admin_msg('提示信息', '请先建立频道');
        }
        $uid = Lua::get('uid');
        $udb = Lua::get_one("select * from lua_admin where uid='$uid'");
        if (empty($udb)){
            Lua::admin_msg('提示信息', '此用户不存在');
        }
        $lua = Lua::get('lua');
        $list = $this->_tree($lua,$udb['channel']);
        if ($lua){
            $mycan = unserialize($udb['piece_can']);
        }else{
            $mycan = unserialize($udb['category_can']);
        }
        if (empty($mycan)){
            $mycan = array();
        }
        include Lua::display('admin_perm_cate', $this->dir);
    }
    
    /*
     * 保存权限设置
     */
    private function perm_save(){
        $v1 = Lua::post('v1');
        if (empty($v1)){
            Lua::ajaxmessage('error', '请选择栏目');
        }
        $v1 = serialize($v1);
        $uid = Lua::post('uid');
        $udb = Lua::get_one("select * from lua_admin where uid='$uid'");
        if (empty($udb)){
            Lua::ajaxmessage('error', '此用户不存在');
        }
        $lua = Lua::post('lua');
        $field = $lua ? 'piece_can' : 'category_can';
        $url = $lua ? "&lua=$lua" : "";
        Lua::update('lua_admin', array($field=>$v1), array('uid'=>$uid));
        $log = "uid=$uid<br />title=".($lua ? "碎片栏目" : "栏目");
        Lua::write_log($this->user, '权限设置', $log, SYSNAME);
        Lua::ajaxmessage('success', '操作成功', "./admin.htm?action=perm_category&uid=$uid".$url);
    }
    
    /*
     * 清空权限设置
     */
    private function perm_empty(){
        $uid = Lua::post('uid');
        $udb = Lua::get_one("select * from lua_admin where uid='$uid'");
        if (empty($udb)){
            Lua::ajaxmessage('error', '此用户不存在');
        }
        $lua = Lua::post('lua');
        $field = $lua ? 'piece_can' : 'category_can';
        $url = $lua ? "&lua=$lua" : "";
        Lua::update('lua_admin', array($field=>''), array('uid'=>$uid));
        $log = "uid=$uid<br />title=".($lua ? "清空碎片栏目" : "清空栏目");
        Lua::write_log($this->user, '权限设置', $log, SYSNAME);
        Lua::ajaxmessage('success', '操作成功', "./admin.htm?action=perm_category&uid=$uid".$url);
    }
    
    /*
     * 栏目、碎片栏目树形
     */
    private function _tree($lua = 'category', $perm){
        $lua = $lua ? $lua : 'category';
        $list = Lua::get_more("select * from lua_".$lua." where systemname='".$perm."' order by vieworder asc,id desc");
        $back = array();
        if ($list){
            foreach ($list as $v){
                $out[$v['id']] = $v;
            }
            $tree = new Tree($out);
            $tree->field = 'name';
            $back = $tree->get(0,0,' ');
        }
        return $back;
    }
    
    /*
     * 权限设置
     */
    private function perm(){
        $uid = Lua::get('uid');
        $udb = Lua::get_one("select username,perm from lua_admin where uid='$uid'");
        if (empty($udb)){
            Lua::admin_msg('提示信息', '此用户不存在');
        }
        if ($udb['perm'] == SUPER_MAN){
            Lua::admin_msg('提示信息', '此用户为超级管理员');
        }
        $perms = Lua::perms();
        $aclfile = PROJECT_ROOT.'config/acl.php';
        include $aclfile;
        if (isset($acl[$udb['perm']]['allow'])){
            $myperms = $acl[$udb['perm']]['allow'];
        }else{
            $myperms = array();
        }
        include Lua::display('admin_perm', $this->dir);
    }
    
    /*
     * 显示权限代码
     */
    private function perm_code(){
        $uid = Lua::post('uid');
        $udb = Lua::get_one("select username,perm from lua_admin where uid='$uid'");
        if (empty($udb)){
            Lua::admin_msg('提示信息', '此用户不存在');
        }
        if ($udb['perm'] == SUPER_MAN){
            Lua::admin_msg('提示信息', '此用户为超级管理员');
        }
        $perms = Lua::perms();
        $__member = Lua::post('__member');
        $__category = Lua::post('__category');
        $__content = Lua::post('__content');
        $__file = Lua::post('__file');
        $__piece = Lua::post('__piece');
        if (empty($__member)){
            Lua::admin_msg('提示信息', '请选择会员管理');
        }
        if (empty($__category)){
            Lua::admin_msg('提示信息', '请选择栏目管理');
        }
        if (empty($__content)){
            Lua::admin_msg('提示信息', '请选择内容管理');
        }
        if (empty($__file)){
            Lua::admin_msg('提示信息', '请选择图片管理');
        }
        if (empty($__piece)){
            Lua::admin_msg('提示信息', '请选择碎片管理');
        }
        $__code['__home'] = array("index","info");
        $__code['__login'] = array("index","logout","E404","E401");
        if (in_array('*', $__member)){
            $__code['__member'] = '*';
        }else{
            $__code['__member'] = $__member;
        }
        if (in_array('*', $__category)){
            $__code['__category'] = '*';
        }else{
            $__code['__category'] = $__category;
        }
        if (in_array('*', $__content)){
            $__code['__content'] = '*';
        }else{
            $__code['__content'] = $__content;
        }
        if (in_array('*', $__file)){
            $__code['__file'] = '*';
        }else{
            $__code['__file'] = $__file;
        }
        if (in_array('*', $__piece)){
            $__code['__piece'] = '*';
        }else{
            $__code['__piece'] = $__piece;
        }
        $__code['__extend'] = '*';
        $aclfile = PROJECT_ROOT.'config/acl.php';
        include $aclfile;
        $acl[$udb['perm']]['allow'] = $__code;
        $data = $this->_acl($acl);
        file_put_contents($aclfile, $data);
        Lua::write_log($this->user, 'ACL权限设置', "group=".$udb['perm']."<br />title=更新", SYSNAME);
        Lua::admin_msg('信息提示', '操作成功', './admin.htm');  
    }
    
    /*
     * acl array to html
     */
    private function _acl($acl){
        $data = '';
        foreach ($acl as $k=>$v){
            if ($v['allow'] == '*'){
                $data .= '$acl["'.$k.'"]["allow"] = "*";';
                $data .= "\r\n";
            }else{
                $data .= '$acl["'.$k.'"]["allow"] = array(';
                $data .= "\r\n";
                foreach ($acl[$k]['allow'] as $_k=>$_v){
                    $data .= "\t";
                    if ($_v == '*'){                        
                        $data .= '"'.$_k.'" => "*",';
                    }else{
                        if ($_k == '__home' || $_k == '__login'){
                            $data .= '"'.$_k.'" => array("'.implode('","',$_v).'"),';
                        }else{
                            $data .= '"'.$_k.'" => array("index","'.implode('","',$_v).'"),';
                        }
                    }
                    $data .= "\r\n";
                }
                $data .= ');';
                $data .= "\r\n";
            }
        }
        return "<?php\r\n".$data;
    }
    
    /*
     * 添加管理员
     */
    private function add(){
        $db = Lua::db_array('lua_admin');
        $action = 'save_add';
        include Lua::display('admin_add', $this->dir);
    }
    
    /*
     * 保存添加
     */
    private function save_add(){
        $username = Lua::post('username');
        if (empty($username)){
            Lua::ajaxmessage('error', '用户名');
        }
        $password = Lua::post('password');
        $confirm_password = Lua::post('confirm_password');
        if (empty($password)){
            Lua::ajaxmessage('error', '密码');
        }
        if ($password != $confirm_password){
            Lua::ajaxmessage('error', '二次密码不相同');
        }
        $perm = Lua::post('perm');
        if (empty($perm)){
            Lua::ajaxmessage('error', '用户组');
        }
        $channel = Lua::post('channel');
        if (empty($channel)){
            Lua::ajaxmessage('error', '所属频道');
        }
        $sqlarr = array(
            'gid' => Lua::post('gid'),
            'loginip' => $this->clientIP(),
            'password' => md5($password),
            'perm' => $perm,
            'username' => $username,
            'logintime' => time(),
            'channel' => $channel
        );
        $lastid = Lua::insert('lua_admin',$sqlarr);
        Lua::write_log($this->user, '增加管理员', "uid=$lastid<br />title=$username", SYSNAME);
        Lua::ajaxmessage('success', '操作成功', './admin.htm');        
    }
    
    /*
     * 编辑管理员
     */
    private function edit(){
        $db = Lua::get_one("select * from lua_admin where uid='".Lua::get('uid')."'");
        $action = "save_edit&uid=".Lua::get('uid');
        include Lua::display('admin_add', $this->dir);
    }
    
    /*
     * 保存编辑
     */
    private function save_edit(){
        $username = Lua::post('username');
        if (empty($username)){
            Lua::ajaxmessage('error', '用户名');
        }
        $perm = Lua::post('perm');
        if (empty($perm)){
            Lua::ajaxmessage('error', '用户组');
        }
        $channel = Lua::post('channel');
        if (empty($channel)){
            Lua::ajaxmessage('error', '所属频道');
        }
        $sqlarr = array(
            'username' => $username,
            'perm' => $perm,
            'channel' => $channel
        );
        $where  = array(
            'uid' => Lua::get('uid')
        );
        $password = Lua::post('password');
        if ($password){
            $confirm_password = Lua::post('confirm_password');
            if ($password != $confirm_password){
                Lua::ajaxmessage('error', '二次密码不相同');
            }
            $sqlarr['password'] = md5($password);
        }
        Lua::update('lua_admin', $sqlarr, $where);
        Lua::write_log($this->user, '修改管理员', "uid=".Lua::get('uid')."<br />title=$username", SYSNAME);
        Lua::ajaxmessage('success', '操作成功', './admin.htm');  
    }

    /*
     * 删除管理员
     */
    private function del(){
        $uid = Lua::get('uid');
        $mdb = Lua::get_one("select * from lua_admin where uid='$uid'");
        Doo::db()->query("delete from lua_admin where uid='$uid'");
        $list = Lua::get_more("select * from lua_admin");
        $perms = array();
        foreach ($list as $v){
            $perms[] = $v['perm'];
        }
        $perms = array_unique($perms);
        $aclfile = PROJECT_ROOT.'config/acl.php';
        include $aclfile;
        foreach ($acl as $k=>$v){
            if (!in_array($k, $perms)){
                unset($acl[$k]);
            }
        }
        $data = $this->_acl($acl);
        file_put_contents($aclfile, $data);
        Lua::write_log($this->user, '删除管理员', "uid=$uid<br />title=".$mdb['username'], SYSNAME);
        Lua::admin_msg('提示信息', '成功删除', './admin.htm');
    }
    
    /*
     * 管理员列表
     */
    private function home(){
        $list = Lua::get_more("select * from lua_admin");
        include Lua::display('admin', $this->dir);
    }
    
    /*
     * 更改管理状态
     */
    private function ajax_change(){
        $uid = Lua::post('uid');
        if ($uid){
            $db = Lua::get_one("select gid from lua_admin where uid='$uid'");
            $rt = $db['gid'] == 1 ? 0 : 1;
            Doo::db()->query("update lua_admin set gid='$rt' where uid='$uid'");
            Lua::println();
        }
    }

}