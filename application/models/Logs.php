<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class LogsModel extends BlueModel
{
    protected  $table  = 'logs';  // 表名
    protected $write_mode = true;  // 使用读写数据库

    // 菜单
    public function addLog($action,$content){
        //获取数据
       if($action && $content){
            $insert_data = array();
            $insert_data['action'] = $action;
            if(is_array($content)){
                $content = json_encode($content);
            }
            $insert_data['ip'] = getRealIp();
            $insert_data['content'] = $content;
            if($insert_data) return $this->insert($this->table,$insert_data);
        }
        return false;
    }
    
    public function demo(){
        $this->update($this->table,['action'=>'1112353251'],['id'=>2]);
    }

        //获取表字段
    public function columnsTab(){
        $sql = "SHOW COLUMNS FROM {$this->table}";
        $columns = $this->query($sql)->fetchAll();
        return array_column($columns, 'Field');
    }
    
}

