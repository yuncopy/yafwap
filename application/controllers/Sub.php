<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class SubController extends AbstractController {
    
    public  $server = 'http://vas.vietteltelecom.vn/MPS/';
    
    public function indexAction(){
        $svid = $this->input->get('svid',false);
        $sessionid = $this->input->get('sessionid',false);
        $msisdn = $this->input->get('msisdn',false);
        if($svid){
            $_subscribe = new SubscribeModel();
            $subscribe =  $_subscribe->checkSvid($svid);  // 获取订阅信息
            if($subscribe){
                $charge_url = $this->getReg($subscribe,$msisdn,$sessionid);
                jump($charge_url);// 执行跳转
            }
        }else{
            die(json_encode(array('status'=>404,'content'=>'parameter error'))); 
        }
        return false;
    }
    
    // 获取URL
    public function  getReg($subscribe,$msisdn,$sessionid=''){
        
        if($msisdn){
            $sub_str = $subscribe['short_key'];
            $pos = strpos ( $sub_str ,  '@' ); //获取问号位置
            if ( $pos  ===  false ) {
                $sub = $sub_str ;
            }else{
                $sub = mb_substr($sub_str, 0, $pos, 'UTF-8'); //截取@前的参数
            }
            $cont = $sub_cp = $item = $cate = '';
            $price = $subscribe['price'];
            $shuffle_str = '@'.date('YmdHis').str_shuffle('123');
            if($sessionid){
                $shuffle_str = $sessionid.'@'.date('YmdHis').str_shuffle('123');  //sessionid参数处理
            }
            $svid = $subscribe['svid'];
            $req = $svid.'@'.$shuffle_str;
            $data = "SUB={$sub}&CATE={$cate}&ITEM={$item}&SUB_CP={$sub_cp}&CONT={$cont}&PRICE={$price}&REQ={$req}&MOBILE={$msisdn}&SOURCE=WAP";
            $data = pkcs5_pad($data, 16);
            //B1. Ma hoa du lieu bang AES
              // 密钥
            $z = secretConfig('key');
            $pub_key = secretConfig('pub_key');
            $pri_key_cp = secretConfig('pri_key_cp');
            $pub_key_cp = secretConfig('pub_key_cp');
            $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
            $aes = new Util_Viettelaes($z, 'CBC', $iv);
            $encrypted = $aes->encrypt();     
            $aeskey = bin2hex($encrypted);
            
            
            $value_encrypt_aes = encrypt($data,$aeskey);
            $value_with_key = 'value='.$value_encrypt_aes.'&key='.$aeskey;
            openssl_public_encrypt($value_with_key,$data_encrypted,$pub_key);
            $data_encrypted = base64_encode($data_encrypted);
            $signature ='';
            openssl_sign($data_encrypted, $signature, $pri_key_cp, OPENSSL_ALGO_SHA1);
            $signature = base64_encode($signature);
            $signature = urlencode($signature);
            $verify = openssl_verify (($data_encrypted) , base64_decode(urldecode($signature)) , $pub_key_cp, OPENSSL_ALGO_SHA1);
            openssl_private_decrypt(($data_encrypted),$data_decrypted,$pri_key_cp);
            $value_decrypt = decrypt($value_encrypt_aes,$aeskey);
            
            // 跳转URL
            $url_charge = $this->server."charge.html?PRO=BLUEMOBILE&CMD=REGISTER&SER=FUNVIDEO&SUB=".$sub."&DATA=".urlencode( $data_encrypted).'&SIG='.$signature;
       }
       return isset($url_charge) ? $url_charge :false;
    }
    
    
    
    
    
    
}
