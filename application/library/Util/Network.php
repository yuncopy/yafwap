<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use IPTools\IP,IPTools\Network,IPTools\Range;
class Util_Network
{
    
    
    protected static $ip;
    protected static $isv6 = false;
    
    //IPv4  营运商网段地址列表 
    protected $network_range=array(  
        'viettel'=>array(
                '203.113.128.0/18',
                '220.231.64.0/18',
                '125.234.0.0/15',
                '117.0.0.0/13',
                '115.72.0.0/13',
                '27.64.0.0/12',
                '171.224.0.0/11',
                '116.96.0.0/12',
                '125.212.128.0/17',
                '125.214.0.0/18',
                '203.190.160.0/20'
        ),
        'vinaphone'=>array(
                '203.162.0.0/16',
                '203.210.128.0/17',
                '221.132.0.0/18',
                '203.160.0.0/23',
                '222.252.0.0/14',
                '123.16.0.0/12',
                '113.160.0.0/11',
                '14.160.0.0/11',
                '14.224.0.0/11',
                '221.132.30.0/23',
                '221.132.32.0/21'
        ),
        'mobifone'=>array(
                '111.91.232.0/22',
                '103.19.164.0/22',
                '45.125.208.0/22',
                '103.53.252.0/22',
                '45.121.24.0/22',
                '103.199.20.0/22',
                '59.153.220.0/22',
                '103.199.24.0/22',
                '59.153.224.0/22',
                '103.199.28.0/22',
                '59.153.228.0/22',
                '103.199.32.0/22',
                '59.153.232.0/22',
                '103.199.40.0/22',
                '59.153.236.0/22',
                '103.199.36.0/22',
                '59.153.240.0/22',
                '103.199.44.0/22',
                '59.153.244.0/22',
                '103.199.52.0/22',
                '59.153.248.0/22',
                '103.199.48.0/22',
                '59.153.252.0/22',
                '103.199.56.0/22',
                '137.59.32.0/22',
                '103.199.60.0/22',
                '137.59.36.0/22',
                '103.199.68.0/22',
                '137.59.24.0/22',
                '103.199.72.0/22',
                '137.59.40.0/22',
                '103.199.76.0/22',
                '137.59.44.0/22',
                '103.199.64.0/22',
                '137.59.28.0/22',
        )
    );
    
    //IPv6  营运商网段地址列表
    protected  $network_range_ipv6 = array(
        'viettel'=>array(
                '2402:0800::/32',
                '2401:d800::/32'
            ),
        'vinaphone'=>array(
            '2001:0EE0::/32',
            '2001:0EE0:1::/48'
        ),
        'mobifone'=>array(
            '2001:0DF0:2E8::/48',
            '2402:9D80::/32'
        )
    );
    
    public function getTelcoName($ip_address=false){
        if(!$ip_address) $ip_address = $this->getIpAddress();   // 获取IP地址
        //$ip_address = '116.109.172.123';   // 测试代码
        //$ip_address = '2001:cdba:0000:0000:0000:0000:3257:9652';  //测试代码
        /**ip=59.153.243.244&ipv=IPv4&operator=mobifone
         * ip=116.109.172.123&ipv=IPv4&operator=viettel
         * ip=59.153.243.109&ipv=IPv4&operator=mobifone
         * ip=113.179.140.220&ipv=IPv4&operator=vinaphone
         */
        $check_ip = self::isValid($ip_address); // 检查IP地址
        if($check_ip){
            $ip_ipv = new IP($ip_address);
            $ip_version = trim($ip_ipv->version);  //获取IP地址版本
            $operator = $code = '';
            switch ($ip_version){
                case 'IPv4':
                    foreach ($this->network_range as $key => $value){
                        foreach($value as $kk => $vv){
                            $status = self::match($ip_address, $vv); // 判断是否存在网段范围
                            if($status){
                                $operator= $key;$code = 200;break;
                            }
                        }
                    }
                break;
                case 'IPv6':
                    /* 测试代码
                    $hosts = Network::parse('2001:0EE0:1::/48')->hosts;  //获取子网范围
                    $firstIP = (string)$hosts->firstIP;  // 第一个子网地址
                    $format_firstIP = str_replace('::', str_repeat(':0000', 8 - substr_count($firstIP, ':') + 1 ), $firstIP); 
                    $lastIP = (string)$hosts->lastIP;   // 第最后一个子网地址
                    echo $format_firstIP.'<br/>';
                    echo $lastIP.'<br/>';
                    $status = IPs::match( $ip_address,$format_firstIP.'-'.$lastIP); // true
                    var_dump($status);exit;
                     */
                    foreach ($this->network_range_ipv6 as $key => $value){
                        foreach($value as $kk => $vv){
                            $hosts = Network::parse( $vv )->hosts;  //获取子网范围
                            $firstIP = (string)$hosts->firstIP;  // 第一个子网地址
                            $format_firstIP = str_replace('::', str_repeat(':0000', 8 - substr_count($firstIP, ':') + 1 ), $firstIP); // 严格正确格式
                            $lastIP = (string)$hosts->lastIP;   // 第最后一个子网地址
                            $status = self::match($ip_address,$format_firstIP.'-'.$lastIP); // 验证IP范围
                            if($status){
                                $operator = $key;$code = 200;break;
                            }
                        }
                    }
                break;
            }
        }
        $out_data['operator'] = $operator;
        $out_data['ip'] = $ip_address;
        $out_data['status'] = $code;
        return !empty($out_data) ? $out_data : false;
    }
 
    
    
   /**
     * Checks if an IP is valid.
     *
     * @param string $ip IP
     * @return boolean true if IP is valid, otherwise false.
     */
    public static function isValid($ip)
    {
        $valid = self::isValidv4($ip);
        if ($valid) {
            self::$ip = $ip;
            self::$isv6 = false;
            return true;
        }

        $valid = self::isValidv6($ip);
        if ($valid) {
            self::$ip = $ip;
            self::$isv6 = true;
            return true;
        }
        return false;
    }


    /**
     * Checks if an IP is valid IPv4 format.
     *
     * @param string $ip IP
     * @return boolean true if IP is valid IPv4, otherwise false.
     */
    public static function isValidv4($ip)
    {
        return (bool)filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * Checks if an IP is valid IPv6 format.
     *
     * @param string $ip IP
     * @return boolean true if IP is valid IPv6, otherwise false.
     */
    public static function isValidv6($ip)
    {
        return (bool)filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * Checks if an IP is local
     *
     * @param  string  $ip IP
     * @return boolean     true if the IP is local, otherwise false
     */
    public static function isLocal($ip)
    {
        $localIpv4Ranges = array(
            '10.*.*.*',
            '127.*.*.*',
            '192.168.*.*',
            '169.254.*.*',
            '172.16.0.0-172.31.255.255',
            '224.*.*.*',
        );

        $localIpv6Ranges = array(
            'fe80::/10',
            '::1/128',
            'fc00::/7'
        );

        if (self::isValidv4($ip)) {
            return self::match($ip, $localIpv4Ranges);
        }

        if (self::isValidv6($ip)) {
            return self::match($ip, $localIpv6Ranges);
        }

        return false;
    }

    /**
     * Checks if an IP is remot
     *
     * @param  string  $ip IP
     * @return boolean     true if the IP is remote, otherwise false
     */
    public static function isRemote($ip)
    {
        return !self::isLocal($ip);
    }

    /**
     * Checks if an IP is part of an IP range.
     *
     * @param string $ip IPv4/IPv6
     * @param mixed $range IP range specified in one of the following formats:
     * Wildcard format:     1.2.3.*
     * CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
     * Start-End IP format: 1.2.3.0-1.2.3.255
     * @return boolean true if IP is part of range, otherwise false.
     */
    public static function match($ip, $ranges)
    {
        if (is_array($ranges)) {
            foreach ($ranges as $range) {
                $match = self::compare($ip, $range);
                if ($match) {
                    return true;
                }
            }
        } else {
            return self::compare($ip, $ranges);
        }
        return false;
    }

    /**
     * Checks if an IP is part of an IP range.
     *
     * @param string $ip IPv4/IPv6
     * @param string $range IP range specified in one of the following formats:
     * Wildcard format:     1.2.3.* OR 2001:cdba:0000:0000:0000:0000:3257:*
     * CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
     * Start-End IP format: 1.2.3.0-1.2.3.255 OR 2001:cdba:0000:0000:0000:0000:3257:0001-2001:cdba:0000:0000:0000:0000:3257:1000
     * @return boolean true if IP is part of range, otherwise false.
     */
    public static function compare($ip, $range)
    {
        if (!self::isValid($ip)) {
            throw new \InvalidArgumentException('Input IP "'.$ip.'" is invalid!');
        }

        $status = false;
        if (strpos($range, '/') !== false) {
            $status = self::processWithSlash($range);
        } else if (strpos($range, '*') !== false) {
            $status = self::processWithAsterisk($range);
        } else if (strpos($range, '-') !== false) {
            $status = self::processWithMinus($range);
        } else {
            $status = ($ip === $range);
        }
        return $status;
    }


    /**
     * Checks if an IP is part of an IP range.
     *
     * @param string $range IP range specified in one of the following formats:
     * CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
     * @return boolean true if IP is part of range, otherwise false.
     */
    protected static function processWithSlash($range)
    {
        list($range, $netmask) = explode('/', $range, 2);

        if (self::$isv6) {
            if (strpos($netmask, ':') !== false) {
                $netmask     = str_replace('*', '0', $netmask);
                $netmask_dec = self::ip2long($netmask);
                return ((self::ip2long(self::$ip) & $netmask_dec) == (self::ip2long($range) & $netmask_dec));
            } else {
                $x = explode(':', $range);
                while (count($x) < 8) {
                    $x[] = '0';
                }

                list($a, $b, $c, $d, $e, $f, $g, $h) = $x;
                $range = sprintf(
                    "%u:%u:%u:%u:%u:%u:%u:%u",
                    empty($a) ? '0' : $a,
                    empty($b) ? '0' : $b,
                    empty($c) ? '0' : $c,
                    empty($d) ? '0' : $d,
                    empty($e) ? '0' : $e,
                    empty($f) ? '0' : $f,
                    empty($g) ? '0' : $g,
                    empty($h) ? '0' : $h
                );
                $range_dec           = self::ip2long($range);
                $ip_dec              = self::ip2long(self::$ip);
                $wildcard_dec        = pow(2, (32 - $netmask)) - 1;
                $netmask_dec         = ~$wildcard_dec;

                return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }
        } else {
            if (strpos($netmask, '.') !== false) {
                $netmask     = str_replace('*', '0', $netmask);
                $netmask_dec = self::ip2long($netmask);
                return ((self::ip2long(self::$ip) & $netmask_dec) == (self::ip2long($range) & $netmask_dec));
            } else {
                $x = explode('.', $range);
                while (count($x) < 4) {
                    $x[] = '0';
                }

                list($a, $b, $c, $d) = $x;
                $range               = sprintf("%u.%u.%u.%u", empty($a) ? '0' : $a, empty($b) ? '0' : $b, empty($c) ? '0' : $c, empty($d) ? '0' : $d);
                $range_dec           = self::ip2long($range);
                $ip_dec              = self::ip2long(self::$ip);
                $wildcard_dec        = pow(2, (32 - $netmask)) - 1;
                $netmask_dec         = ~$wildcard_dec;

                return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }
        }

        return false;
    }



    /**
     * Checks if an IP is part of an IP range.
     *
     * @param string $range IP range specified in one of the following formats:
     * Wildcard format:     1.2.3.* OR 2001:cdba:0000:0000:0000:0000:3257:*
     * @return boolean true if IP is part of range, otherwise false.
     */
    protected static function processWithAsterisk($range)
    {
        if (strpos($range, '*') !== false) {
            $lowerRange = self::$isv6 ? '0000' : '0';
            $upperRange = self::$isv6 ? 'ffff' : '255';

            $lower = str_replace('*', $lowerRange, $range);
            $upper = str_replace('*', $upperRange, $range);

            $range = $lower . '-' . $upper;
        }

        if (strpos($range, '-') !== false) {
            return self::processWithMinus($range);
        }

        return false;
    }

    /**
     * Checks if an IP is part of an IP range.
     *
     * @param string $range IP range specified in one of the following formats:
     * Start-End IP format: 1.2.3.0-1.2.3.255 OR 2001:cdba:0000:0000:0000:0000:3257:0001-2001:cdba:0000:0000:0000:0000:3257:1000
     * @return boolean true if IP is part of range, otherwise false.
     */
    protected static function processWithMinus($range)
    {
        list($lower, $upper) = explode('-', $range, 2);
        $lower_dec           = self::ip2long($lower);
        $upper_dec           = self::ip2long($upper);
        $ip_dec              = self::ip2long(self::$ip);

        return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
    }


    /**
     * Gets IP long representation
     *
     * @param string $ip IPv4 or IPv6
     * @return long If IP is valid returns IP long representation, otherwise -1.
     */
    public static function ip2long($ip)
    {
        $long = -1;
        if (self::isValidv6($ip)) {
            if (!function_exists('bcadd')) {
                throw new \RuntimeException('BCMATH extension not installed!');
            }

            $ip_n = inet_pton($ip);
            $bin = '';
            for ($bit = strlen($ip_n) - 1; $bit >= 0; $bit--) {
                $bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
            }

            $dec = '0';
            for ($i = 0; $i < strlen($bin); $i++) {
                $dec = bcmul($dec, '2', 0);
                $dec = bcadd($dec, $bin[$i], 0);
            }
            $long = $dec;
        } else if (self::isValidv4($ip)) {
            $long = ip2long($ip);
        }
        return $long;
    }


    /**
     * Gets IP string representation from IP long
     *
     * @param long $dec IPv4 or IPv6 long
     * @return string If IP is valid returns IP string representation, otherwise ''.
     */
    public static function long2ip($dec, $ipv6 = false)
    {
        $ipstr = '';
        if ($ipv6) {
            if (!function_exists('bcadd')) {
                throw new \RuntimeException('BCMATH extension not installed!');
            }

            $bin = '';
            do {
                $bin = bcmod($dec, '2') . $bin;
                $dec = bcdiv($dec, '2', 0);
            } while (bccomp($dec, '0'));

            $bin = str_pad($bin, 128, '0', STR_PAD_LEFT);
            $ip = array();
            for ($bit = 0; $bit <= 7; $bit++) {
                $bin_part = substr($bin, $bit * 16, 16);
                $ip[] = dechex(bindec($bin_part));
            }
            $ip = implode(':', $ip);
            $ipstr = inet_ntop(inet_pton($ip));
        } else {
            $ipstr = long2ip($dec);
        }
        return $ipstr;
    }

    public static function matchRange($ip, $range)
    {
        $ipParts = explode('.', $ip);
        $rangeParts = explode('.', $range);

        $ipParts = array_filter($ipParts);
        $rangeParts = array_filter($rangeParts);

        $ipParts = array_slice($ipParts, 0, count($rangeParts));

        return implode('.', $rangeParts) === implode('.', $ipParts);
    }
    
    
    /**
     * Returns the default headers to check for the real ip (best to worst)
     */
    private function getDefaultHeaders()
    {
        return [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        ];
    }

    /**
     * Returns client's accurate IP Address
     *
     * @return bool|string ip address, false on failure
     */
    public function getIpAddress()
    {
        $headers = $this->getDefaultHeaders();
        foreach ($headers as $k)
        {
            if (isset($_SERVER[$k]))
            {
                // header can be comma separated
                $ips = explode(',', $_SERVER[$k]);
                $ip = trim(end($ips));
                $ip = filter_var($ip, FILTER_VALIDATE_IP);
                if (false !== $ip)
                {
                    return $ip;
                }
            }
        }

        // no valid ip found
        return false;
    }
    

    
}
