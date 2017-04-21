<?php
namespace Framework\Core;

class App 
{

    public static function init(Route $router)
    {
        var_dump($router->uri);
        var_dump($router->routes);
        $router->dispatch(); //路由分发
//        self::run();
    }

    public static function run()
    {
        require ROOT_PATH . 'route.php';
        
        Route::dispatch();
    }
}