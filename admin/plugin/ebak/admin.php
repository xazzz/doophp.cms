<?php

// 数据备份 by Lua

class ebak extends __auth{
    
    public $plugin_dir;
    public $dir;
    public $img;
    public $user;
    public $cache;

    public function _set($plugin_dir, $dir, $img, $user){
        $this->plugin_dir = $plugin_dir;
        $this->tpl = SYSNAME.'/plugin/ebak/tpl/';
        $this->dir = $dir;
        $this->img = $img;
        $this->user = $user;
        $this->cache = $plugin_dir.'ebak/cache/';
    }
    
    public function _home(){
        Doo::loadHelper('DooFile');
        $fileManager = new DooFile();
        $list = $fileManager->getList($this->cache);
        if (!$list){
            $list = array();
        }
        include Lua::display('home', $this->tpl);
    }
    
    public function _down(){
        $dir = Lua::get('dir');
        $path = $this->cache.$dir.'/';
        if (file_exists($path)){
            $zipname = $path.$dir.'.zip';
            if (!file_exists($zipname)){
                Doo::loadClass('Zip');
                $zip = new Zip();
                $zip->Zip($path, $zipname);
            }
            header('Content-type: application/octet-stream'); 
            header('Content-Length: ' . filesize($zipname));
            header("Content-Disposition: attachment; filename=$dir.zip");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($zipname);
            unlink($zipname);
        }
    }
    
    public function _del(){
        $dir = Lua::get('dir');
        Doo::loadHelper('DooFile');
        $fileManager = new DooFile(0777);
        if (file_exists($this->cache.$dir.'/')){
            $fileManager->delete($this->cache.$dir.'/');
        }
        Lua::admin_msg('提示信息', '成功删除', './plugin.htm?action=ebak');
    }
    
    public function _import(){
        $dir = Lua::post('dir');
        if (file_exists($this->cache.$dir.'/')){
            $path = $this->cache.$dir.'/';
            include $path.'config.php';
            $btb=explode(",",$b_table);
            $t = intval(Lua::post('t'));
            $p = Lua::post('p') ? Lua::post('p') : 1;
            $count = count($btb);
            if($p>=$tb[$btb[$t]]){
                $t++;
                if($t>=$count){
                    echo 'success';exit;
                }
                $nfile=$btb[$t]."_1.php";
                include $path.$nfile;
                $this->jsonb('还原 '.$btb[$t-1].' 表完毕，正在进入下一个表还原......', $dir, 0, $t);
            }
            $p++;
            $nfile=$btb[$t]."_".$p.".php";
            include $path.$nfile;
            $this->jsonb('Table Name&nbsp;:&nbsp;<b>'.$btb[$t].'</b><br />Table&nbsp;:&nbsp;<b>'.($t+1).'/'.$count.'</b><br />File&nbsp;:&nbsp;<b>'.$p.'/'.$tb[$btb[$t]].'</b><br />一组数据恢复完毕，正在进入下一组数据......', $dir, $p, $t);
        }
    }
    
    public function _doit(){
        $dirs = date('Y-m-d');
        $path = $this->cache.$dirs.'/';
        $configFile = $path.'config.php';
        Doo::loadHelper('DooFile');
        $fileManager = new DooFile(0777);
        if (!file_exists($configFile)) {
            if (!file_exists($path)) {
                $fileManager->create($path);
            }
            $tables = Doo::db()->fetchAll("SHOW TABLE STATUS");
            $b_table = $d_table = '';
            foreach ($tables as $row){
                $b_table .= $row['Name'].",";
                $d_table .= "\$tb['".$row['Name']."']=0;\r\n";
            }
            $b_table=substr($b_table,0,strlen($b_table)-1);
            $string="<?php\r\n\$b_table=\"".$b_table."\";\r\n".$d_table."?>";
            $fileManager->create($configFile, $string);
            $this->jsonp('写入配置文件 ...');
        }else{
            include $configFile;
        }
        $btb=explode(",",$b_table);
        $count=count($btb);
        $t = intval(Lua::post('t'));
        $s = intval(Lua::post('s'));
        $p = intval(Lua::post('p'));
        $alltotal = intval(Lua::post('alltotal'));
        $fnum = intval(Lua::post('fnum'));
        $dumpsql = '';
        if($t>=$count){
            echo 'success';exit;
        }
        if (empty($s)){
            $num = Doo::db()->fetchRow("SHOW TABLE STATUS LIKE '".$btb[$t]."';");
            $num = $num['Rows'];
            $dumpsql .= "self::query(\"DROP TABLE IF EXISTS `".$btb[$t]."`;\");\r\n";
            Doo::db()->query("SET SQL_QUOTE_SHOW_CREATE=1");
            $r = Doo::db()->fetchRow("SHOW CREATE TABLE `".$btb[$t]."`;");
            $create=str_replace("\"","\\\"",$r['Create Table']);
            $dumpsql .= "self::create(\"".$create."\");\r\n";
        }else{
            $num = (int)$alltotal;
        }
        $fields = Doo::db()->fetchAll("SHOW FIELDS FROM `".$btb[$t]."`");
        if(empty($fnum)){  
            $field_num = count($fields);
        }else{
            $field_num = $fnum;
        }
        $b = 0;
        $list = Doo::db()->fetchAll("select * from `".$btb[$t]."` limit $s,$num");
        if ($list){
            foreach ($list as $v){
                $b=1;
                $s++;
                $dumpsql .= "self::query(\"replace into `".$btb[$t]."` values(";
                $first=1;
                for($i=0;$i<$field_num;$i++){
                    if(empty($first)){
                        $dumpsql.=',';
                    }else{
                        $first=0;
                    }
                    $_field_name = $fields[$i]['Field'];
                    if (!isset($v[$_field_name])){
                        $dumpsql.='NULL';
                    }else{
                        $dumpsql .= '\''.Lua::clean($v[$_field_name]).'\'';
                    }
                }
                $dumpsql.=");\");\r\n";
                if(strlen($dumpsql)>=2048*1024){
                    $p++;
                    $sfile = $path."/".$btb[$t]."_".$p.".php";
                    $fileManager->create($sfile, "<?php\r\n".$dumpsql."?>");
                    $this->jsonp('Table Name&nbsp;:&nbsp;<b>'.$btb[$t].'</b><br />Table&nbsp;:&nbsp;<b>'.($t+1).'/'.$count.'</b><br />Record&nbsp;:&nbsp;<b>'.$s.'/'.$num.'</b><br />备份一组数据成功，正在进入下一组．．．．．．', $s, $p, $t, $alltotal, $fnum);
                }
            }
        }
        if(empty($p)||$b==1){
            $p++;
            $sfile=$path."/".$btb[$t]."_".$p.".php";
            $fileManager->create($sfile, "<?php\r\n".$dumpsql."?>");
        }
        if(empty($p)){
            $p=0;
        }
        $text = $fileManager->readFileContents($configFile);
        $rep1="\$tb['".$btb[$t]."']=0;";
        $rep2="\$tb['".$btb[$t]."']=".$p.";";
        $text=str_replace($rep1,$rep2,$text);
        $fileManager->create($configFile, $text);
        $t++;
        $this->jsonp('备份'.$btb[$t-1].'表成功，正在进入下一个表备份．．．．．．', 0, 0, $t, 0, 0);
    }    
    
    private function jsonb($info, $dir, $p, $t){
        echo json_encode(array('info'=>$info, 'dir'=>$dir, 'p'=>$p, 't'=>$t));
        exit;
    }
    
    private function jsonp($info,$s = 0,$p = 0,$t = 0,$alltotal = 0,$fnum = 0){
        echo json_encode(array('s'=>$s, 'info'=>$info, 'p'=>$p, 't'=>$t, 'alltotal'=>$alltotal, 'fnum'=>$fnum));
        exit;
    }
    
    static function query($query){
        Doo::db()->query($query);
    }
    
    static function create($query){
        $type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU","\\2",$query));
        $type = in_array($type,array('MYISAM','HEAP'))?$type:'MYISAM';
        $query = preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU","\\1",$query)." ENGINE=$type DEFAULT CHARSET=utf8";
        self::query($query);
    }
}