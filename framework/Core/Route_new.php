<?php
namespace Framework\Core;

class NewRoute 
{
    public static $routes    = []; //路由配置数组
    public static $methods   = []; //http访问方式
    public static $callbacks = []; //对应的回调操作
    public static $error_callback; 
}