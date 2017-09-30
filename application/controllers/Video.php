<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class VideoController extends AbstractController {
    
    public  $movies  = [5,6,7];// 类别
    public  $sport = [9,10,11,12,14];  // 类别
    public  $contents = null;


    // movies
    public function indexAction(){
        $p = $this->getRequest()->getQuery("p", 1);
        $c = $this->getRequest()->getQuery("c");
        $n = $this->getRequest()->getQuery("n",4);
        $category = $this->movies;
        $page_content = $this->categoryContent($category,$p,$c,$n);
        $this->assign($page_content);
        $group_title = array_column($this->menus(true),'name','id');
        $this->assign(array('group_menus'=>$group_title));
        $category_top = $this->contents->categoryTop($category,10); // 排序
        $this->assign($category_top);
    }
    
    // sport
    public function sportAction(){
        $p = $this->getRequest()->getQuery("p", 1);
        $c = $this->getRequest()->getQuery("c");
        $n = $this->getRequest()->getQuery("n",6);
        $sport = $this->sport;
        $page_content = $this->categoryContent($sport,$p,$c,$n);
        $this->assign($page_content);
        $group_title = array_column($this->menus(true),'name','id');
        $this->assign(array('group_menus'=>$group_title));
    }
    
    //分类内容
    public function categoryContent($category,$p,$c,$n){
        if($category && $p && $c && $n){
            if($p <= 0) $p = 1;
            $_contents = new ContentsModel();
            $this->contents = $_contents;
            foreach ($category as $c){
                $cate_content = $_contents->getCategory($c,$p,$n,true,true);
                $contents[$c] = $cate_content['contents'];
                $max[] = $cate_content['tatolpage'];
            }
            //dd($contents);
            $pos = array_search(max($max),$max); // 最大值
            $tatolpage = $max[$pos];
            $str = $_contents->pageInfo($p,$c,$n,$tatolpage);
            $page_content = ['contents'=>$contents,'page'=>$str];
            return $page_content;
        }
    }
    
    //本使用条款
    public function useAction(){
        
       $post =  $this->getRequest()->isPost();
       $contents = new ContentsModel();
       if($post){
           $post_data = $this->getRequest()->getPost();
           $text = $post_data['content'];
           $cid = $post_data['cid'];
           $affect_number = $contents->saveGetContent(['cid'=>$cid],['text'=>$text]);
           if($affect_number){
               $out = ['status'=>200,'data'=>$affect_number,'content'=>'successful'];
           }else{
               $out = ['status'=>400,'content'=>'unsuccessful'];
           }
           die(json_encode($out));
       }else{
           $cid = $this->input->get('c');
           $cont = $contents->saveGetContent(['cid'=>$cid,'id[=]'=>16]);
           $this->assign(array('use'=>$cont['text']));
       }
    }
    
    // 客服热血
    public function serviceAction(){
        //Yaf_Dispatcher::getInstance()->autoRender(false); //关闭视图渲染 
        //$this->display('video/service');
    }
    
    // 指导
    public function guideAction(){
        $post =  $this->getRequest()->isPost();
       $contents = new ContentsModel();
       if($post){
           $post_data = $this->getRequest()->getPost();
           $content = $post_data['content'];
           $cid = $post_data['cid'];
           $affect_number = $contents->saveGetContent(['cid'=>$cid],['text'=>$content]);
           if($affect_number){
               $out = ['status'=>200,'data'=>$affect_number,'content'=>'successful'];
           }else{
               $out = ['status'=>400,'content'=>'unsuccessful'];
           }
           die(json_encode($out));
       }else{
           $cid = $this->input->get('c');
           $cont = $contents->saveGetContent(['cid'=>$cid]);
           $this->assign(array('use'=>$cont['text']));
       }
        
    }
    
    
    
    
    
    
    
    
    
}
