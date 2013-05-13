<?php 

class Lua{
        /*
     * 获取下级数据
     */
    public static function get_sub($fatherid, $subid, $tablename, $select='*', $limit=10, $vieworder = 0){
        $order = $vieworder == 1 ? "vieworder asc,id desc" : "id desc" ;
        return Lua::get_more("select $select from $tablename where isdel='0' and $subid='$fatherid' order by $order limit ".$limit);
    }
    /*
     * 数据切割，适用于瀑布流
     */
    public static function chunk($list, $row){
        $index = 0;
        $ret = array();
        if ($list){
            foreach ($list as $item){
                $mod = $index % $row;
                $ret['col'.$mod][] = $item;
                $index++;
            }
        }
        return $ret;
    }
    
    /*
     * 生成静态页面
     */
    public static function save_html($filename, $dir = ''){
        $data = ob_get_contents();
        ob_end_clean();
        $path = HTML_PATH.$dir;
        if (!file_exists($path)) {
            Doo::loadHelper('DooFile');
            $fileManager = new DooFile(0777);
            $fileManager->create($path);
        }
        echo $data;
        if(file_put_contents($path.$filename, $data)>0){
            return true;
        }
        return false;
    }
    
    /*
     * 读取静态页面带时间缓存
     */
    public static function get_html($filename, $cachetime = 1800){
        if ($cachetime && file_exists(HTML_PATH.$filename) && TIMESTAMP - $cachetime < filemtime(HTML_PATH.$filename)){
            include HTML_PATH.$filename;
            return true;
        }
        return false;
    }
    
    /*
     * 获取静态数据缓存
     */
    public static function get_cache($id, $cachetime = 60){
        if ($id){
            Doo::cache('php')->hashing = false;
            return Doo::cache('php')->get($id, $cachetime);
        }
        return array();
    }
    
    /*
     * 设置静态数据缓存
     */
    public static function set_cache($id, $value, $cachetime = 60){
        Doo::cache('php')->hashing = false;
        Doo::cache('php')->set($id, $value);
    }
    
    /*
     * 格式化时间
     */
    public static function format_date($time, $format = 'Y-m-d H:i'){
        $limit = TIMESTAMP - $time;
        if ($limit < 60){
            return $limit.'秒钟之前';
        }
        if ($limit >= 60 && $limit < 3600){
            return floor($limit/60).'分钟之前';
        }
        if ($limit >= 3600 && $limit < 86400){
            return floor($limit/3600).'小时之前';
        }
        if ($limit >= 86400){
            return date($format, $time);
        }
    }
    
    /*
     * 后台登录记录失败次数
     */
    public static function adminfail($ip, $force = 0, $limit = 15){
        $db = Lua::get_one("select * from lua_admin_fails where ip='$ip'");
        if ($db && $db['nums'] == $limit){
            $session = Doo::session('Lua');
            $session->auth = '';
            Lua::admin_msg('错误提示', '登录失败次数过多，已禁止此IP登录。','/'.ADMIN_ROOT.'/');
        }
        if ($force == 0){
            if (empty($db)){
                Lua::insert('lua_admin_fails', array('ip'=>$ip,'dateline'=>time(),'nums'=>1));
            }else{
                Doo::db()->query("update lua_admin_fails set nums=nums+1 where ip='$ip'");
            }
        }
    }
    
    /*
     * 随机数
     */
    public static function random($length, $numeric = 0) {
        PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
        $seed = base_convert(md5(print_r($_SERVER, 1).microtime()), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
        $hash = '';
        $max = strlen($seed) - 1;
        for($i = 0; $i < $length; $i++) {
                $hash .= $seed[mt_rand(0, $max)];
        }
        return $hash;
    }
    
    /*
     * 日志操作
     */
    public static function write_log($user, $action, $content, $channel){
        $sqlarr = array(
            'uid' => $user['uid'],
            'username' => $user['username'],
            'ip' => $user['loginip'],
            'dateline' => date('Y-m-d H:i:s'),
            'actionname' => $action,
            'content' => $content,
            'path' => $channel
        );
        Lua::insert('lua_logs', $sqlarr);
    }
    
    /*
     * 后台所有权限数组
     */
    public static function perms(){
        $perms = array(
            '__member' => array('home#会员列表','model#会员模型列表','change_status#更新模型状态','change_regtype#更改审核方式','model_field#会员模型字段列表','field_option#模型字段列表','save_field_option#保存字段选项','model_group#模型用户组','group_order#用户组排序','group_add#添加用户组','save_group#保存新增用户组','group_edit#编辑用户组','save_group_edit#保存编辑用户组','group_del#删除用户组','del_model#删除会员模型','user#会员模型的会员列表','user_del#删除会员模型的会员','change_user#更改会员模型的会员状态','user_add#添加会员模型的会员','user_edit#编辑会员模型的会员','save_user_edit#保存编辑会员模型的会员','save_user#保存新增会员模型的会员','field_add#添加字段','save_field#保存字段','field_change_status#切换字段的显示方式','field_ismust#切换字段是否必填','field_order_by#字段排序','field_del#删除字段','model_add#添加会员模型','save_model_add#保存新增会员模型','del#删除注册会员','change#更改注册会员状态','edit#编辑注册会员','save_edit#保存编辑注册会员','add#添加注册会员','save_add#保存新增注册会员'),
            '__category' => array('home#栏目列表','del#删除栏目','order#栏目排序','add#添加栏目','edit#编辑栏目','save_edit#保存编辑栏目','save#保存新增栏目'),
            '__content' => array('home#内容列表','moveit#移动内容','copyit#复制内容','recycleit#放入回收站','undoit#内容还原','orderit#内容排序','commendit#内容推荐','toppedit#内容置顶','del#删除内容','add#添加内容','edit#编辑内容','save_edit#保存编辑内容','save_single#保存单页面内容','save#保存添加内容','recycle#回收站'),
            '__file' => array('upimage#单一张图片上传','uploadEditorImage#编辑器图片上传'),
            '__piece' => array('home#碎片栏目列表','add#添加碎片栏目','edit#编辑碎片栏目','save_edit#保存编辑碎片栏目','order#碎片栏目排序','del#删除碎片栏目','save#保存添加碎片栏目','add_any#添加任意数据','edit_any#编辑任意数据','save_any_add#保存添加任意数据','save_any_edit#保存编辑任意数据','del_any#删除任意数据','any#任意数据管理')
        );
        return $perms;
    }

    /*
     * 自定义字段表单HTML
     */
    public static function html($field_db, $value_db, $father_id = '', $father_value = ''){
        $html = "";
        $field = $field_db['fieldname'];
        $value = $value_db[$field];
        $option = unserialize($field_db['fieldoption']);
        if ($option){
            ksort($option);
        }
        switch ($field_db['fieldtype']){
            case 'text':
            case 'int':
                $html = "<input name='$field' type='text' class='text' value='$value'>";
                break;
            case 'select':
                $html = "<select name='$field'>";
                foreach ($option as $v){
                    if ($v == $value){
                        $html .= "<option value='$v' selected>$v</option>";
                    }else{
                        $html .= "<option value='$v'>$v</option>";
                    }
                }
                $html .= "</select>";
                break;
            case 'textarea':
                $html = "<textarea name='$field' cols='50' class='textarea keytext' rows='4'>$value</textarea>";
                break;
            case 'checkbox':
                $ex_value = array();
                if ($value){
                    $ex_value = explode(',',$value);
                }
                foreach ($option as $v){
                    if (in_array($v, $ex_value)){
                        $html .= "<input name='".$field."[]' type='checkbox' class='radio' value='$v'  checked/>&nbsp;$v&nbsp;&nbsp;";
                    }else{
                        $html .= "<input name='".$field."[]' type='checkbox' class='radio' value='$v'  />&nbsp;$v&nbsp;&nbsp;";
                    }
                }
                break;
            case 'radio':
                foreach ($option as $v){
                    if ($v == $value){
                        $html .= "<input name='$field' type='radio' value='$v'  checked/>&nbsp;$v&nbsp;&nbsp;";
                    }else{
                        $html .= "<input name='$field' type='radio' value='$v'  />&nbsp;$v&nbsp;&nbsp;";
                    }
                }
                break;
            case 'picurl':
                $html = "<input name='$field' type='text' class='text' value='$value' readonly style='float:left;' /><input name='upfile' type='file' id='{$field}_upload' /><script type='text/javascript'>$(document).ready(function(){upfile('#{$field}_upload','upimage','$field');});</script>";
                if ($value){
                    $html .= " <a href='/".SYSNAME."/$value' target='new'>查看</a>";
                }                
                break;
            case 'edit':
                $html = "<script>edit=1;</script><textarea name='$field' style='height:460px;' class='redactor_content'>$value</textarea>";
                break;
            case 'relate':
                $html = "<input id='so_$field' class='text' type='text' onkeyup=\"so_relate('$field',".$field_db['relate_id'].",this.value,0);\" onclick=\"so_default('$field',0);\"/><input type='hidden' name='$field' value='$value' /><div id='div_$field'></div><script>$(function(){so_value('$field',".$field_db['relate_id'].",'$value',0);});</script>";
                break;
            case 'multi':
                $html = "<input id='so_$field' class='text' type='text' onkeyup=\"so_relate('$field',".$field_db['relate_id'].",this.value,1);\" onclick=\"so_default('$field',1);\"/><input type='hidden' name='$field' value='$value' /><input type='hidden' id='model_$field' value='".$field_db['relate_id']."' /><div id='value_$field'></div><div id='div_$field'></div><script>$(function(){so_value('$field',".$field_db['relate_id'].",'$value',1);});</script>";
                break;
            case 'datetime':
                $html = "<input name='$field' readonly type='text' class='text jsdate' value='$value'>";
                break;
            case 'uedit':
                $html = "<script>edit=2;var id_name = 'ueditor_$field';</script><input name='$field' id='ueditor_$field' type='hidden' value=''/><div><script id=\"editor\" type=\"text/plain\">$value</script></div>";
                break;
        }
        if ($father_id){
            if ($father_id == $field){
                $html = "$father_value<input type='hidden' name='$field' value='$father_value'  />";
            }
        }
        return $html;
    }

    /*
     * 在数据表中创建字段
     */
    public static function create_field($table, $type, $fieldname){
        $query = "";
        switch ($type){
            case 'text':
            case 'picurl':
                $query = "ALTER TABLE $table add $fieldname char(100) not null";
                break;
            case 'multi':
                $query = "ALTER TABLE $table add $fieldname char(250) not null";
                break;
            case 'select':
            case 'checkbox':
            case 'radio':
                $query = "ALTER TABLE $table add $fieldname char(40) not null";
                break;
            case 'textarea':
                $query = "ALTER TABLE $table add $fieldname text not null";
                break;
            case 'edit':
            case 'uedit':
                $query = "ALTER TABLE $table add $fieldname mediumtext not null";
                break;
            case 'int':
            case 'relate':
                $query = "ALTER TABLE $table add $fieldname int(10) not null";
                break;
            case 'datetime':
                $query = "ALTER TABLE $table add $fieldname datetime not null";
                break;
        }
        if ($query){
            Doo::db()->query($query);
        }
    }
    
    /*
     * 字段类型
     */
    public static function form(){
        return array(
            'text'=>'简短',
            'select'=>'下拉',
            'textarea'=>'文本',
            'checkbox'=>'多选',
            'picurl'=>'图片',
            'radio'=>'单选',
            'int'=>'数字',
            'datetime' => '时间',
            'edit'=>'默认编辑器',
            'uedit'=>'百度编辑器',
            'relate'=>'关联单一数据',
            'multi'=>'关联多条数据'
        );
    }

    /*
     * 分页
     */
    public static function page($url, $page, $count, $tpp){
        $page = max($page, 1);
        $totalpage = ceil($count/$tpp);
        if ($totalpage <= 1){
            return '';
        }
        $rangepage = 6;
        $startpage = max(1,$page - $rangepage); 
        $endpage   = min($totalpage,$startpage+$rangepage*2 - 1);
        $startpage = min($startpage,$endpage - $rangepage*2 + 1);
        if($startpage < 1) $startpage = 1;
        $url .= strstr($url, '?') ? '&amp;' : '?';
        $html = '<a href="'.$url.'p=1">首页</a>';
        $html .= $page > 1 ? '<a href="'.$url.'p='.($page-1).'">上一页</a>':''; 
        for($i = $startpage;$i <= $endpage;$i++){ 
            $html .= '<a href="'.$url.'p='.$i.'"'.($page == $i ? ' class="current"':'').'>'.$i.'</a>'; 
            if($i == $totalpage) break; 
        }
        $html .= $page < $totalpage ? '<a href="'.$url.'p='.($page+1).'">下一页</a>':''; 
        $html .= '<a href="'.$url.'p='.$totalpage.'">末页</a>'; 
        return $html;
    }
    
    /*
     * ajax 分页
     */
    public static function ajaxpage($url, $page, $count, $tpp){
        $page = max($page, 1);
        $totalpage = ceil($count/$tpp);
        if ($totalpage <= 1){
            return '';
        }
        $rangepage = 6;
        $startpage = max(1,$page - $rangepage); 
        $endpage   = min($totalpage,$startpage+$rangepage*2 - 1);
        $startpage = min($startpage,$endpage - $rangepage*2 + 1);
        if($startpage < 1) $startpage = 1;
        $html = '<a href="javascript:'.$url.'1);">首页</a>';
        $html .= $page > 1 ? '<a href="javascript:'.$url.($page-1).');">上一页</a>':''; 
        for($i = $startpage;$i <= $endpage;$i++){ 
            $html .= '<a href="javascript:'.$url.$i.');"'.($page == $i ? ' class="current"':'').'>'.$i.'</a>'; 
            if($i == $totalpage) break; 
        }
        $html .= $page < $totalpage ? '<a href="javascript:'.$url.($page+1).');">下一页</a>':''; 
        $html .= '<a href="javascript:'.$url.$totalpage.');">末页</a>'; 
        return $html;
    }
    
    /*
     * 删除数据
     */
    public static function delete($table, $query){
        foreach ($query as $field=>$value){
            $__v[] = "$field='$value'";
        }
        Doo::db()->query("delete from $table where ".implode(' and ', $__v));
        return true;
    }
    
    /*
     * 更新数据
     */
    public static function update($table, $query, $where){
        foreach ($query as $field=>$value){
            $__v[] = "$field='$value'";
        }
        foreach ($where as $field=>$value){
            $__w[] = "$field='$value'";
        }
        Doo::db()->query("update $table set ".implode(',',$__v)." where ".implode(' and ',$__w));
        return true;
    }
    
    /*
     * 新增数据
     */
    public static function insert($table, $query, $replace = 0){
        foreach ($query as $field=>$value){
            $__f[] = $field;
            $__v[] = "'$value'";
        }
        $func = $replace == 1 ? 'replace': 'insert';
        Doo::db()->query("$func into $table(".implode(',',$__f).") values (".implode(',',$__v).")");
        return Doo::db()->lastInsertId();
    }
    
    /*
     * ajax提示
     */
    public static function ajaxmessage($type, $message, $url = null){
        echo json_encode(array('type'=>$type, 'info'=>$message, 'url'=>$url));
        exit;
    }
    
    /*
     * 读取某个数据表的表结构
     */
    public static function db_array($table){
        $db = array();
        $fields = Doo::db()->fetchAll("show columns from $table");
        if ($fields){
            foreach ($fields as $field){
                $db[$field['Field']]  = '';
            }
        }
        return $db;
    }
    
    /*
     * 纯粹输出
     */
    public static function println($msg = 'success'){
        echo $msg;
        exit;
    }
    
    /*
     * 404页面
     */
    public static function e404(){
        include Lua::display('404', ADMIN_ROOT.'/moban/');
        exit;
    }
    
    /*
     * 读取多条数据
     */
    public static function get_more($query){
        return Doo::db()->fetchAll($query);
    }
    
    /*
     * 页面执行时间
     */
    public static function querytime(){
        $querytime = Doo::benchmark();
        $querytime = round($querytime, 2);
        return $querytime.'s';
    }
    
    /*
     * 读取一条数据
     */
    public static function get_one($query){
        return Doo::db()->fetchRow($query);
    }
    
    /*
     * 表单传值
     */
    public static function post($var){
        if ($var == 'GLOBALS'){
            return '';
        }
        return isset($_POST[$var]) ? Lua::clean($_POST[$var]) : '';
    }
    
    /*
     * 表单或地址栏传值
     */
    public static function get_post($var){
        return Lua::get($var) ? Lua::get($var) : Lua::post($var);
    }
    
    /*
     * 地址栏传值
     */
    public static function get($var){
        if ($var == 'GLOBALS'){
            return '';
        }
        return isset($_GET[$var]) ? Lua::clean($_GET[$var]) : '';
    }
    
    /*
     * 后台提示跳转页面
     */
    public static function admin_msg($title, $content, $url=''){
        Lua::showmessage($title, $content, $url, 1);
    }
    
    /*
     * 提示跳转页面
     */
    public static function showmessage($title, $content, $url='', $admin = 0){
        if ($admin == 1){
            include Lua::display('message', ADMIN_ROOT.'/moban/');
            exit;
        }
    }
    
    /*
     * 转义
     */
    public static function clean($var, $strip = true){
        if (is_array($var)) {
            foreach ($var as $key => $value) {
                $var[$key] = trim(Lua::clean($value, $strip));
            }
            return $var;
        }elseif (is_numeric($var)) {
            return $var;
        }else{
            return addslashes($strip ? stripslashes($var) : $var);
        }
    }
    
    /*
     * 字符串加解密
     */
    public static function authcode($string, $operation, $code = 'Jack') {
        $key = md5($code);
        $key_length = strlen($key);
        $string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string.$key), 0, 8).$string;
        $string_length = strlen($string);
        $rndkey = $box = array();
        $result = '';
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($key[$i % $key_length]);
            $box[$i] = $i;
        }
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            if(substr($result, 0, 8) == substr(md5(substr($result, 8).$key), 0, 8)) {
                return substr($result, 8);
            }else{
                return '';
            }
        }else{
            return str_replace('=', '', base64_encode($result));
        }
    }
    
    /*
     * 模板显示
     */
    public static function display($file,$dir){
        $tplfile = LUA_ROOT.$dir."$file.htm";
        $objfile = LUA_ROOT.$dir."cache/$file.tpl.php";
        $time = 0;
        if (file_exists($objfile)){
            $time = filemtime($objfile);
        }
        if(filemtime($tplfile) > $time) {
            Tpl::parse_template($file, $dir);
        }
        return $objfile;
    }
    
    /*
     * curl_get 主要用于开放平台
     */
    public static function curl_get($sUrl,$aGetParam){
        $oCurl = curl_init();
        if(stripos($sUrl,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        $aGet = array();
        foreach($aGetParam as $key=>$val){
            $aGet[] = $key."=".urlencode($val);
        }
        curl_setopt($oCurl, CURLOPT_URL, $sUrl."?".join("&",$aGet));
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return '';
        }
    }
    
    /*
     * curl_post 主要用于开放平台
     */
    public static function curl_post($sUrl,$aGetParam){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sUrl);  
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $aGetParam);  
        $data = curl_exec($ch);
        $aStatus = curl_getinfo($ch);
        curl_close($ch);
        return $aStatus;
    }
    
    /*
     * 图片上传
     */
    public static function upload($user, $form = 'Filedata', $thumb = array(), $waterit = 0){
        Doo::loadHelper('DooGdImage');
        $file_path = "files/".date('Y')."/".date('m')."/";
        $upload_path = PROJECT_ROOT.$file_path;
        $GD = new DooGdImage($upload_path, $upload_path);
        if ($GD->checkImageExtension($form, array('jpg','jpeg'))){
            $filename = $GD->uploadImage($form);
            if ($waterit == 1){
                list($newname,) = explode('.',$filename);
                $water = PROJECT_ROOT.'static/img/water.png';
                $GD->waterMarkImage($filename,$water,'right','bottom',$newname);
            }
            if ($thumb){
                $width = $thumb[0];
                $height = $thumb[1];
                $GD->ratioResize($filename, $width, $height);
            }
            // insert db
            $sqlarr = array(
                'hash' => Lua::get_post('hash'),
                'filename' => $file_path.$filename,
                'dateline' => TIMESTAMP,
                'used' => 0,
                'systemname' => SYSNAME,
                'uid' => $user['uid'],
                'username' => $user['username']
            );
            Lua::insert('lua_files', $sqlarr);
            return "1@".$file_path.$filename;
        }
        return "你上传的文件非图片格式";
    }
    
    /*
     * 获取缩略图地址
     */
    public static function get_thumb($filename, $thumb = '_thumb'){
        list($name,$ext) = explode('.',$filename);
        return $name.$thumb.'.'.$ext;
    }
    
    /*
     * 截取字符
     */
    public static function substrs($content,$length,$add='Y'){
        if (strlen($content)>$length) { 
            $hex = ''; 
            $add = $add == 'Y' ? ' ...' : '';
            $str = substr($content,0,$length);
            $len = strlen($str)-1;
            for ($i=$len;$i>=0;$i-=1) {
                $ch = ord($str[$i]);
                $hex .= " $ch"; 
                if (($ch & 128)==0 || ($ch & 192)==192) { 
                    return substr($str,0,$i).$add; 
                }
            }
            return $str.$hex.$add; 
        }
        return $content;
    }
}

/**
 * 通用树形类 
 */
class Tree{
    
    public $arr = array();
    public $icon = array('│ ','├ ');
    public $ret = '';
    public $field = 'modelname';
    
    public function Tree($arr=array()){
        $this->arr = $arr;
        $this->ret = '';
        return is_array($arr);
    }
    
    public function parent($myid){
        $newarr = array();
        if(!isset($this->arr[$myid])) return false;
        $pid = $this->arr[$myid]['upid'];
        $pid = $this->arr[$pid]['upid'];
        if(is_array($this->arr)){
            foreach($this->arr as $id => $a){
                if($a['upid'] == $pid) $newarr[$id] = $a;
            }
        }
        return $newarr;
    }
    
    public function child($myid){
        $a = array();
        $newarr = array();
        if(is_array($this->arr)){
            foreach($this->arr as $id => $a){
                if($a['upid'] == $myid) $newarr[$id] = $a;
            }
        }
        return $newarr ? $newarr : array();
    }
    
    public function get($myid=0, $sid=0, $adds=''){
        $child = $this->child($myid);
        if(is_array($child)) {
            foreach($child as $id=>$a) {
                $j = '';
                $k = '';
                $j .= $this->icon[1];
                $k = $adds ? $this->icon[0] : '';
                $spacer = $adds ? $adds.$j : '';
                @extract($a);
                $a[$this->field] = $spacer.$a[$this->field];
                $this->ret[$a['id']] = $a;
                $fd = $adds.$k;
                $this->get($id, $sid, $fd);
            }
        }
        return $this->ret;
    }
}

/**
 * 采用DZ的模板机制
 */
class Tpl{
	
    public static function parse_template($file,$dir){
        $nest = 5;
        $tplfile = LUA_ROOT.$dir."$file.htm";
        $objfile = LUA_ROOT.$dir."cache/$file.tpl.php";

        if(!@$fp = fopen($tplfile, 'r')) {
            exit("Current moban file '$file.htm' not found or have no access!");
        }

        $template = @fread($fp, filesize($tplfile));
        fclose($fp);

        $var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
        $const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";

        $template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
        $template = str_replace("{LF}", "<?=\"\\n\"?>", $template);
        $template = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template);
        $template = preg_replace("/$var_regexp/es", "Tpl::addquote('<?=\\1?>')", $template);
        $template = preg_replace("/\<\?\=\<\?\=$var_regexp\?\>\?\>/es", "Tpl::addquote('<?php echo isset(\\1) ? \\1 : \"\";?>')", $template);

        $template = "<? if(!defined('LUA_ROOT')) exit('Access Denied'); ?>\n$template";
        $template = preg_replace_callback("/[\n\r\t]*\{template\s+([a-z0-9_]+)\}[\n\r\t]*/is", "Tpl::display", $template);
        $template = preg_replace_callback("/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/is", "Tpl::display", $template);
        $template = preg_replace("/[\n\r\t]*\{eval\s+(.+?)\}[\n\r\t]*/ies", "Tpl::stripvtags('<? \\1 ?>','')", $template);
        $template = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/ies", "Tpl::stripvtags('<? echo \\1; ?>','')", $template);
        $template = preg_replace("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/ies", "Tpl::stripvtags('\\1<? } elseif(\\2) { ?>\\3','')", $template);
        $template = preg_replace("/([\n\r\t]*)\{else\}([\n\r\t]*)/is", "\\1<? } else { ?>\\2", $template);
        // cache
        $template = str_replace('<!-- endcache -->', "\n<?php Doo::cache('front')->end(); ?>\n<?php endif; ?>", $template);
        $template = preg_replace_callback('/<!-- cache\(([^\t\r\n}\)]+)\) -->/', "Tpl::convertCache", $template);

        for($i = 0; $i < $nest; $i++) {
            $template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r]*(.+?)[\n\r]*\{\/loop\}[\n\r\t]*/ies", "Tpl::stripvtags('<? if(is_array(\\1)) { foreach(\\1 as \\2) { ?>','\\3<? } } ?>')", $template);
            $template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)[\n\r\t]*\{\/loop\}[\n\r\t]*/ies", "Tpl::stripvtags('<? if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>','\\4<? } } ?>')", $template);
            $template = preg_replace("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r]*)(.+?)([\n\r]*)\{\/if\}([\n\r\t]*)/ies", "Tpl::stripvtags('\\1<? if(\\2) { ?>\\3','\\4\\5<? } ?>\\6')", $template);
        }

        $template = preg_replace("/\{$const_regexp\}/s", "<?=\\1?>", $template);
        $template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);

        if(!@$fp = fopen($objfile, 'w')) {
            exit("Directory './moban/' not found or have no access!");
        }

        $template = preg_replace("/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/e", "Tpl::transamp('\\0')", $template);
        $template = preg_replace("/\<script[^\>]*?src=\"(.+?)\".*?\>\s*\<\/script\>/ise", "Tpl::stripscriptamp('\\1')", $template);
        $template = preg_replace("/[\n\r\t]*\{block\s+([a-zA-Z0-9_]+)\}(.+?)\{\/block\}/ies", "Tpl::stripblock('\\1', '\\2')", $template);
        $template = str_replace('@this','$this',$template);
        flock($fp, 2);
        fwrite($fp, $template);
        fclose($fp);
    }
    
    public static function display($matches){
        list($file,$dir) = explode('.',$matches[1]);
        return "\n<? include Lua::display('$file',$dir); ?>\n";
    }

    public static function transamp($str) {
        $str = str_replace('&', '&amp;', $str);
        $str = str_replace('&amp;amp;', '&amp;', $str);
        $str = str_replace('\"', '"', $str);
        return $str;
    }

    public static function addquote($var) {
        return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
    }

    public static function stripvtags($expr, $statement) {
        $expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
        $statement = str_replace("\\\"", "\"", $statement);
        return $expr.$statement;
    }

    public static function stripscriptamp($s) {
        $s = str_replace('&amp;', '&', $s);
        return "<script src=\"$s\" type=\"text/javascript\"></script>";
    }

    public static function stripblock($var, $s) {
        $s = str_replace('\\"', '"', $s);
        $s = preg_replace("/<\?=\\\$(.+?)\?>/", "{\$\\1}", $s);
        preg_match_all("/<\?=(.+?)\?>/e", $s, $constary);
        $constadd = '';
        $constary[1] = array_unique($constary[1]);
        foreach($constary[1] as $const) {
            $constadd .= '$__'.$const.' = '.$const.';';
        }
        $s = preg_replace("/<\?=(.+?)\?>/", "{\$__\\1}", $s);
        $s = str_replace('?>', "\n\$$var .= <<<EOF\n", $s);
        $s = str_replace('<?', "\nEOF;\n", $s);
        return "<?\n$constadd\$$var = <<<EOF\n".$s."\nEOF;\n?>";
    }

    public static function convertCache($matches){
        $data = str_replace(array('<?php echo ', '; ?>'), '', $matches[1]);
        $data = explode(',', $data);
        if(sizeof($data)==2){
            $data[1] = intval($data[1]);
            return "<?php if (!Doo::cache('front')->getPart({$data[0]}, {$data[1]})): ?>\n<?php Doo::cache('front')->start({$data[0]}); ?>";
        }else{
            return "<?php if (!Doo::cache('front')->getPart({$data[0]})): ?>\n<?php Doo::cache('front')->start({$data[0]}); ?>";
        }
    }
}