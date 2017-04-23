<?php
namespace App\Http\Controller;

class IndexController
{
    public function index()
    {
        var_dump(request());
        echo 'hello world!';
    }
}