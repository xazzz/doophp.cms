<?php
Doo::loadController('__auth');

class __content extends __auth{
    
    // 栏目数据
    public $cate_db;
    
    // 主表数据
    public $mode_db;
    
    // 来源
    public $lua = 'category';
    
    // 来源附加网址
    public $lua_url = "";
    
    /*
     * 入口
     */
    public function index(){
        $action = Lua::get_post('action');
        $action = $action ? $action : 'home';
        $rs = $this->acl()->process($this->user['perm'], '__content', $action);
        if ($rs){
            return $rs;            
        }
        if (method_exists($this, $action)){
            $lua = Lua::get('lua');
            if ($lua){
                $this->lua = $lua;
                $this->lua_url = "&lua=$lua";
            }
            $this->$action();
        }else{
            Lua::e404();
        }
    }
    
    /*
     * 内容正常列表
     */
    private function home($isdel = 0){
        $catid = Lua::get('catid');
        $this->_condition($catid);
        $Ftree = $this->_tree($this->cate_db['upid']);
        $Stree = $this->_tree($this->cate_db['id']);
        $and = "";
        // 下级模型数据处理
        $tableid = Lua::get('tableid');
        $tid = Lua::get('tid');
        $table_db = $this->_table_db($tableid);
        $db = $nav = array();
        $suffix = "";
        if ($table_db){
            if (empty($tid)){
                Lua::admin_msg('提示信息', '数据错误，请检查ID值');
            }
            $father_db = $this->_table_db($table_db['upid']);
            $db = $this->_db($father_db, $tid);
            $this->mode_db = $table_db;
            $nav = $this->_next_mods($this->mode_db['upid']);
            $suffix = "&tableid=$tableid&tid=$tid";
            $and = " and ".$father_db['subid']."='$tid' ";
        }
        $mods = $this->_next_mods($this->mode_db['id']);
        // end
        if ($this->mode_db['model_type'] == 1){
            // 单页面模式
            if ($this->cate_db['add_perm'] == 0){
                Lua::admin_msg('提示信息', '此栏目不允许添加内容');
            }
            $tpl = "content_single";
            $rs = Lua::get_one("select * from ".$this->mode_db['tablename']." where catid='$catid'");
            if (empty($rs)){
                $rs = Lua::db_array($this->mode_db['tablename']);
            }
            $fields = $this->_fields($this->mode_db);
            if (empty($father_db)){
                $father_db['subid'] = '';
            }
            $action = "save_single&catid=$catid".$suffix.$this->lua_url;
        }else{
            // 列表模式
            $url = "./content.htm?catid=$catid".$suffix;
            $range = range(1,9);
            $tpl = "content";   
            $fields = $this->_fields($this->mode_db, 1);
            // soso
            $txt = Lua::get('txt');
            if ($txt){
                $and .= " and subject like binary '%$txt%' ";
                $url .= "&txt=$txt";
            }
            $topped = Lua::get('topped');
            if ($topped){
                $and .= " and topped='$topped' ";
                $url .= "&topped=$topped";
            }
            $commend = Lua::get('commend');
            if ($commend){
                $and .= " and commend='$commend' ";
                $url .= "&commend=$commend";
            }
            $catids = array();
            $catidb = array();
            $father = array();
            if ($Stree){
                foreach ($Stree as $v){
                    $catids[] = $v['id'];
                    $catidb[$v['id']] = $v;
                }
            }
            if ($this->cate_db['add_perm'] == 1){
                $and .= " and catid='$catid' ";
                $father = Lua::get_one("select * from lua_".$this->lua." where id='".$this->cate_db['upid']."'");
            }else{
                if ($catids){
                    $and .= " and catid in (".implode(',', $catids).")";
                }
            }            
            // end
            $count = Doo::db()->count("select count(*) from ".$this->mode_db['tablename']." where isdel='$isdel' $and");
            $tpp = 20;
            $limit = (($this->page - 1) * $tpp).','.$tpp;
            $list = Lua::get_more("select * from ".$this->mode_db['tablename']." where isdel='$isdel' $and order by vieworder asc,id desc limit ".$limit);
            $page = Lua::page($url.$this->lua_url, $this->page, $count, $tpp);
            $same = $this->_same($this->cate_db['model_id']);
        }
        include Lua::display($tpl, $this->dir);
    }
    
    /*
     * 获取下级模型列表
     */
    private function _next_mods($id){
        return Lua::get_more("select * from lua_model_table where upid='$id'");
    }
    
    /*
     * 批量日志操作
     */
    private function batch_log($msg, $true = 0){
        if ($true == 1){
            $catid = Lua::post('catid');
        }else{
            $catid = Lua::post('trueid');
        }
        $catdb = Lua::get_one("select name from lua_category where id='$catid'");
        Lua::write_log($this->user, '批量'.$msg.'信息', "catid=$catid<br />title=".$catdb['name'], SYSNAME);
    }
    
    /*
     * 移动内容
     */
    private function moveit(){
        $catid = Lua::post('catid');
        $this->batch_log('移动');
        $this->_option('catid', $catid, '移动成功');
    }
    
    /*
     * 复制内容
     */
    private function copyit(){
        $catid = Lua::post('catid');
        $value = Lua::post('values');
        if ($value){
            if ($catid){
                $cate_db = $this->_get($catid);
                $mode_db = $this->_table($cate_db['model_id'], 1);
                $fields  = Lua::get_more("SHOW FIELDS FROM ".$mode_db['tablename']);
                unset($fields[0]);
                foreach ($fields as $row){
                    $field_str[] = $row['Field'];
                }
                $field_str = implode(',',$field_str);
                $tablename = $mode_db['tablename'];
                foreach ($value as $id){
                    Doo::db()->query("insert into $tablename($field_str) select $field_str from $tablename where id='$id'");
                    $lastid = Doo::db()->lastInsertId();
                    Doo::db()->query("update $tablename set catid='$catid',dateline='".time()."' where id='$lastid'");
                }
                $this->batch_log('复制');
                Lua::ajaxmessage('success', '复制成功', "./content.htm?catid=$catid".$this->lua_url);
            }
        }
        Lua::ajaxmessage('error', '请选择对象');
    }
    
    /*
     * 多属性操作
     */
    private function _option($field, $v, $message, $force = 0){
        $catid = Lua::post('catid');
        if ($force == 1){
            $catid = Lua::post('trueid');
        }
        $value = Lua::post('values');
        if ($value){
            $im = is_array($v) ? array() : implode(',',$value);
            if ($catid){
                $cate_db = $this->_get($catid);
                $mode_db = $this->_table($cate_db['model_id'], 1);
                // 数据模型处理
                $tableid = Lua::get('tableid');
                $tid = Lua::get('tid');
                $table_db = $this->_table_db($tableid);
                $suffix = "";
                if ($table_db){
                    $mode_db = $table_db;
                    $suffix = "&tableid=$tableid&tid=$tid";
                }
                // end
                if (is_array($v)){
                    foreach ($v as $_id=>$_va){
                        Doo::db()->query("update ".$mode_db['tablename']." set $field='$_va' where id='$_id'");
                    }
                }else{
                    Doo::db()->query("update ".$mode_db['tablename']." set $field='$v' where id in ($im)");
                }
                Lua::ajaxmessage('success', $message, "./content.htm?catid=$catid".$suffix.$this->lua_url);
            }
        }
        Lua::ajaxmessage('error', '请选择对象');
    }
    
    /*
     * 放入回收站
     */
    private function recycleit(){
        $this->batch_log('移除');
        $this->_option('isdel', 1, '成功放入回收站');
    }
    
    /*
     * 数据还原
     */
    private function undoit(){
        $this->batch_log('还原');
        $this->_option('isdel', 0, '成功还原');
    }
    
    /*
     * 数据排序
     */
    private function orderit(){
        $v = Lua::post('values');
        $r = array();
        if ($v){
            $v = str_replace(array('order_new','%5B','%5D'),array('','',''),$v);
            $a = explode('&',$v);
            if ($a){
                foreach ($a as $k){
                    list($x,$y) = explode('=',$k);
                    $r[$x] = $y;
                }
            }
        }
        $this->batch_log('排序');
        $this->_option('vieworder', $r, '成功排序');
    }
    
    /*
     * 推荐
     */
    private function commendit(){
        $cid = Lua::post('catid');
        $msg = $cid == 0 ? '取消推荐' : '推荐' ;
        $this->batch_log($msg);
        $this->_option('commend', $cid, '推荐成功', 1);
    }
    
    /*
     * 获取相同模型的栏目
     */
    private function _same($model_id){
        return Lua::get_more("select * from lua_".$this->lua." where systemname='".SYSNAME."' and model_id='$model_id' and add_perm='1'");
    }
    
    /*
     * 置顶
     */
    private function toppedit(){
        $cid = Lua::post('catid');
        $msg = $cid == 0 ? '取消置顶' : '置顶' ;
        $this->batch_log($msg);
        $this->_option('topped', $cid, '置顶成功', 1);
    }
    
    /*
     * 删除数据
     */
    private function del(){
        $catid = Lua::post('catid');
        $value = Lua::post('values');
        if ($value){
            if ($catid){
                $im = implode(',',$value);
                $cate_db = $this->_get($catid);
                $mode_db = $this->_table($cate_db['model_id'], 1);
                // 数据模型处理
                $tableid = Lua::get('tableid');
                $tid = Lua::get('tid');
                $table_db = $this->_table_db($tableid);
                $suffix = "";
                if ($table_db){
                    $mode_db = $table_db;
                    $suffix = "&tableid=$tableid&tid=$tid";
                }
                // end
                foreach ($value as $id){
                    $this->_table_for_del($cate_db['model_id'], $mode_db['id'], $mode_db['subid'], $id);
                }
                Doo::db()->query("delete from ".$mode_db['tablename']." where id in ($im)");
                $this->batch_log('删除', 1);
                Lua::ajaxmessage('success', '删除成功', "./content.htm?catid=$catid".$suffix.$this->lua_url);
            }
        }
        Lua::ajaxmessage('error', '请选择对象');
    }
    
    /*
     * 获取关联模型 用于删除数据
     */
    private function _table_for_del($model_id, $table_id, $subid, $id){
        $tables = Lua::get_more("select * from lua_model_table where model_id='$model_id' and upid='$table_id'");
        if ($tables){
            foreach ($tables as $v){
                $tablename = $v['tablename'];
                $tablesubid = $v['subid'];
                $list = Lua::get_more("select * from $tablename where $subid='$id'");
                Doo::db()->query("delete from $tablename where $subid='$id'");
                if ($list){
                    foreach ($list as $r){
                        $this->_table_for_del($model_id, $v['id'], $tablesubid, $r['id']);
                    }
                }
            }
        }
    }
    
    /*
     * 添加内容
     */
    private function add(){
        $catid = Lua::get('catid');
        $this->_condition($catid);
        // 数据模型处理
        $tableid = Lua::get('tableid');
        $tid = Lua::get('tid');
        $table_db = $this->_table_db($tableid);
        $rs = array();
        $suffix = "";
        $father_id = "";
        $father_value = "";
        if ($table_db){
            $rs = $this->_db($this->mode_db, $tid);
            $this->mode_db = $table_db;
            $father_value = $tid;
            $father_db = Lua::get_one("select subid from lua_model_table where id='$table_db[upid]'");
            $father_id = $father_db['subid'];
            $suffix = "&tableid=$tableid&tid=$tid";
        }
        // end
        $fields = $this->_fields($this->mode_db);
        $action = "save&catid=$catid".$suffix.$this->lua_url;
        $db = Lua::db_array($this->mode_db['tablename']);
        include Lua::display('content_add', $this->dir);
    }
    
    /*
     * 编辑内容
     */
    private function edit(){
        $catid = Lua::get('catid');
        $this->_condition($catid);
        // 数据模型处理
        $tableid = Lua::get('tableid');
        $tid = Lua::get('tid');
        $table_db = $this->_table_db($tableid);
        $rs = array();
        $suffix = "";
        $father_id = "";
        $father_value = "";
        if ($table_db){
            $rs = $this->_db($this->mode_db, $tid);
            $this->mode_db = $table_db;
            $father_value = $tid;
            $father_db = Lua::get_one("select subid from lua_model_table where id='$table_db[upid]'");
            $father_id = $father_db['subid'];
            $suffix = "&tableid=$tableid&tid=$tid";
        }
        // end
        $fields = $this->_fields($this->mode_db);
        $id = Lua::get('id');
        $action = "save_edit&catid=$catid&id=$id".$suffix.$this->lua_url;
        $db = Lua::get_one("select * from ".$this->mode_db['tablename']." where id='$id'");
        include Lua::display('content_add', $this->dir);
    }
    
    /*
     * 保存编辑内容
     */
    private function save_edit(){
        $catid = Lua::get('catid');
        $this->_condition($catid, 1);
        $id = Lua::get('id');
        // 数据模型处理
        $tableid = Lua::get('tableid');
        $tid = Lua::get('tid');
        $table_db = $this->_table_db($tableid);
        $suffix = "";
        if ($table_db){
            $this->mode_db = $table_db;
            $suffix = "&tableid=$tableid&tid=$tid";
        }
        // end
        $db = $this->_db($this->mode_db, $id);
        $query = $this->_query($this->mode_db, $db);
        if (!is_array($query)){
            Lua::ajaxmessage('error', $query);
        }
        Lua::update($this->mode_db['tablename'], $query, array('id'=>$id));
        Lua::write_log($this->user, '修改信息', "catid=$catid<br />id=$id<br />title=".$query['subject'], SYSNAME);
        Lua::ajaxmessage('success', '操作成功', "./content.htm?catid=$catid".$suffix.$this->lua_url);
    }
    
    /*
     * 获取数据表数据
     */
    private function _db($db, $id){
        return Lua::get_one("select * from ".$db['tablename']." where id='$id'");
    }
    
    /*
     * 保存单页面内容
     */
    private function save_single(){
        $catid = Lua::get('catid');
        $this->_condition($catid, 1);
        // 数据模型处理
        $tableid = Lua::get('tableid');
        $tid = Lua::get('tid');
        $table_db = $this->_table_db($tableid);
        $suffix = "";
        if ($table_db){
            $this->mode_db = $table_db;
            $suffix = "&tableid=$tableid&tid=$tid";
        }
        // end
        $db = Lua::get_one("select * from ".$this->mode_db['tablename']." where catid='$catid'");
        $query = $this->_query($this->mode_db, $db);
        if (!is_array($query)){
            Lua::ajaxmessage('error', $query);
        }        
        if ($db){
            Lua::update($this->mode_db['tablename'], $query, array('id'=>$db['id']));
            $lastid = $db['id'];
        }else{
            $query['catid'] = $catid;
            $query['dateline'] = time();
            $query['ip'] = $this->clientIP();
            $query['isdel'] = 0;
            $query['uid'] = $this->user['uid'];
            $query['username'] = $this->user['username'];
            $lastid = Lua::insert($this->mode_db['tablename'], $query);
        }
        Lua::write_log($this->user, '更新单页面信息', "catid=$catid<br />id=$lastid<br />title=".$query['subject'], SYSNAME);
        Lua::ajaxmessage('success', '操作成功', "./content.htm?catid=$catid".$suffix.$this->lua_url);
    }
    
    /*
     * 保存添加内容
     */
    private function save(){
        $catid = Lua::get('catid');
        $this->_condition($catid, 1);
        // 数据模型处理
        $tableid = Lua::get('tableid');
        $tid = Lua::get('tid');
        $table_db = $this->_table_db($tableid);
        $suffix = "";
        if ($table_db){
            $this->mode_db = $table_db;
            $suffix = "&tableid=$tableid&tid=$tid";
        }
        // end
        $query = $this->_query($this->mode_db);
        if (!is_array($query)){
            Lua::ajaxmessage('error', $query);
        }
        $query['catid'] = $catid;
        $query['dateline'] = time();
        $query['ip'] = $this->clientIP();
        $query['isdel'] = 0;
        $query['uid'] = $this->user['uid'];
        $query['username'] = $this->user['username'];
        $id = Lua::insert($this->mode_db['tablename'], $query);
        Lua::write_log($this->user, '增加信息', "catid=$catid<br />id=$id<br />title=".$query['subject'], SYSNAME);
        Lua::ajaxmessage('success', '操作成功', "./content.htm?catid=$catid".$suffix.$this->lua_url);
    }
    
    /*
     * 化字段为力量
     */
    private function _query($db, $rs = array()){
        $fields = $this->_fields($db);
        $query = array();
        $subject = Lua::post('subject');
        if (empty($subject)){
            return '标题';
        }
        $filename = Lua::post('filename');
        $query['subject'] = $subject;
        if ($filename){
            $count = Doo::db()->count("select count(*) from ".$db['tablename']." where filename='$filename'");
            if ((($rs && $rs['filename'] != $filename) || empty($rs)) && $count > 0){
                return '静态名称已存在';
            }
            $query['filename'] = $filename;
        }
        if ($fields){
            foreach ($fields as $rs){
                $field = $rs['fieldname'];
                $value = Lua::post($field);
                if ($rs['ismust'] == 1){
                    if ($value == ''){
                        return $rs['name'];
                    }
                }
                if ($rs['fieldtype'] == 'checkbox'){
                    if ($value){
                        $query[$field] = implode(',', $value);
                    }else{
                        $query[$field] = '';
                    }
                }else{
                    $query[$field] = $value;
                }
            }
        }
        return $query;
    }
    
    /*
     * 获取所有字段列表
     */
    private function _fields($db, $show = 0){
        $and = $show == 1 ? " and status='1' " : "";
        $list = Lua::get_more("select * from lua_model_field where model_id='".$db['model_id']."' and table_id='".$db['id']."' $and order by vieworder asc,id asc");
        $oute = array();
        if ($list){
            foreach ($list as $v){
                $oute[$v['id']] = $v;
            }
        }
        return $oute;
    }
    
    /*
     * 查找关联数据
     */
    private function so_relate(){
        $value = Lua::post('value');
        if (empty($value)){
            Lua::println("<font color='red'>请输入关键字</font>");
        }
        $model_id = Lua::post('model_id');
        $db = $this->_table_db($model_id);
        $mode_type = Lua::post('mode');
        if ($db){
            $list = Lua::get_more("select id,subject from ".$db['tablename']." where subject like binary '%$value%' order by id desc limit 10");
            if ($list){
                $id = Lua::post('id');
                foreach ($list as $row){
                    echo "<a href='javascript:;' onclick=\"so_select_it('$id',".$row['id'].",'".$row['subject']."','".$mode_type."');\">".$row['subject']."</a><br />";
                }
            }else{
                Lua::println("<font color='blue'>没有找到相关内容</font>");
            }
        }else{
            Lua::println('<font color="red">你关联的模型为空</font>');
        }
    }
    
    /*
     * 编辑内容时显示关联的标题
     */
    private function so_value(){
        $mode = Lua::post('mode');
        $value = Lua::post('value');
        if ($value){
            $model_id = Lua::post('model_id');
            $db = $this->_table_db($model_id);
            if ($db){
                if ($mode == 0){
                    $row = Lua::get_one("select subject from ".$db['tablename']." where id='".intval($value)."'");
                    Lua::println($row['subject']);
                }else{
                    $exv = explode(',',$value);
                    $out = array();
                    if ($exv){
                        foreach ($exv as $id){
                            if ($id){
                                $out[] = intval($id);
                            }
                        }
                    }
                    if ($out){
                        $out = array_unique($out);
                        $ime = implode(',',$out);
                        $list = Lua::get_more("select id,subject from ".$db['tablename']." where id in (".$ime.")");
                        if ($list){
                            foreach ($list as $row){
                                echo $row['subject']." <a href='javascript:;' onclick=\"so_delete('$ime', ".$row['id'].", '".Lua::post('id')."','".$model_id."');\" title='移除'>×</a>&nbsp;";
                            }
                            echo "<script>$(\"input[name='".Lua::post('id')."']\").val('".$ime."');</script>";
                        }
                    }
                }
            }
        }
    }
    
    /*
     * 多关联模式下移除某值
     */
    private function so_delete(){
        $v1 = Lua::post('v1');
        $v2 = Lua::post('v2');
        if ($v1){
            $e1 = explode(',',$v1);
            $e1 = array_unique($e1);
            $o1 = array();
            foreach ($e1 as  $r1){
                if ($r1){
                    $r1 = intval($r1);
                    if ($r1 == $v2){
                        continue;
                    }
                    $o1[] = $r1;
                }
            }
            if ($o1){
                echo implode(',',$o1);
            }else{
                echo '';
            }
        }
    }
    
    /*
     * 内容回收站列表
     */
    private function recycle(){
        $this->home(1);
    }
    
    /*
     * 获取下级栏目或平级栏目
     */
    private function _tree($id){
        return Lua::get_more("select * from lua_".$this->lua." where systemname='".SYSNAME."' and upid='$id' order by vieworder asc,id desc");
    }
    
    /*
     * 前提条件
     */
    private function _condition($id, $ajax = 0){
        $field = $this->lua == 'piece' ? "piece_can" : "category_can";
        if ($this->user[$field]){
            $value = unserialize($this->user[$field]);
            if (!in_array($id,$value)){
                if ($ajax == 1){
                    Lua::ajaxmessage('error', '你无权操作');
                }else{
                    Lua::admin_msg('提示信息', '你无权操作');
                }
            }
        }
        $this->cate_db = $this->_get($id, $ajax);
        $this->mode_db = $this->_table($this->cate_db['model_id'], $ajax);
    }
    
    /*
     * 获取栏目信息
     */
    private function _get($id, $ajax = 0){
        $db = Lua::get_one("select * from lua_".$this->lua." where systemname='".SYSNAME."' and id='$id'");
        if (empty($db)){
            if ($ajax == 1){
                Lua::ajaxmessage('error', '栏目不存在');
            }else{
                Lua::admin_msg('提示信息', '栏目不存在');
            }
        }
        return $db;
    }
    
    /*
     * 获取模型数据表
     */
    private function _table($id, $ajax = 0){
        $db = Lua::get_one("select * from lua_model_table where model_id='$id' and upid='0'");
        if (empty($db)){
            if ($ajax == 1){
                Lua::ajaxmessage('error', '栏目没数据表');
            }else{
                Lua::admin_msg('提示信息', '栏目没数据表');
            }
        }
        return $db;
    }
    
    /*
     * 根据ID获取模型数据表
     */
    private function _table_db($id){
        return Lua::get_one("select * from lua_model_table where id='$id'");
    }
    
}