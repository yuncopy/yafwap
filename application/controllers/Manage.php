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
    
    // 文件上传 图片文件
    public function uploadAction(){
        
        if($_FILES){
            $this->ini_set_file();
            // 文件上传
            $upload = new Upload_Upload(); // 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     '/home/copy/resource/'; // 设置附件上传根目录，临时设置上传目录  home/copy/resource
            // 上传文件 
            $info   =   $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                //dd($upload->getError());
            }else{// 上传成功
                // 上传成功后移动文件，到项目目录下


                dd('上传成功！');
            }
        }
        
    }
    
    public function ini_set_file(){
        //HTTP上传文件的开关，默认为ON即是开  
        ini_set('file_uploads','ON');
        //通过POST、GET以及PUT方式接收数据时间进行限制为90秒 默认值：60  
        ini_set('max_input_time','90');
        //脚本执行时间就由默认的30秒变为180秒  
        ini_set('max_execution_time', '180');
        //Post变量由2M修改为8M，此值改为比upload_max_filesize要大  
        ini_set('post_max_size', '60M');
        //上传文件修改也为8M，和上面这个有点关系，大小不等的关系。
        ini_set('upload_max_filesize','20M');  
        //正在运行的脚本大量使用系统可用内存,上传图片给多点，最好比post_max_size大1.5倍  
        ini_set('memory_limit','120M');
    }

        public function uploadseedAction(){
            $this->ini_set_file();
             // 文件上传
             $upload = new Upload_Upload(); // 实例化上传类
             $upload->maxSize   =     314572800 ;// 设置附件上传大小
             $upload->exts      =     array('apk');// 设置附件上传类型
             $upload->rootPath  =     '/home/copy/resource/'; // 设置附件上传根目录，临时设置上传目录
             // 上传文件 
             $info   =   $upload->upload();

             if(!$info) {// 上传错误提示错误信息
                 dd($upload->getError());
             }else{// 上传成功
                 // 上传成功后移动文件，到项目目录下

                 dd($info);
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
        $upload->rootPath  =     '/home/copy/resource/'; // 设置附件上传根目录，临时设置上传目录
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
