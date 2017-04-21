<?php
namespace Framework\Core;

use Whoops\Run as Whoops;
use Whoops\Handler\PrettyPageHandler;

class App
{
    /**
     * 框架初始化
     */
    public static function init()
    {
        self::load_config(); //加载配置文件
        self::initWhoops(); //加载错误提示

        self::run(new Route());
    }

    /**
     * 启动框架
     * @param Route $router
     */
    private static function run(Route $router)
    {
        $router->dispatch(); //路由分发
    }

    /**
     * 初始加载配置文件
     */
    private static function load_config()
    {
        global $app_config;
        $app_config = [];
        $config_dir = opendir(CONF_PATH);
        while (($file = readdir($config_dir)) !== false) {
            if ($file == '.' || $file == '..' || is_dir($file)) {
                continue;
            } else {
                $app_config[basename($file, '.php')] = require CONF_PATH . $file;
            }
        }
    }

    private static function initWhoops()
    {
        $whoops = new Whoops();
        $whoops->pushHandler(new PrettyPageHandler);
        $whoops->register();
    }

}