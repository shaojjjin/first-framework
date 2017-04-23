<?php
namespace Framework\Core;

use ReflectionMethod;

class Route
{
    public $routes = []; //路由配置数组
    public static $methods = ['GET', 'POST', 'PUT', 'DELETE']; //支持的请求方式

    /**
     * Route constructor.
     * 加载路由配置
     */
    public function __construct()
    {
        $this->routes = self::loader(config('routes', []));
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

        if (isset($routes_tmp['url'])) {
            foreach ($routes_tmp['url'] as $uri => $params) {
                $method = strtoupper($params[0]);
                $uri = self::handleUri($uri);
                if ($method == 'ANY') {
                    foreach (self::$methods as $val) {
                        $routes[$val][$uri] = $params[1];
                    }
                } else {
                    $routes[$method][$uri] = $params[1];
                }
            }
        }

        if (isset($routes_tmp['group'])) {
            foreach ($routes_tmp['group'] as $prefix => $item) {
                foreach ($item as $uri => $params) {
                    $method = strtoupper($params[0]);
                    $full_uri = self::handleUri($prefix . '/' . $uri);
                    if ($method == 'ANY') {
                        foreach (self::$methods as $val) {
                            $routes[$val][$full_uri] = $params[1];
                        }
                    } else {
                        $routes[$method][$full_uri] = $params[1];
                    }
                }
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
     * @param string $uri 当前请求的地址
     */
    public function dispatch($uri = '')
    {
        $method = request()->method; //当前访问的方法
        $uri = empty($uri) ? request()->url : $uri;
        $current_method_routes = $this->routes[$method]; //当前请求方法的路由表

        //判断当前请求是否存在与路由配置中
        $route_exist = false;
        if (in_array($uri, array_keys($current_method_routes))) {
            $route_exist = true;
            //判断是实例化控制器还是为闭包函数
            $callback = $current_method_routes[$uri];
            is_object($callback) === true ? $callback() : self::initController($callback);
        }

        if ($route_exist === false) {
            self::errorCallBack(404, '该路由不存在！');
        }
    }

    /**
     * 实例化控制器
     * @param null $callback
     */
    protected static function initController($callback = null)
    {
        list($controller, $action) = explode('@', $callback);

        request()->setController($controller); //设置当前的控制器
        request()->setAction($action); //设置当前操作名

        $controller = 'App\\Http\\Controller\\' . $controller;

        //判断是否存在控制器
        if (method_exists($controller, $action)) {
            //构建反射
            $reflector = new ReflectionMethod($controller, $action);
            $parameters = [];
            foreach ($reflector->getParameters() as $key => $parameter) {
                $class = $parameter->getClass();
                if ($class) {
                    array_splice($parameters, $key, 0, [new $class->name]);
                }
            }
            call_user_func_array([new $controller(), $action], $parameters);
        } else {
            $error_msg = $controller . '类不存在！';
            self::errorCallBack(404, $error_msg);
        }
    }

    /**
     * 错误处理
     * @param int $code
     * @param string $message
     * @throws Exception
     */
    public static function errorCallBack($code = 500, $message = '')
    {
        if (APP_DEBUG === true) {
            throw new Exception($message, $code);
        } else {
            echo '404';
        }
    }
}