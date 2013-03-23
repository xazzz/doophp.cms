<?php
Doo::loadController('__auth');

class __file extends __auth{

    /*
     * 入口
     */
    public function index(){
        $action = Lua::get_post('action');
        $action = $action ? $action : 'home';
        $rs = $this->acl()->process($this->user['perm'], '__file', $action);
        if ($rs){
            return $rs;            
        }
        if (method_exists($this, $action)){
            $this->$action();
        }else{
            Lua::e404();
        }
    }
    
    private function home(){}
    
    /*
     * 单一图片上传
     */
    private function upimage(){
        echo Lua::upload($this->user);
    }
    
    /*
     * 上传单一图片并生成200的缩略图
     */
    private function up200(){
        echo Lua::upload($this->user, 'Filedata', array('200','150'));
    }
    
    /*
     * 百度编辑器上传图片
     */
    private function ueditor(){
        $filename = Lua::upload($this->user, 'upfile', array(), 0);
        if (strstr($filename,'1@')){
            $filename = '/'.SYSNAME.'/'.str_replace('1@', '', $filename);
            echo "{'url':'".$filename."','title':'','original':'','state':'SUCCESS'}";
        }else{
            echo "{'url':'".$filename."','title':'','original':'','state':'请上传jpg图片'}";
        }
    }
    
    /*
     * 编辑器上传图片
     */
    private function uploadEditorImage(){
        $filename = Lua::upload($this->user, 'file', array(), 0);
        if (strstr($filename,'1@')){
            $filename = '/'.SYSNAME.'/'.str_replace('1@', '', $filename);
            echo stripslashes(json_encode(array('filelink'=>$filename)));
        }
    }

}
