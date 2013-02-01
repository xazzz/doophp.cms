<?php
Doo::loadController('__auth');

class __plugin extends __auth{
    
    public $plugin_dir = '';

    /*
     * 入口
     */
    public function index(){
        $this->plugin_dir = PROJECT_ROOT.Doo::conf()->PROTECTED_FOLDER.'plugin/';
        $action = Lua::get_post('action');
        $action = $action ? $action : 'home';
        if (method_exists($this, $action)){
            $this->$action();
        }else{
            $file = $this->plugin_dir.$action.'/admin.php';
            if (!file_exists($file)){
                Lua::admin_msg('提示信息', '你访问的插件不存在');
            }
            require_once $file;
            $oo = new $action();
            $oo->_set($this->plugin_dir, $this->dir, $this->img, $this->user);
            $c = Lua::get_post('c');
            $c = $c ? '_'.$c : '_home';
            if (method_exists($oo, $c)){
                $oo->$c();
            }else{
                Lua::e404();
            }
        }
    }
    
    /*
     * 已安装插件列表
     */
    private function home(){
        Doo::loadHelper('DooFile');
        $f = new DooFile();
        $list = $f->getList($this->plugin_dir, DooFile::LIST_FOLDER);
        $ps = array();
        if ($list){
            foreach ($list as $k=>$v){
                $rs['name'] = $f->readFileContents($v['path'].'/readme.txt');
                $rs['ico'] = '/'.ADMIN_ROOT.'/'.Doo::conf()->PROTECTED_FOLDER.'plugin/'.$v['name'].'/ico.png';
                $rs['act'] = $v['name'];
                $ps[$v['name']] = $rs;
            }
        }
        include Lua::display('plugin', $this->dir);
    }
    
    /*
     * 插件市场
     */
    private function market(){
        include Lua::display('plugin_market', $this->dir);
    }

}