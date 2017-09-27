<?php

class Log_Driver_File
{

    public $config = array(
            'log_time_format' => 'Y-m-d H:i:s',
            'log_file_size' => 1073741824,
            'log_path' => '',
            'log_name'=>'Y_m_d_H',
            'log_num'=>15
    );
    
    // 实例化并传入参数
    public function __construct ($config = array())
    {
        $this->config['log_path'] = getConfig('log', 'path');
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 日志写入接口
     * @access public
     * @param string $log 日志信息
     * @param string $destination 写入目标
     * @throws Exception
     * @return void
     */
    public function write ($log, $destination = '')
    {
         $this->clearLog();
        $now = date($this->config['log_time_format']);
        if (empty($destination))
        $destination = date($this->config['log_name']) . '.log';
        $destination = $this->config['log_path'] . $destination;
        $path = dirname($destination);
        if (!is_dir($path) && ! @mkdir($path, 0777, true)) {
            throw new Exception('创建日志目录失败:'.$path, 2);
        }

        // 检测日志文件大小，超过配置大小则备份日志文件重新生成
        clearstatcache();
        if (
            file_exists($destination) && 
            is_writable($destination) &&
            is_writable(dirname($destination)) &&
            floor($this->config['log_file_size']) <= filesize($destination)
         ) {
            try {
                @ rename(
                    $destination, 
                    dirname($destination) . '/' . time() . '-' . basename($destination)
                );
            } catch (Exception $e) {
                @ Log::warn('尝试重命名｛' . $destination . '｝失败，请检查文件和文件夹权限', true, true);
            }
        }
        clearstatcache();
       
        error_log(
            "[{$now}] " . $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['REQUEST_URI'] . "\r\n{$log}\r\n",
            3, 
            $destination
        );
        if (PHP_OS != 'WINNT') {
            $pid = posix_getpwuid(posix_geteuid());
            if (strtolower($pid['name']) == 'root') {
                @chown($destination, getConfig('web_server', 'user'));
                @chgrp($destination, getConfig('web_server', 'group'));
            }
        }
    }
    
    public function clearLog(){
        $log_num = $this->config['log_num'];
        $log_path = $this->config['log_path'];
        dd($log_path);
        glob($log_path);
    }
}
