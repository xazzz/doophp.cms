<?php
Doo::loadController('__auth');

class __log extends __auth{

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
    
    private function home(){
        $url = './log.htm';
        $and = '';
        $startday = Lua::get('startday');
        $endday = Lua::get('endday');
        $key = Lua::get('key');
        $show = Lua::get('show');
        if ($startday){
            $startday1 = date('Y-m-d 00:00:00', strtotime($startday));
        }
        if ($endday){
            $endday1 = date('Y-m-d 23:59:59', strtotime($endday));
        }
        if ($startday && $endday && $endday1 >= $startday1){
            $and .= " and dateline >= '$startday1' and dateline <= '$endday1' ";
        }elseif ($startday){
            $and .= " and dateline >= '$startday1' ";
        }elseif ($endday){
            $and .= " and dateline <= '$endday1' ";
        }
        if ($show && $key){
            switch ($show){
                case 1: $and .= " and username like binary '%$key%' ";break;
                case 2: $and .= " and ip like binary '%$key%' ";break;
                case 3: $and .= " and actionname like binary '%$key%' ";break;
            }
        }
        if ($and){
            $url = "./log.htm?startday=$startday&endday=$endday&key=$key&show=$show";
        }
        $count = Doo::db()->count("select count(*) from lua_logs where id>0 $and");
        $tpp = 20;
        $limit = (($this->page - 1) * $tpp).','.$tpp;
        $list = Lua::get_more("select * from lua_logs where id>0 $and order by id desc limit ".$limit);
        $page = Lua::page($url, $this->page, $count, $tpp);
        include Lua::display('log', $this->dir);
    }
    
    private function del(){
        $id = Lua::get('id');
        Lua::delete('lua_logs', array('id'=>$id));
        Lua::admin_msg('信息提示', '删除成功', './log.htm');
    }
    
    private function batch_del(){
        $value = Lua::post('values');
        if ($value){
            foreach ($value as $id){
                Lua::delete('lua_logs', array('id'=>$id));
            }
        }
        Lua::ajaxmessage('success', '删除成功', './log.htm');
    }
    
    private function deltime(){
        $startday = Lua::post('startday');
        $endday = Lua::post('endday');
        if ($startday){
            $startday1 = date('Y-m-d 00:00:00', strtotime($startday));
        }
        if ($endday){
            $endday1 = date('Y-m-d 23:59:59', strtotime($endday));
        }
        $and = '';
        if ($startday && $endday && $endday1 >= $startday1){
            $and .= " where dateline >= '$startday1' and dateline <= '$endday1' ";
        }elseif ($startday){
            $and .= " where dateline >= '$startday1' ";
        }elseif ($endday){
            $and .= " where dateline <= '$endday1' ";
        }
        if ($and){
            Doo::db()->query("delete from lua_logs $and");
            Lua::admin_msg('信息提示', '删除成功', './log.htm');
        }
        Lua::admin_msg('信息提示', '请选择时间段');
    }

}