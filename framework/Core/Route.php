<?php
namespace Framework\Core;

class Route
{
    public $uri; //当前访问
    public $routes = []; //路由配置数组

    /**
     * Route constructor.
     * 加载路由配置
     * @param array $routes
     */
    public function __construct($routes = [])
    {
        $this->uri = self::detect_uri();
        $this->routes = self::loader($routes);
    }

    /**
     * 获取当前访问的uri
     * inspired by CodeIgniter 2
     * @return string $uri
     */
    public static function detect_uri()
    {
        $uri = $_SERVER['REQUEST_URI'];

        if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
            $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
        } elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
            $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
        }

        if ($uri == '/' || empty($uri)) return '/';

        $uri = parse_url($uri, PHP_URL_PATH);
        return str_replace(array('//', '../'), '/', trim($uri, '/'));
    }

    /**
     * 加载路由配置
     * 处理完成后的路由配置数组
     *
     * $routes = [
     *     'GET' => [
     *          '/' => 'IndexController@index',
     *          'Closure' => function () {
     *              ......
     *          }
     *      ],
     *     'POST' => [],
     *      ......
     * ];
     *
     * @param array $routes_tmp 没有处理过的路由配置
     * @return array 处理完成的路由配置
     */
    public static function loader($routes_tmp = [])
    {
        $routes = [];

        foreach ($routes_tmp['url'] as $uri => $params) {
            $method = strtoupper($params[0]);
            $uri = self::handleUri($uri);
            $routes[$method][$uri] = $params[1];
        }

        foreach ($routes_tmp['group'] as $prefix => $item) {
            foreach ($item as $uri => $params) {
                $method = strtoupper($params[0]);
                $full_uri = self::handleUri($prefix . '/' . $uri);
                $routes[$method][$full_uri] = $params[1];
            }
        }

        return $routes;
    }

    /**
     * 对路由配置的uri进行格式化处理
     *
     * @param $uri
     * @return mixed
     */
    public static function handleUri($uri)
    {
        if ($uri == '/' || empty($uri)) return '/';
        if (substr($uri, -1, 1) === '/') $uri = substr($uri, 0, strlen($uri)-1);
        return $uri;
    }

    /**
     * 分发执行
     */
    public function dispatch()
    {
        $uri = $this->uri; //当前请求的地址
        $method = $_SERVER['REQUEST_METHOD']; //当前访问的方法
        $current_method_routes = $this->routes[$method]; //当前请求方法的路由表

        //判断当前请求是否存在与路由配置中
        $route_exist = false;
        if (in_array($uri, array_keys($current_method_routes))) {
            $route_exist = true;
            //判断是实例化控制器还是为闭包函数
            $callback = $current_method_routes[$uri];
            is_object($callback) === true ? $callback() : self::initController($callback);
        }

        if ($route_exist === false) self::errorCallBack();
    }

    /**
     * 实例化控制器
     * @param null $callback
     */
    public static function initController($callback = null)
    {
        $segments = explode('@', $callback);

        $controller_name = 'App\\Http\\Controller\\' . $segments[0];
        $function_name = $segments[1];

        //判断是否存在控制器
        if (class_exists($controller_name)) {
            $controller = new $controller_name();
            $controller->$function_name();
        } else {
            self::errorCallBack();
        }
    }

    /**
     * 处理错误
     */
    public static function errorCallBack()
    {

    }
}