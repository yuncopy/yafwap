<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class UsersModel extends BlueModel
{
    protected  $table  = 'users';  // 表名
    protected $write_mode = true;  // 使用读写数据库
    // 菜单
    public function menusList($site){
        //获取数据
       if((self::$menus == null) && $site){
            $datas = $this->select($this->table, ["id","name","pid","icon","url","class","sort"],['site'=>$site,'states'=>1,'ORDER'=>'sort']);
            self::$menus = $datas;
        }
        return self::$menus;
    }
    
    //检查邮箱是否存在
    public function checkMail($mail){
        if($mail){
            $boolval_user = $this->get( $this->table, [
                "id","email","password"
            ], [
                "email" => $mail
            ]);
        }
        return !empty($boolval_user) ? $boolval_user : null;
    }
    
    // 执行添加用户
    public function  createUser($create_data){
        if($create_data){
            $last_user_id = $this->insert($this->table, $create_data);
        }
        return !empty($last_user_id) ? $last_user_id : null;
    }
    
}

