<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SubscribeModel extends BlueModel
{
    protected   $table  = 'tb_subscribe_info';  // 表名
    protected   $table_user = array(
        'viettel'=>'tb_subscribe_user',
        'mobifone'=>'tb_subscribe_user',
        'vinaphone'=>'tb_subscribe_user_vinaphone'
    );  

   

    //检查是否存在业务ID
    public function checkSvid($svid){
        if($svid){
            $subscribe_info = $this->get( $this->table, [
                "id","svid","short_code","short_key","price"
            ], [
                "svid" => $svid
            ]);
            //dd($this->error());
            return !empty($subscribe_info) ? $subscribe_info : false;
        }
    }
    
    //检查MT用户
    public function loginMt($site,$telco,$msisdn){
        if($site && $telco && $msisdn){
            $svid = new SvidModel();
            $svids_data = $svid->getSvids($site,$telco);
            $svids = array_column($svids_data, 'svid');
            $table = $this->table_user[$telco];
            $t_svids = $this->get($table,"*",['status'=>'A','svid'=>$svids,'msisdn'=>$msisdn]);
            return !empty($t_svids) ? $t_svids : false;
        }
    }
    
    // 验证登录是否成功
    public function userLogin($site,$msisdn,$password,$telco){
        if($msisdn && $password && $telco){
            $svid = new SvidModel();
            $svids_data = $svid->getSvids($site,$telco);
            $svids = array_column($svids_data, 'svid');// 获取SVID信息
            $table = $this->table_user[$telco];
            $t_svids = $this->get($table,"*",['status'=>'A','svid'=>$svids,'msisdn'=>$msisdn]);// 获取业务信息
            $message_id = $t_svids['message_id'];
            if($message_id == $password){
                return true;
            }
        }
        return false;
    }
    
}

