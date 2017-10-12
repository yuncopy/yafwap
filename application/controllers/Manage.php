<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class ManageController extends AbstractController {
    
    // 网站介绍
    public function indexAction(){
        
        $_menus = new MenusModel();
        $menus = $_menus->menusList($this->site);
        $option = make_option_tree_for_select($menus);
        $this->assign(array('option'=>$option));
    }
    
    // 文件上传
    public function uploadAction(){
        
        //dd($_FILES);
        
        // 文件上传
        $upload = new Upload_Upload(); // 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     '/home/copy/resource/'; // 设置附件上传根目录
        // 上传文件 
        $info   =   $upload->upload();
        
        
        // 图片处理
        $image = new Upload_Image(); 
        $image->open('/home/copy/resource/20171012/59df200793c4f.jpg');
        // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
        $image->thumb(150, 150)->save('/home/copy/resource/20171012/thumb.jpg');
        
        if(!$info) {// 上传错误提示错误信息
            //dd($upload->getError());
        }else{// 上传成功
            dd('上传成功！');
        }
        
    }
    
     // DEMO  http://document.thinkphp.cn/manual_3_2.html#image
     public function demoAction(){
        
        //dd($_FILES);
         
        // 文件上传
        $upload = new Upload_Upload(); // 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     '/home/copy/resource/'; // 设置附件上传根目录
        // 上传文件 
        $info   =   $upload->upload();
        
        
        // 图片处理
        $image = new Upload_Image(); 
        $image->open('/home/copy/resource/20171012/59df200793c4f.jpg');
        // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
        $image->thumb(150, 150)->save('/home/copy/resource/20171012/thumb.jpg');
        
        if(!$info) {// 上传错误提示错误信息
            //dd($upload->getError());
        }else{// 上传成功
            dd('上传成功！');
        }
        
    }
    
    
    
}
