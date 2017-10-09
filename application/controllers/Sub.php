<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SubController extends AbstractController {
    
    public  $server = 'http://vas.vietteltelecom.vn/MPS/';
    private $pwd_prefix = 'wap_';

    // 订阅地址 viettel
    public function indexAction(){
        $svid = $this->input->get('svid',false);
        $sessionid = $this->input->get('sessionid',false);
        $msisdn = $this->input->get('msisdn',false);
        if($svid){
            $_subscribe = new SubscribeModel();
            $subscribe =  $_subscribe->checkSvid($svid);  // 获取订阅信息
            if($subscribe){
                $charge_url = $this->getReg($subscribe,$msisdn,$sessionid);
                //设置缓存
                $key_http_referer = 'HTTP_REFERER_SUB';
                if($this->getRequest()->isGet()){
                     $server = $this->getRequest()->getServer();
                    $HTTP_REFERER = $server['HTTP_REFERER'];
                    $this->session->setFlash($key_http_referer, $HTTP_REFERER);  // 闪存
                }
                jump($charge_url);// 执行跳转
            }
        }else{
            die(json_encode(array('status'=>404,'content'=>'svid parameter error'))); 
        }
        return false;
    }
    
    
    // mobifone 订阅地址
    public function mbAction(){
        $svid = $this->input->get('svid',false);
        $sessionid = $this->input->get('sessionid',false);
        $msisdn = $this->input->get('msisdn',false);
        if($svid){
            if($msisdn){
                $_subscribe = new SubscribeModel();
                $subscribe =  $_subscribe->checkSvid($svid);  // 获取订阅信息
                if($subscribe){
                    $charge_url = $this->getMbReg($subscribe,$msisdn,$sessionid);
                    jump($charge_url);// 执行跳转
                }else
                    die(json_encode(array('status'=>404,'content'=>'svid parameter error'))); 
            }else
                die(json_encode(array('status'=>404,'content'=>'msisdn parameter error')));  
        }else
            die(json_encode(array('status'=>404,'content'=>'svid parameter error'))); 
        return false;
    }
    
    
    public function vbAction(){
        $svid = $this->input->get('svid',false);
        $sessionid = $this->input->get('sessionid',false);
        $msisdn = $this->input->get('msisdn',false);
        if($svid){
            $_subscribe = new SubscribeModel();
            $subscribe =  $_subscribe->checkSvid($svid);  // 获取订阅信息
            if($subscribe){
                $charge_url = $this->getVbReg($subscribe,$msisdn,$sessionid);
                jump($charge_url);// 执行跳转
            }else
                die(json_encode(array('status'=>404,'content'=>'svid parameter error'))); 
        }else
            die(json_encode(array('status'=>404,'content'=>'svid parameter error'))); 
        return false;
    }
    
    // 获取vinaphone
    public function  getVbReg($subscribe,$msisdn,$sessionid=''){
        
        //$requestid = str_shuffle('123456789'); // 不得随意修改
        if($subscribe['vnpcpid']){
            $time = date('YmdHis');
            if($sessionid){
                $sessionid = '#'.$sessionid;
            }
            $res = $subscribe;
            $svid = $subscribe['svid'];
            $requestid = $svid.'@'.str_shuffle('12345').$msisdn.$sessionid; // 不得随意修改
            //$path = 'http://dk.vinaphone.com.vn/reg.jsp?';
            $path = 'http://dk1.vinaphone.com.vn/reg.jsp?';
            $str = 'requestid='.$requestid;
            $str .= '&returnurl='.$res['returnurl'];
            $str .= '&backurl='.$res['backurl'];  //在数据库中配置回调地址
            $str .= '&cp='.$res['vnpcpid'];
            $str .= '&service='.$res['vnpservice'];
            $str .= '&package='.$res['vnppackage'];
            $str .= '&requestdatetime='.$time; //yyyymmddhhmmss 
            $str .= '&channel=WAP'; //[web/wap/client]

            $path .= $str; 
            $securecode = md5($requestid.$res['returnurl'].$res['backurl'].$res['vnpcpid'].$res['vnpservice'].$res['vnppackage'].$time.'BLUEMOBILE@2016');
            $path .= '&securecode='.$securecode;
            
            Log_Log::info(__METHOD__.' content init mobifone reg:' . $path, true, true);  // 记录日志
        }
        return isset($path) ? $path :false;
    }




    // 获取URL   mobifone
    public function getMbReg($subscribe,$msisdn,$sessionid=''){
        if($msisdn){
            //print_r($res);exit;
            $subserviceid 	= trim($subscribe['short_key']); //42 (daily package)	43 (weekly package)
            $content 		= trim($subscribe['short_name']);
            $price 			= trim($subscribe['price']);

            //$itemname = ''; //此参数为空，url中直接拼接
            //$categoryname = 'g';//此参数为空，url中直接拼接
            $subcpname = 'BL';//固定值
            // cpcode
            $cpCode = secretConfig('mobifone_cpCode');
            $key    = secretConfig('mobifone_key');
            //$cprequestid = !empty($cprequestid) ? $cprequestid : date('YmdHis').str_shuffle('123');//为自动获取手机号码的请求ID 便于查询
            $svid = $subscribe['svid'];
            if($svid) $svid = $svid.'@';
            if($sessionid) $sessionid = '#'.$sessionid;
            $cprequestid = $svid.date('YmdHis').str_shuffle('123456').$sessionid;
            $mobile = trim($msisdn);
            // $mobile = '84934478489';
            // $mobile = '84934596177';

            //去掉国家码或国家码84替换成0
            if(substr($mobile,0,2) == '84'){
                $mobile = str_replace('84','0',$mobile);
            }else{
                $mobile = substr($mobile,2);
            }

            //$patameterStr = "subserviceid=42&categoryname=&itemname=&subcpname=BL&content=bd&cprequestid=323160219102839787&mobile=84934478489&price=2000"; //测试案例
            $patameterStr = "subserviceid={$subserviceid}&categoryname=&itemname=&subcpname={$subcpname}&content={$content}&cprequestid={$cprequestid}&mobile={$mobile}&price=".$price;
            //echo $patameterStr;exit;
            //加密过程
            $obj = new Util_Encryption(); //实例化加密类
            $encryRes = $obj->encode($patameterStr);
            $dataStr = 'data='.$encryRes.'&key='.$key;

            //加密后的数据
            $data = $obj->encryptData($dataStr);

            //签名字符串
            $signature = $obj->createSignature($data);

            $serviceid = $subserviceid;  //serviceid、subserviceid 两个参数是相同的
            // 请求地址 
            $url = "http://m.mgame.vn/paymentgw/index.php?r=pDefault/index&cpid={$cpCode}&cmd=REGISTER&serviceid={$serviceid}&data={$data}&signature=".$signature;
            Log_Log::info(__METHOD__.' content init mobifone reg:' . $url, true, true);  // 记录日志
        }
        return isset($url) ? $url :false;
    }
    

    // 获取URL   viettel
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
            
            Log_Log::info(__METHOD__.' content init viettel reg:' . $url_charge, true, true);  // 记录日志
            
        }
        return isset($url_charge) ? $url_charge :false;
    }
    
    
    //管理员登录
    public function loginAction(){
        
        $key_http_referer = 'HTTP_REFERER';
        if($this->getRequest()->isGet()){
             $server = $this->getRequest()->getServer();
            $HTTP_REFERER = $server['HTTP_REFERER'];
            $this->session->setFlash($key_http_referer, $HTTP_REFERER);  // 闪存
        }

        //执行登录操作
        if($this->input->getMethod() == 'POST'){
           $post = $this->getRequest()->getPost();
           $username = trim($post['username']);
           $pwd = trim($post['password']);
           if(filter_var($username, FILTER_VALIDATE_EMAIL)){
               $_User = new UsersModel();
               $user_data = $_User->checkMail($username);
               $password = $user_data['password'];
               $db_password = md5($this->pwd_prefix.$username.$pwd);
               if($password == $db_password){
                   //登录成功设置标记
                    $this->session->set(systemConfig('UserLogin'),1);  // 设置已经订阅标识
                    $this->session->set(systemConfig('AppsLogin'),$user_data); // 登录用户信息
                    $REQUEST_URI = $this->session->getFlash($key_http_referer);
                    jump($REQUEST_URI);
               }
           }
        }
    }
    
    //生成管理员操作
    public function  makeAction(){
        //手动添加用户信息 /sub/make?u=lastchiliarch@163.com&p=qwer@123
        $u = $this->input->getUsername('u');
        $_User = new UsersModel();
        if(filter_var($u, FILTER_VALIDATE_EMAIL)){
            $query = $this->getRequest()->getQuery();
            $p = $query['p'];
            if($p && $u){
                if(!$_User->checkMail($u)){
                    $create_data = [ 'email'=>$u, 'password'=>md5($this->pwd_prefix.$u.$p)];
                    if($_User->createUser($create_data)){
                        $error = $this->error('200','Creating a successful'); 
                    }
                }else
                    $error = $this->error('501','The user already exists'); 
            }else
                $error = $this->error('400','Enter the correct password');
        }else
            $error = $this->error('400','Enter the correct mailbox');
        echo format_json($error);
        return false;
    }
    
    //系统用户退出登录系统
    public function logoutAction(){
        $this->session->delete(systemConfig('UserLogin'));
        $this->session->delete(systemConfig('AppsLogin'));
        $server = $this->getRequest()->getServer();
        $REQUEST_URI = $server['HTTP_REFERER'];
        jump($REQUEST_URI);
    }
    
     //在没有探测到运营商情况下选择营运商并设置
     public function  settelcoAction(){
        $post = $this->getRequest()->getPost();
        if($post){
            $msisdn = $post['msisdn'];
            $telco = $post['telco'];
            $password = $post['password'];
            if($msisdn && $telco && $password){
                //设置运营并登录
                $subscribe = new SubscribeModel();
                $subs = $subscribe->userLogin($this->site,$msisdn,$password,$telco);
                if($subs){
                    $IsLogin = systemConfig('IsLogin');
                    $this->session->set($IsLogin,1);  // 设置已经订阅标识
                    $out = ['status'=>200,'content'=>'Congratulations on your successful login'];
                }else{
                    $out = ['status'=>401,'content'=>'The phone format or password is incorrect'];
                } 
            }
        }else{
            $out = ['status'=>400,'content'=>'parameter error'];
        } 
        echo json_encode($out);return false;
     }
    
}
