<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Medoo\Medoo;

class BlueModel {

    protected $write_mode = false;
    private static $db;  //保存类实例的静态成员变量
    private static $read;  //保存类实例的静态成员变量
	/* 方法一
	public function __construct() {
        $database = getConfig('database');
        try{
            if($this->write_mode){  // 读写
                $database_init = $database['readwrite'];
            }else{  // 读
                $database_init = $database['read'];
            }
            parent::__construct($database_init);
        }catch (\PDOException $e){
            Log_Log::info('[PDO] content init error:' . $e->getMessage(), true, true);
        }
    }*/
	
    public function __construct() {
        $database = getConfig('database');
        if ($this->write_mode) {  // 读写
            $database_init = $database['readwrite'];
            if (!(self::$db instanceof Medoo)) {  //instanceof用于确定一个 PHP 变量是否属于某一类 class 的实例
                self::$db = new Medoo($database_init);
            }
        } else {  // 读
            $database_init = $database['read'];
            if (!(self::$read instanceof Medoo)) {  //instanceof用于确定一个 PHP 变量是否属于某一类 class 的实例
                self::$read = new Medoo($database_init);
            }
        }
    }
    
     /** 
     * Call a method dynamically 
     * 
     * @param string $method 
     * @param array $args 
     * @return mixed 
     */ 
    public function __call($method, $args)   //魔术方法 ，在对象中调用一个不可访问方法时，__call() 会被调用。 
    { 
        if(!method_exists($this, $method)){ 
            $db_model = self::$db;
            if(!$this->write_mode){
                $db_model = self::$read;
            }
            return call_user_func_array(array($db_model, $method), $args); //http://php.net/manual/zh/function.call-user-func-array.php
        }else{ 
          throw new Exception(sprintf('The required method "%s" exist for %s', $method, get_class($this))); 
        } 
    } 


    //创建__clone方法防止对象被复制克隆
    public function __clone() {
       trigger_error('Clone is not allow!', E_USER_ERROR);
    }
    
    

}
