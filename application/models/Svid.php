<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SvidModel extends BlueModel
{
    protected  $table  = 'svids';  // 表名
    protected $write_mode = true;  // 使用读写数据库
    public static $svids = null;
    // 业务ID信息
    public function getSvids($site,$teclo){
        //获取数据
       if((self::$svids == null) && $site){
            $datas = $this->select($this->table,"*",['site'=>$site,'telco'=>$teclo]);
            self::$svids = $datas;
        }
        return self::$svids;
    }
    
    //获取SVID
    public function getSvidInfo($svid){
        if($svid){
            $svidInfo = $this->get($this->table, "*", [
                "svid" => $svid
            ]);
            return $svidInfo;
        }
    }
    
}

