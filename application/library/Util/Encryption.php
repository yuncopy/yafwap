<?php

/**
 * Description of Encryption_STV
 *
 */
class Util_Encryption
{
    protected $_skey;
    protected $_signature;

    protected $_public_key2='-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzcl6uEavT2NngmgUlNuX
H9vwsFSG2n/jsQoPRkuJnqqI37nDmbnGNUU7RBd2Jqlk+IddbwQ+AE7DC/XW5MdB
273TNyTdNC9SCYvbpMLLdMb1Ac5JWlAaaF7Tk2xojso2DhLcDjNoG5O4WfBsAsdX
EzmKOUZvakMDM4iDLFKlwjDL2zgCZG0DesUbzgg9rx2NzHz25N7s0oHUx8uv6qZ5
w1/dzySTaxmuFO8qbtfM8cXbycR/hRwMl1adtByS9wPGANk0tMCl9na0BXchdicm
acOe94pn0Db/f2VAU3nikPFLUlRVQ8mguBJYFm/W7wdbYZ7sK7MMBevQkxlP6I5i
TwIDAQAB
-----END PUBLIC KEY-----';


    protected $_private_key2='-----BEGIN RSA PRIVATE KEY-----
MIIEowIBAAKCAQEAzcl6uEavT2NngmgUlNuXH9vwsFSG2n/jsQoPRkuJnqqI37nD
mbnGNUU7RBd2Jqlk+IddbwQ+AE7DC/XW5MdB273TNyTdNC9SCYvbpMLLdMb1Ac5J
WlAaaF7Tk2xojso2DhLcDjNoG5O4WfBsAsdXEzmKOUZvakMDM4iDLFKlwjDL2zgC
ZG0DesUbzgg9rx2NzHz25N7s0oHUx8uv6qZ5w1/dzySTaxmuFO8qbtfM8cXbycR/
hRwMl1adtByS9wPGANk0tMCl9na0BXchdicmacOe94pn0Db/f2VAU3nikPFLUlRV
Q8mguBJYFm/W7wdbYZ7sK7MMBevQkxlP6I5iTwIDAQABAoIBABLBWyryXkX5BFUR
UVbj0Jk5vW0Etymw4DqhdRTuC4PnfQQO9THMibEPN0CUdtM9OxOfmrdkhpYWl5Y+
eLBvLKRvX5G2DWcVLP/rvD/+9PYWr/vTJkJR7KY4hkX4amshKN4Nf/rXWpSw2Otx
ixaRvnDlHKDtt3fS1bbbqUDf43WYPQNCuODVvveoFVOJoxJ8wCGy05vm4chiEz5M
7ZQCmZevh5hrQ43ES2pNVG6tVtgq3mjctTiIbGTauFWX8yGpITrbrW64CyYDkJWq
0/Bp7mJn5Ho8/AhqGqGsWtgJb5WNHeDKM3PY0iQeZN3HlF36tMsDynLmMgdqObe4
pq/dzikCgYEA81rXX1FDAWdUTpw2QebyeXB4lZbw3ouJGTqO9KMyCtQ19xJnEpUm
85nKmQDxhrvA0Z8RljaPgd0mdz7TIt0fHm1x1SgDoT+Cn1nOcjyT+2eiRlOTFMug
aMhe4G68+pmd98VcbD9v8euNGOQgDrtL/3kEbR9/hvMVZ3DXfH1cFsUCgYEA2Hrn
H9CphhDo5QpvFJeds8cU/xT9zHn7BSsU4JzHz+nCFV7iRyU+qvC3rGM8vhFCLO/5
7RFyUKQGWqXKq9I4iQ9xecNHNAd0uCtyyLdJljp6irzZAs+FszngKiGAML3+BHqU
CYChmgO+Vh5y1bjDxO0qGUUBYJLvDJo0hZtDhgMCgYBLBMb9+EQKq7renONQ/4vi
CdzyaFyKjkNORrIJEkH7+p1ENCUYImimkdNVxWevXDO9qya1ws4Mf4s7GV/nEHlm
XODJkBNGYxi2XO36Y1hPGQx6V4swzvD9PD+bOigBrNd4qRurtwagcjfF45VKVUzB
TaIiraIXjNodUDjthIud7QKBgG2zooo52J6K1Sq0ZeQejRk8isRVI+RJ8E4HLfZB
HPSctfDUe4CoPCCuCbX/OBntbwaMua9MRwzcgifPmmwGBQZX4B5fOicAnEJ0Kh/S
2iSaD91co/BLr6pKavGOtoJ81Uv5vikumTYOLZdAqNrrXbHeqZXSpUcGTsOpJXKL
YuV3AoGBAL7t/g8yKfLqJw+yle4DEadc0O+BJr/T8hugHmTUn2s/zePMu9WiOl1W
LwmtIeRotweb5n3l5TWBtqwMMW4YUJQ1JWn6Z/SLGKMOLap2rcWU8EgGoY2L0MF4
ck9Pulvl2EYU5LSpmlpHNqHO3VJ4LQCpnt45mL5BBq0jROr/DXwS
-----END RSA PRIVATE KEY-----';

protected $_public_key1='-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4T48tsCfzsSujW7SpAI0
upWr2Qt5xUQLdOr4VGCNKuLmB0VXruCwdzvt7bpMqvxQBWSVm69UjkAg3E3eUSWc
HFwc6K8c/bZpBMLcgFx63Dy84p5iA7VKMac1EMxhPmkSN6ZiOs7OrLeRaQ+ZfJef
4F6qQhw0qWor6BAa4CoOBMyUge2ZJfR8CkikGF0l6udeYOkggzfgvX8UmxN8uyzQ
baWWYjYzg0TsfXbC1vZhv9xP+ulqqjRRmOcqyrcNQNPOYLskrrWNsx5cR0YDZbTO
oGyaXSdLOKC5b6+WUjMSn83zUUz/lpdrRueRurFHiPxm4cetxShl4KxsDp2Qc6JQ
uQIDAQAB
-----END PUBLIC KEY-----';

    public function __construct()
    {
        $this->_skey = 'abcdef0123456789abcdef0123456789';
    }


    public function makeData($input){

		$value = $this->encode($input);
		$key = $this->_skey;
		$newvalue = "data=$value&key=$key";

		//echo "<p>", $newvalue, "</p>";
		$data = $this->encryptData($newvalue);

		$signature = $this->createSignature($data);

		return 'data='.$data.'&signature='.$signature;
    }

    public function decrypData($data,$signature){

        if ($this->verifySignature($data, $signature))
        {

            $datadecrypt = $this->decryptData($data);
            $this->_skey =$this->Query($datadecrypt, 'key');
            $dataAES = $this->Query($datadecrypt, 'data');
            return $this->decodeByKey($dataAES, $this->_skey);
        }
        return '';
    }
    public function Query($data, $key)
    {
        $temp1 = split('&', $data);
        for ($index = 0; $index < count($temp1); $index++) {
            $temp2 = split('=', $temp1[$index]);
            if (strtolower($temp2[0])==strtolower($key)){
                return $temp2[1];
            }
        }
        return '';
    }

    public function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='), array('-','_',''), $data);
        return $data;
    }

    public function safe_b64decode($string) {
        $data = str_replace(array('-','_'), array('+','/'), $string);
        $mod4 = strlen($data)%4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public function encode($value){
        if (!$value) { return false; }
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);

        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->_skey, $text, MCRYPT_MODE_ECB, $iv);

        return trim($this->safe_b64encode($crypttext));
    }

    public function decode($value){
        if (!$value) { return false; }
        $crypttext = $this->safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->_skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }
    public function decodeByKey($value,$_skey){
        if (!$value) { return false; }
        $crypttext = $this->safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $_skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }

    public function encryptData($value) {
        try {
            openssl_public_encrypt($value, $crypttext, $this->_public_key1);
            return trim($this->safe_b64encode($crypttext));
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return '';
    }

    public function decryptData($value) {
        try {
            openssl_private_decrypt($this->safe_b64decode($value), $newsource, $this->_private_key2);
            return trim($newsource);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return '';
    }

    public function createSignature($value) {
        openssl_sign($value, $this->_signature, $this->_private_key2, OPENSSL_ALGO_SHA1);
        return trim($this->safe_b64encode($this->_signature));
    }

    public function verifySignature($value, $signature) {
        $verify = openssl_verify($value, $this->safe_b64decode($signature), $this->_public_key1, OPENSSL_ALGO_SHA1);
        return $verify;
    }
}
