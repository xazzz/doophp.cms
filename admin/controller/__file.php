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
        echo $this->_upload();
    }
    
    /*
     * 上传单一图片并生成200的缩略图
     */
    private function up200(){
        echo $this->_upload('Filedata', array('200','150'));
    }
    
    /*
     * 编辑器上传图片
     */
    private function uploadEditorImage(){
        $filename = $this->_upload('file',array(),1);
        if (strstr($filename,'1@')){
            $filename = '/'.SYSNAME.'/'.str_replace('1@', '', $filename);
            echo stripslashes(json_encode(array('filelink'=>$filename)));
        }
    }
    
    /*
     * 图片上传
     */
    private function _upload($form = 'Filedata', $thumb = array(), $waterit = 0){
        Doo::loadHelper('DooGdImage');
        $file_path = "files/".date('Y')."/".date('m')."/";
        $upload_path = PROJECT_ROOT.$file_path;
        $GD = new DooGdImage($upload_path, $upload_path);
        if ($GD->checkImageExtension($form)){
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
            return "1@".$file_path.$filename;
        }
        return "你上传的文件非图片格式";
    }

}