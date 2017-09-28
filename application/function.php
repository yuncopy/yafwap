<?php

/**
 *  公共函数库
 */
if (!function_exists('p')) {

    /**
     * 打印数组
     * @param $data
     */
    function p($data) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

}


if (!function_exists('format_json')) {
    /** 方便阅读
     * A function which takes in a JSON string and formats it in a human-readable and computer-readable manner.
     *
     * @param string $json A JSON string (output of json_encode)
     * @return string A formatted JSON string
     *
     * @author Edmund Gentle (https://github.com/edmundgentle)
     */
    function format_json($json) {
        $indents=0;
        $output='';
        $inside=false;
        for ($i = 0, $j = strlen($json); $i < $j; $i++) {
                $char=$json[$i];
                if($char=='{' || $char=='[') {
                        if(!$inside) {
                                $indents+=3;
                                $output.=$char."\n".space($indents);
                        }else{
                                $output.=$char;
                        }
                }elseif($char==',') {
                        if(!$inside) {
                                $output.=$char."\n".space($indents);
                        }else{
                                $output.=$char;
                        }
                }elseif($char==':') {
                        if(!$inside) {
                                $output.=$char." ";
                        }else{
                                $output.=$char;
                        }
                }elseif($char=='}' || $char==']') {
                        if(!$inside) {
                                $indents-=3;
                                $output.="\n".space($indents).$char;
                        }else{
                                $output.=$char;
                        }
                }elseif($char=='"') {
                        if($inside) {
                                $inside=false;
                        }else{
                                $inside=true;
                        }
                        $output.=$char;
                }else{
                        $output.=$char;
                }
        }
        $output=str_replace('\/','/',$output);
        return $output;
    }
    /**
     * Returns a string containing a given number of spaces. Used by the format_json function.
     *
     * @param integer $x The number of spaces to return
     * @return string A given number of spaces.
     *
     * @author Edmund Gentle (https://github.com/edmundgentle)
     */
    function space($x) {
        $output='';
        for($y=1;$y<=$x;$y++) {
                $output.=' ';
        }
        return $output;
    }
}



// 形成树状结构
if (!function_exists('make_to_tree')) {
    /*
     * 二数组形成树状结构
     * @param $arr 操作数组
     * @param $parent_id 顶级父ID
     * @param $parent_name 顶级父字段名
     * @param $primary_key 主键
     * return void
     */
    function make_to_tree($arr, $parent_id = 0, $parent_name = "pid", $primary_key = "id") {
        $new_arr = array();
        foreach ($arr as $k => $v) {
            if ($v[$parent_name] == $parent_id) {
                $new_arr[] = $v;
                unset($arr[$k]);
            }
        }
        foreach ($new_arr as &$a) {
            $a['children'] = make_to_tree($arr, $a[$primary_key]);
        }
        return $new_arr;
    }

}

// 真实数据
if (!function_exists('getRealIp')) {
    /*
     * 获取客服端IP
     * return string
     */
    function getRealIp() {
        static $realip;
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } else if (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        return $realip;
    }

}


if (!function_exists('bluepay_array_sort')) {

     /*
     * PHP二维数组排序函数
     * @param $arr 排序数组
     * @param $keys 按照键排序
     * @param  $type  升序/降序
     * return array
     */
    function bluepay_array_sort($arr, $keys, $type = 'asc') {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }

}

if (!function_exists('subtext')) {
    /*
     * 跳转
     * @param $text 字符串
     * @param $length 长度
     * return string
     */
    function subtext($text, $length) {
        if (mb_strlen($text, 'utf8') > $length)
            return mb_substr($text, 0, $length, 'utf8') . '...';
        return $text;
    }

}


if (!function_exists('jump')) {
    /*
     * 跳转
     * @param $url 目标地址
     * @param $info 提示信息
     * @param $sec 等待时间
     * return void
     */

    function jump($url, $info = null, $sec = 3) {
        if (is_null($info)) {
            header("Location:$url");exit;
        } else {
            die("<meta http-equiv='refres' content='{$sec}';URL='{$url}'>".$info );
        }
    }

}
if (!function_exists('microtime_float')) {
    function  microtime_float ($step=1,$file='/tmp/time.log')
    {
        list( $usec ,  $sec ) =  explode ( " " ,  microtime ());
        $micTime =  ((float) $usec  + (float) $sec );
        list($usecm, $secm) = explode(".", $micTime);
        $date = date('Y-m-d H:i:s x(ms)',$usecm);
        $text = str_replace('x', $secm, $date);
        //运行记录
        file_put_contents($file, "step:{$step}||{$text}".PHP_EOL , FILE_APPEND);
    }
}
/***********************************************基于项目函数******************************************************/

if (!function_exists('Config')) {

    /**
     * 获取配置文件信息
     * @param null $name  Config('system.NetType');
     * @return mixed
     */
    function Config($name) {
        static $conf = null;
        $array_conf = explode('.', $name);
        $config_path = APP_PATH . "/conf/{$array_conf[0]}.php";
        if (is_file($config_path) && file_exists($config_path)) {
            $conf = !empty($conf) ? $conf : include ($config_path);
            array_shift($array_conf);
//          $str = '';
//          foreach ($array_conf as $v){
//             $str .= '["'.$v.'"]';
//          }
//          return eval('return $conf'.$str.';');  // 形成字符串执行
            $info = $conf;
            foreach($array_conf as $v){
               if(isset($info[$v])){
                   $info = $info[$v];  // 重新更新变量值，因为循环程序没有结束，可以操作变量值
               }
            }
            return $info;
        }
    }
}


if (!function_exists('getConfig')) {

    /**
     * 获取配置文件信息
     * @param $field
     * @param null $key
     * @return mixed
     */
    function getConfig($field, $key = null) {
        $data = Yaf_Registry::get('config')->toArray();
        return $key ? $data[$field][$key] : $data[$field];
    }

}

if (!function_exists('secretConfig')) {

    /**
     * 获取配置文件信息
     * @param null $key
     * @return mixed
     */
    function secretConfig($name, $key = null) {
        static $conf = null;
        $config_path = APP_PATH . "/conf/secret.php";
        if (is_file($config_path) && file_exists($config_path)) {
            $conf = !empty($conf) ? $conf : include_once ($config_path);
            if (isset($conf[$name])) {
                return $conf[$name];
            }
        }
    }

}

if (!function_exists('systemConfig')) {

    /**
     * 获取配置文件信息
     * @param null $key
     * @return mixed
     */
    function systemConfig($name) {
        static $conf = null;
        $config_path = APP_PATH . "/conf/system.php";
        if (is_file($config_path) && file_exists($config_path)) {
            $conf = !empty($conf) ? $conf : include_once ($config_path);
            if (isset($conf[$name])) {
                return $conf[$name];
            }
        }
    }
}


// 设置网络类型
if (!function_exists('netType')) {

    function netType($value = null) {
        static $NetType;
        $netType_key = systemConfig('NetType');  // 设置键
        $session = Yaf_Registry::get('session'); // 获取SESSIO对象
        if ($value) {
            $NetType = $session->set($netType_key, $value);
        } else {
            $NetType = $session->get($netType_key);
        }
        return $NetType;
    }

}


if (!function_exists('videourl')) {
    /*
     * 获取订阅地址
     */

    function videourl() {
        $UserSub = systemConfig('UserSub');
        $session = Yaf_Registry::get('session'); // 获取SESSIO对象
        $UserContent = $session->get($UserSub);
        if($UserContent){
            $REQ = $UserContent['REQ'];
            $sessionid = ltrim(strstr ( $REQ ,  '#' ),'#');
            $svid = strstr($REQ, '@', TRUE);
            $msisdn = $UserContent['MOBILE'];
            $params = array(
                'sessionid'=>$sessionid,
                'svid'=>$svid,
                'msisdn'=>$msisdn
            );
            $route = '/sub/index?'.http_build_query($params);
            return $route;
        }
    }

}


//获取手机号
if (!function_exists('getmsisdn')) {
    
    function getmsisdn(){
        $GetMsisdn = systemConfig('GetMsisdn');
        $session = Yaf_Registry::get('session'); // 获取SESSIO对象
        $msisdn = $session->get($GetMsisdn);
        return !empty($msisdn) ? $msisdn : false;
    }
}

if (!function_exists('microtime_float')) {
    /**
     * 运行记录 记录到毫秒
     */
    function  microtime_float ($step=1,$file='/tmp/times.log')
    {
        list( $usec ,  $sec ) =  explode ( " " ,  microtime ());
        $micTime =  ((float) $usec  + (float) $sec );
        list($usecm, $secm) = explode(".", $micTime);
        $date = date('Y-m-d H:i:s x',$usecm);
        $text = str_replace('x', $secm, $date);
        //运行记录
        file_put_contents($file, "step:{$step}|{$text}".PHP_EOL , FILE_APPEND);
    }
}

// 订阅函数
if (!function_exists('encrypt')) {
    function encrypt($encrypt, $key){
         $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);
         $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, pack("H*", $key), $encrypt, MCRYPT_MODE_ECB, $iv));
         return $encrypted;
    }
}
if (!function_exists('decrypt')) {
    function decrypt($decrypt, $key){ 
         $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND);
         $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128,  pack("H*", $key), base64_decode($decrypt), MCRYPT_MODE_ECB, $iv);
         return $decrypted;
    }
}
if (!function_exists('pkcs5_pad')) {
    function pkcs5_pad ($text, $blocksize) { 
      $pad = $blocksize - (strlen($text) % $blocksize); 
      return $text . str_repeat(chr($pad), $pad); 
    }
}


