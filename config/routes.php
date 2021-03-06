<?php
/**
 * 路由配置表
 * User: Jasper
 * Date: 2017/4/21 0021
 * Time: 10:54
 */
return [
    'url' => [
        '/' => ['get', 'IndexController@index', 'name' => 'home_page'],
        'post' => ['post', function() {
            $data = [
                'status' => 1,
                'message' => 'test success!'
            ];
            echo json_encode($data);
        }, 'name' => 'test_post'],
    ],
    'group' => [
        'test' => [
            '/' => ['get', 'IndexController@index'],
            'Closure' => ['get', function () {
                var_dump($_SERVER['REQUEST_URI']);
                var_dump(request());
                echo 'Group Route Closure Test Success!';
            }]
        ]
    ],
];