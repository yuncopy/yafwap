<?php
/**
 * @name IndexController
 * @author copy
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends AbstractController {
    
    
    //入口
    protected $category = [ 11,12,5 ];
    protected  $server_video = 'http://vas.vietteltelecom.vn/MPS/';
    private $demo = true;  // 模拟测试  http://125.212.233.65:41801/index/index?svid=vnd998  测试地址
    
    public function indexAction(){
      
        $telcoName_Arr = self::$telco_arr;
        $telcoName = $telcoName_Arr['operator'];
        if($telcoName_Arr['status']== 200){  // 成功探测到运营商
            netType('3G');  // 设置网络方式
            switch ($telcoName){
                case 'viettel':  //http://125.212.233.65:41801/index/index?svid=vnd998
                        $data = $this->getRequest()->getQuery("DATA", false);  //$this->input->get('DATA',false);  会过滤特殊字符，不能使用
                        $svid = $this->input->get('svid','');
                        $sessionid = $this->input->get('sessionid','');
                        $data_encrypt = $this->aes_encrypt_encrypt_video($data,$svid,$sessionid); //探测手机号
                        $josn_to_data = json_decode($data_encrypt,true);
                        Log_Log::info(__METHOD__.' viettel init msisdn :' . $data_encrypt, true, true);  // 记录日志
                        if($josn_to_data['status']== 201){  // 加密跳转
                            $redirect_viettel  = $josn_to_data['redirect'];
                            if(!getmsisdn()){  // 探测手机号
                                jump($redirect_viettel);// 执行跳转
                            }
                        }else if($josn_to_data['status']== 200){  // 解密获取参数
                            //缓存数据
                            //dd($josn_to_data);
                             if($this->demo){  // 模拟测试数据
                                 $josn_to_data=[
                                    'CMD'   =>'MSISDN',
                                    'MOBILE'=>'167xxxx115',
                                    'SVID'  =>'vnd998',
                                    'REQ'   =>'vnd998@749518263#999999',
                                    'RES'   =>'204',
                                    'TELCO' => $telcoName
                                ];
                            }
                            $GetMsisdn = systemConfig('GetMsisdn');
                            $UserSub = systemConfig('UserSub');
                            $svid = strstr($josn_to_data['REQ'], '@', TRUE);
                            $josn_to_data['SVID'] = $svid;
                            $josn_to_data['TELCO'] = $telcoName;
                           
                            $this->session->set($UserSub,$josn_to_data);  // 设置整体缓存信息 
                            $CMD = $josn_to_data['CMD'];
                            $MOBILE = $josn_to_data['MOBILE'];
                            $RES = $josn_to_data['RES'];
                            switch ($CMD){
                                case 'MSISDN':  // 解析探测手机号
                                    if($MOBILE =='null' || $RES=='406'){  //获取不到手机号，则标识WIFI下订阅
                                        $msisdn = '';
                                        netType("WIFI"); // 设置网络方式
                                    }else if($RES == '204'){   // 成功获取 RES "RES":"204"
                                        $msisdn = $MOBILE;   // 手机格式 
                                    }
                                    $this->session->set($GetMsisdn,$msisdn);
                                break;
                                
                                case 'REGISTER':  // 订阅成功  
                                    $GetMsisdn = $josn_to_data['MOBILE'];
                                    $this->session->set($GetMsisdn,$msisdn);
                                    dd($data_encrypt);
                                    break;
                                case 'CANCEL':  // 取消订阅
                                    
                                    break;
                            }
                        }
                    break;
                case 'vinaphone':
                    
                    break;
                case 'mobifone':
                    
                    break;
            }
            
        }else{ // 默认无法区分，使用WIFI订阅
            netType("WIFI"); // 设置网络方式
        }
        
        // 检查用户是否登录
        $this->loginMsisdn();
        
        // 展示内容列表
        $content =  new ContentsModel(); 
        // 分类ID
        $where_cid = $this->category;
        //$group_contents = $content->getContents($where_cid);
        $group_contents = $this->categoryContent($where_cid,true,true,8); // 一排4个
        $slideshow = $content->getSlideshow([10]);  // 幻灯片
        $group_title = array_column($this->menus(true),'name','id');
        $this->assign(array('video'=>$content->videoTop3()));
        $this->assign(array('group'=>$group_contents));
        $this->assign(array('group_menus'=>$group_title));
        $this->assign(array('slideshow'=>$slideshow));
        
        
    }
    
    // 检查用户是否已经MT
    private function  loginMsisdn(){
        $Subscribe = new SubscribeModel();
        $telco = self::$telco_arr;
        $msisdn = getmsisdn();
        $msisdn ='966000306';
        $msisdn_sub = $Subscribe->loginMt($this->site,$telco['operator'],$msisdn);
        if($msisdn_sub){
            $IsLogin = systemConfig('IsLogin');
            $this->session->set($IsLogin,1);  // 设置已经订阅标识
        }
    }

    

    // 分类
    public function categoryContent($category,$p,$c,$n){
        if($category && $p && $c && $n){
            if($p <= 0) $p = 1;
            $_contents = new ContentsModel();
            foreach ($category as $c){
                $cate_content = $_contents->getCategory($c,$p,$n,true);
                $contents[$c] = $cate_content['contents'];
            }
            $row_num = 4; // 分成 2数组
            foreach ($contents as $kk => $vv){
               $chunk_array = array_chunk($vv,$row_num,true);//分割数组
               $new_content[$kk] =  array_map(function($value){
                    return array_map(function($vv){
                        return $vv[0];
                     },$value);
               },$chunk_array);
            }
            return $new_content;
        }
    }
    
    //观看视频
    public function  showAction(){
        
        
        
    }

    //请求探测手机号加密数据
    /**
    AES 加密  256位   视频业务加密、解密
    */
    public function aes_encrypt_encrypt_video($data=null,$svid='',$sessionid=''){
        $z = secretConfig('key');
        $pub_key = secretConfig('pub_key');
        $pri_key_cp = secretConfig('pri_key_cp');
        $pub_key_cp = secretConfig('pub_key_cp');
        if(!$data){   // 加密
            $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
            $aes = new Util_Viettelaes($z, 'CBC', $iv);
            $encrypted = $aes->encrypt();     
            $aeskey = bin2hex($encrypted);
            // 需要加密参数
            if($svid) $svid = $svid.'@';
            if($sessionid) $sessionid = '#'.$sessionid;
            $reqStr = $svid.str_shuffle('123456789').$sessionid;
            $sess = date('YmdHis').str_shuffle('abcd');
            $data = 'SUB=FUNVIDEO_GOINGAY&REQ='.$reqStr.'&SOURCE=WAP&SESS='.$sess;   // 视频业务参数
            $data = pkcs5_pad($data, 16);	
            $value_encrypt_aes = encrypt($data,$aeskey);
            $value_with_key = 'value='.$value_encrypt_aes.'&key='.$aeskey;	
            openssl_public_encrypt($value_with_key,$data_encrypted,$pub_key);
            $data_encrypted = base64_encode($data_encrypted);
            $signature ='';
            openssl_sign($data_encrypted, $signature, $pri_key_cp, OPENSSL_ALGO_SHA1);
            $signature = base64_encode($signature);
            $signature = urlencode($signature);
            
            // 验证参数 可选代码
            $verify = openssl_verify (($data_encrypted) , base64_decode(urldecode($signature)) , $pub_key_cp, OPENSSL_ALGO_SHA1);
            openssl_private_decrypt(($data_encrypted),$data_decrypted,$pri_key_cp);
            $value_decrypt = decrypt($value_encrypt_aes,$aeskey);
            
            // 跳转运营商 , 拼接参数
            $url_mobile = $this->server_video.'mobile.html?PRO=BLUEMOBILE&SER=FUNVIDEO&SUB=FUNVIDEO_GOINGAY&DATA='.urlencode( $data_encrypted).'&SIG='.$signature;
            $data_encrypt['status'] = 201;
            $data_encrypt['redirect'] = $url_mobile;
            return json_encode($data_encrypt);  // 获取跳转链接
        }else if($data){  // 解密
            // 成功解密：DATA=CmGuz2Dny3LJ8GPatJ2oAvEXYc4cwZ JR2451lWcyIkkNSZ/1FJp9PLSQgh54/1 I8qzniBII/9NiskxFj61NivJaUGYNkALpeAu9NaLLJAeABCqywFPi7j5MmdJ/mZwSmfjYsY5AYpC1uw4mgrTzJQ 2yLTkmk XMC9HzRFWQYa7yERHsgZ2E/FPyiGinL5C9XMtWxgi55GrIyYDMR95dXB7m51e2vg1oYu0pgxz17Wgy0ZEaiTa3sAsY4ZUlc/dSwHiKks8BAOnyXoVkGKZGzW m9Z2lKP3icvK6GUIg5lyHNGvfnXn8YUmUEo/aa9LPQZ0uJQL Fr K0pWj8 9gj454nEJvAWtblBIRLlxq7eg00i02eOwFPP9WFUnZNwIOG/1NeQ9Z/M9InrqZXrXV0KIe7jxhvQ43yN3oKiBHb0QK5wVJKn9y2jJb8GofyTeV7f7EgkQSwBXMZn8gS5jYxomixBfYoW7sWUJQTPzuldybcHE7pBiE6jFAMn dbSUZAjYv3yUWxDSFw3ulhmFDH0H16Ff5q2q/JuArp/pd2aiipDtpIB9p0IWFEzTPL3m ArLkn76NCjWb4vj8FZ SbY5rfwknxomH0mZZJ RqmCJq yFHQ 5K63NT4h7ZexMskkJc6LQSKG5FXIU/FBwbgm7Aq91tIHfwIfzvnvvY4=
            //$data = urldecode($_GET['DATA']);  // 直接获取简单
            $data = urldecode(trim($data));
            $data = urldecode($data);
            $data = str_replace(' ','+',$data);
            $data_encrypted = base64_decode($data); //编码
            openssl_private_decrypt($data_encrypted, $data, $pri_key_cp, OPENSSL_SSLV23_PADDING); //解密
            $array = explode('&',$data); //处理结果
            $datavalue = $array[0];
            $value = explode('=',$datavalue);
            $datakey = $array[1];
            $key = explode('=',$datakey);
            $res = decrypt($value[1],$key[1]);
            parse_str ( $res ,  $data_plaintext );  //结果数组化
            $data_plaintext['status'] = 200;
            //dd($data_plaintext);
            return json_encode($data_plaintext);  // 解密后明文
        }
        Yaf_Dispatcher::getInstance()->autoRender(FALSE);  // 关闭自动加载模板
    }
    
  
    public function infoAction(){
        session_start();
        dd($_SESSION);
        return false;
    }

}
