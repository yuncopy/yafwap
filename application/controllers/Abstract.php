<?php
/**
 * Class AbstractController
 */
use Joomla\Filter\InputFilter;  //https://packagist.org/packages/joomla/input
use Joomla\Input;
use duncan3dc\Sessions\SessionInstance;
use duncan3dc\Sessions\Cookie;
abstract class AbstractController extends Yaf_Controller_Abstract
{
    
    public $input =null;
    public $session =null;
    public $cookie =null;
    public $site = null;
    static $telco_arr= array();
    /**
     * 登录、权限判断、初始化
     */
    public function init()
    {
        
        //组件对象 (输入)
        // $this->getRequest()->getQuery("paramname", "default value");
        // http://php.net/manual/zh/class.yaf-request-http.php
        if($this->input == null) $this->input = new Input\Input;
        if($this->session == null) {
            $cookie = new Cookie;  //http://php.net/manual/en/function.session-set-cookie-params.php
            $lifetime = 3600;  // 秒
            $cookie = $cookie->withLifetime($lifetime);
            $this->session = new SessionInstance("wap_",$cookie);  // 初始化
            Yaf_Registry::set('session',$this->session);
        };
        
        microtime_float(2);
        $this->site = $this->getSite(); // 网站类型 视频，游戏
        $this->menus(); // 加载菜单
        $this->getOptions(); // 网站描述信息
        $telco = $this->getTelco(); // 获取运营商
        microtime_float(3);
        $this->checkLogin();  // 检查是否登录系统
        $this->appLogin();
    }
   
    // 检查是否MT成功过
    public function checkLogin(){
        $IsLogin = systemConfig('IsLogin');
        $login = $this->session->get($IsLogin); 
        $this->assign(array('login'=>$login));
    }
    
    // 检查用户是否成功登录
    public function appLogin(){
        $UserLogin = systemConfig('UserLogin');
        $applogin = $this->session->get($UserLogin);
       
        $this->assign(array('applogin'=>$applogin));
    }



    //获取运营商
    public function getTelco(){
        if(empty(self::$telco_arr)){
            $network = new Util_Network();
            $GetTelco = systemConfig('GetTelco');  // 设置键
            $telcoName_Arr = unserialize($this->session->get($GetTelco));
            if(!$telcoName_Arr){  // 没有缓存则去请求获取运营商
                $telcoName_Arr = $network -> getTelcoName();  // 获取运营商
                $this->session->set($GetTelco,serialize($telcoName_Arr));
            }
            Log_Log::info(__METHOD__.' content init network:' . json_encode($telcoName_Arr), true, true);  // 记录日志
            self::$telco_arr = $telcoName_Arr;
        }
        return self::$telco_arr;
    }

        /**
    * 
    * 渲染一个视图模板, 并直接输出给请求端
    * @param unknown_type $c 控制器
    * @param unknown_type $a 文件名
    */
   public function display($path){
        $view_path = $this->getViewpath();
        $view_file = $path.'.phtml';
        if(file_exists($view_path."/".$view_file)){
            $this->getView()->display($view_file); 
            return $this;
        }
        throw new Exception(__METHOD__);
   }


   /**
    * 
    * 为视图引擎分配一个模板变量, 在视图模板中可以直接通过${$name}获取模板变量值
    * @param unknown_type $params
    */
   public function assign($params){
        if(is_array($params)){
            $this->getView()->assign($params);
            return $this;
        }
        throw new Exception(__METHOD__);
   }
   
   
   /**
    * 菜单定义
    */
   public function  menus($list=false){
       $key= systemConfig('Menus');  //unserialize
       $cache_menus = $this->session->get($key);
       $menus = unserialize($cache_menus);
       //$this->session->delete($key);
       if(!$cache_menus){
            $_menus = new MenusModel();
            $menus = $_menus->menusList($this->site);
            $this->session->set($key, serialize($menus));
        }
        if($list) return $menus;
        $menus_n = make_to_tree($menus);
        $this->assign(array('menus'=>$menus_n));
   }
   
   /**
    * 定义网站描述信息
    */
   private function  getOptions(){
       $key= systemConfig('Options');  //unserialize
       $cache_options = $this->session->get($key);
       $site = unserialize($cache_options);
       if(!$cache_options){
            $options = new OptionsModel();
            $site = $options->getOptions($this->site);
            $this->session->set($key, serialize($site));
       }
       $this->assign(array('site'=>$site));
   }
   
   /**
    * 网站类型
    */
   private function getSite(){
        $server = $this->getRequest()->getServer(); // 获取请求 SERVER
        $host = $server['HTTP_HOST'];
        $web_server  = getConfig('web_server');
        if($web_server['web_video_host'] == $host){
            $site = 1;  // 视频站点
        }else if($web_server['web_game_host'] == $host){
            $site = 2;  // ?游戏站点
        }
        return  $site;
   }
   
    /**
    * 错误输出
    */
    public function error($status = '400', $message = '') {
        return json_encode(['status' => (string) $status, 'message' => $message], JSON_UNESCAPED_UNICODE);
    }
   
 

}
