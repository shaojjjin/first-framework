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
        global $global_config;

        $global_config_path = CONF_PATH . 'config.php';
        if (!file_exists($global_config_path)) {
            $default_conf = <<<eof
<?php
return [

];
eof;
            file_put_contents($global_config_path, $default_conf);
        } else {
            $config = require $global_config_path;
            if (isset($config['load_ext']) && is_array($config['load_ext'])) {
                foreach ($config['load_ext'] as $ext_name) {
                    $config[$ext_name] = require CONF_PATH . $ext_name . '.php';
                }
            }
        }
        $global_config = $config;
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