<?php
Doo::loadController('__auth');

class __home extends __auth{

    /*
     * 后台首页
     */
    public function index(){
        if ($this->user['perm'] != SUPER_MAN && SYSNAME != $this->user['channel']){
            header("Location:/".$this->user['channel'].'/admin/');
            exit;
        }
        $list = Lua::get_more("select * from lua_channel where status='1'");
        $cssname = 'nav_list';
        if (SYSNAME != ADMIN_ROOT){
            $session = Doo::session('Lua');   
            $change_id = $session->get('change_id');
            $set = Lua::get('set');
            if ($set){
                $session->change_id = empty($change_id) || $change_id == 0 ? 1 : 0 ;
                header("Location:/".SYSNAME."/".ADMIN_ROOT."/");
                exit;
            }
            $set_id = $change_id || $change_id == 1 ? 1: 0;
            if ($set_id == 0){
                Doo::cache('php')->hashing = false;
                $tree = Doo::cache('php')->get('category');
                $cssname = 'tree_list';
                $html = '';
                if ($tree){
                    $html = $this->_tree($tree, 0);
                }
            }
        }
    	include Lua::display('frame', $this->dir);
    }
    
    /*
     * 栏目树形递归
     */
    private function _tree($tree, $upid = 0, $level = 0, $blank = ''){
        $html = "";
        foreach ($tree as $id=>$cate){
            if ($cate['upid'] == $upid){
                if ($cate['add_perm'] == 0){
                    $html .= '<li>'.str_repeat($blank,$level).'<img src="'.$this->img.'img/botton/File.png" align="absmiddle"/> <b>'.$cate['name'].'</b></li>';
                }else{
                    $html .= '<li>'.str_repeat($blank,$level).'<img src="'.$this->img.'img/botton/ie.png" align="absmiddle"/> <a href="./content.htm?catid='.$cate['id'].'" target="main">'.$cate['name'].'</a></li>';
                }
                $html .= $this->_tree($tree, $cate['id'], $level + 1, '&nbsp;&nbsp;&nbsp;&nbsp;');
            }
        }
        return $html;
    }

    /*
     * 系统信息
     */
    public function info(){
        $ip = $this->clientIP();
        $my = Doo::db()->fetchRow("select VERSION()",null,PDO::FETCH_COLUMN);
        include Lua::display('info', $this->dir);
    }

}