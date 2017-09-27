<?php
/**
 * @name Bootstrap
 * @author copy
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract {
    
    static public  $config = null;
    public function _initConfig() {
        //把配置保存起来
        $arrConfig = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $arrConfig);
        if(self::$config == null){
            self::$config = Yaf_Application::app()->getConfig()->toArray();
        }
    }
     public function _initLoader()
    {
        Yaf_Loader::import(APP_PATH . "/vendor/autoload.php");
        Yaf_Loader::import(APP_PATH . "/application/function.php");
        // 注册本地类名前缀, 这部分类名将会在本地类库查找
        Yaf_Loader::getInstance()->registerLocalNameSpace(array('Http','Util','Log'));
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
            //注册一个插件
            $objSamplePlugin = new SamplePlugin();
            $dispatcher->registerPlugin($objSamplePlugin);
    }

    public function _initRoute(Yaf_Dispatcher $dispatcher) {
            //在这里注册自己的路由协议,默认使用简单路由
    }

    public function _initView(Yaf_Dispatcher $dispatcher) {
        //在这里注册自己的view控制器，例如smarty,firekylin
        
    }
    
    //注册错误调试模式功能
    public function _initError(Yaf_Dispatcher $dispatcher) {
        $config = self::$config;
        if ($config['application']['debug'])
        {
            ini_set('display_errors', 'On');
        }
        else
        {
            ini_set('display_errors', 'Off');
        }
    }
        
}
