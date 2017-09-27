<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class ShowController extends AbstractController {
    
    // 网站介绍
    public function indexAction(){
        
        $p = $this->getRequest()->getQuery("p", 1);
        $c = $this->getRequest()->getQuery("c");
        $n = $this->getRequest()->getQuery("n",18);
	if($p <= 0) $p = 1;
        $_contents = new ContentsModel();
        $page_content = $_contents->getCategory($c,$p,$n);
        $this->assign($page_content);
        $category_top = $_contents->categoryTop($c,10);
        $this->assign($category_top);
    }
}
