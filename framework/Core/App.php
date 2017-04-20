<?php
namespace Framework\Core;

class App 
{
    public static function init()
    {
        self::run();
    }

    public static function run()
    {
        require ROOT_PATH . 'route.php';
        
        Route::dispatch();
    }
}