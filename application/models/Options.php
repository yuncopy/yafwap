<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class OptionsModel extends BlueModel
{
    protected   $table  = 'options';  // 表名
    protected   $write_mode = true;  // 使用读写数据库
    static $options = null;


    //检查是否存在业务ID
    public function getOptions($site=1){
        if(self::$options == null){
            $options_info = $this->select( $this->table, [
                "id","name","value"
            ], [
                "site" => $site
            ]);
            $options = array_column($options_info, 'value' ,  'name' );
            self::$options = $options;
        }
        return !empty(self::$options) ? self::$options : false;
    }
}

