<?php
namespace App\Http\Controller;

class IndexController
{
    public function index()
    {
        $request = request();
        var_dump($request);

        echo 'hello world!';
    }
}