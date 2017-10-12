<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class MenusModel extends BlueModel
{
    protected  $table  = 'menus';  // 表名
    protected $write_mode = true;  // 使用读写数据库
    static $menus = null;
    static $games = null;

    // 菜单
    public function menusList($site){
        //获取数据
       if((self::$menus == null) && $site){
            $datas = $this->select($this->table, ["id","name","pid","icon","url","class","exp","sort"],['site'=>$site,'states'=>1,'ORDER'=>'sort']);
            self::$menus = $datas;
        }
        return self::$menus;
    }
    
    //获取游戏分类
    public function  getGames($site){
        if($site){
            $datas = $this->select($this->table, 
                ["id","name","pid","icon","url","class","sort"],
                ['site'=>$site,'states'=>1,'pid[!]'=>0,'ORDER'=>'sort']
            );
            self::$games = $datas;
        }
        return self::$games;
    }
    
}

