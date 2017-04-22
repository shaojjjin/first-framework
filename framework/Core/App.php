<?php
namespace Framework\Core;

use Whoops\Run as Whoops;
use Whoops\Handler\PrettyPageHandler;
use Illuminate\Database\Capsule\Manager as Capsule;

class App
{
    /**
     * 框架初始化
     */
    public static function init()
    {
        self::load_config(); //加载配置文件
        self::initWhoops(new Whoops()); //加载错误提示
        self::initDatabase(new Capsule()); //初始化Eloquent
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
        $global_config_path = CONF_PATH . 'config.php';
        if (file_exists($global_config_path)) {
            $_config = require $global_config_path;
        } else {
//            file_put_contents($global_config_path, var_export([]), true);
        }
        exit;
        $config_dir = opendir(CONF_PATH);
        while (($file = readdir($config_dir)) !== false) {
            if ($file == '.' || $file == '..' || is_dir($file)) {
                continue;
            } else {
                $app_config[basename($file, '.php')] = require CONF_PATH . $file;
            }
        }
    }

    /**
     * @param Whoops $whoops
     */
    private static function initWhoops(Whoops $whoops)
    {
        if (APP_DEBUG === true) {
            $whoops->pushHandler(new PrettyPageHandler)->register();
        }
    }

    /**
     * @param Capsule $capsule
     */
    private static function initDatabase(Capsule $capsule)
    {
        $capsule->addConnection(config('database'));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}