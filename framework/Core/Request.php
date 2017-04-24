<?php
namespace Framework\Core;

class Request
{
    private static $request;

    public $action; //当前实例化的控制器执行的方法名
    public $controller; //当前实例化的控制器

    public $url; //当前请求url 不包含参数
    public $fullUrl; //完整的请求url
    public $method; //当前请求类型

    public $defaultFilter; //默认过滤方法

    public static function instance()
    {
        if (!isset(self::$request)) {
            self::$request = new self;
        }

        return self::$request;
    }

    public function __construct()
    {
        $this->setUrl();
        $this->setMethod();
        $this->defaultFilter = config('default_filter');
    }

    /**
     * 设置当前请求方法
     */
    private function setMethod()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 设置当前请求地址
     */
    private function setUrl()
    {
//        $fullUrl = $_SERVER['REQUEST_URI'];
//
//        if (strpos($fullUrl, $_SERVER['SCRIPT_NAME']) === 0) {
//            $uri = substr($fullUrl, strlen($_SERVER['SCRIPT_NAME']));
//        } elseif (strpos($fullUrl, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
//            $uri = substr($fullUrl, strlen(dirname($_SERVER['SCRIPT_NAME'])));
//        } else {
//            $uri = $fullUrl;
//        }
//
//        $uri = parse_url($uri, PHP_URL_PATH); //过滤当前请求的参数
//
//        $this->url = ($uri == '/' || empty($uri)) ? '/' : str_replace(array('//', '../'), '/', trim($uri, '/'));
//        $this->fullUrl = $fullUrl;

        $fullUrl = $_SERVER['REQUEST_URI'];
        $this->fullUrl = $fullUrl;
        $url = parse_url($fullUrl, PHP_URL_PATH);
        $this->url = ($url == '/' || empty($url)) ? '/' : str_replace(array('//', '../'), '/', trim($url, '/'));
    }

    /**
     * 返回所有请求参数
     * @return mixed
     */
    public function all()
    {
        $data = $_REQUEST;
        if (is_object($data)) return $data;

        $params['filters'] = explode(',', $this->defaultFilter);
        array_walk_recursive($data, [$this, 'filter'], $params);
        reset($data);

        return $data;
    }

    /**
     * 获取所有请求类型中的指定参数值
     * @param null $name 参数名
     * @param null $default 默认值
     * @param null $filters 过滤方式
     * @return mixed
     */
    public function input($name = null, $default = null, $filters = null)
    {
        if (empty($name)) return false;

        $data = $_REQUEST;
        $keys = explode('.', $name);
        foreach ($keys as $key) {
            $data = $data[$key];
        }
        if (is_object($data) || $filters === false) return $data;

        if (empty($filters)) $filters = $this->defaultFilter;
        $filters = is_string($filters) ? explode(',', $filters) : $filters; //将过滤方法转为数组

        $params['filters'] = $filters;

        if (is_array($data)) {
            array_walk_recursive($data, [$this, 'filter'], $params);
            reset($data);
        } else {
            $data = self::filter($data, $default, $params);
        }

        return empty($data) ? $default : $data;
    }

    /**
     * 过滤数据
     * @param null $value
     * @param mixed $key
     * @param array $params
     * @return mixed
     */
    protected function filter($value = null, $key = 0, $params = [])
    {
        foreach ($params['filters'] as $filter) {
            if (is_callable($filter)) {
                $value = call_user_func($filter, $value);
            }
        }

        return $value;
    }

    /**
     * 设置当前的控制器
     * @param string $controller
     */
    public function setController($controller = '')
    {
        $this->controller = $controller;
    }

    /**
     * 设置当前操作名
     * @param string $action
     */
    public function setAction($action = '')
    {
        $this->action = $action;
    }

    /**
     * 检测是否使用手机访问
     * @author ThinkPHP
     * @return bool
     */
    public function isMobile()
    {
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取客户端IP地址
     * @param integer   $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean   $adv 是否进行高级模式获取（有可能被伪装）
     * @author ThinkPHP
     * @return mixed
     */
    public function ip($type = 0, $adv = false)
    {
        $type      = $type ? 1 : 0;
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }

        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim(current($arr));
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? [$ip, $long] : ['0.0.0.0', 0];
        return $ip[$type];
    }
}