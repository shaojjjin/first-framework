<?php
/**
 * Created by PhpStorm.
 * User: Jasper
 * Date: 2017/4/21 0021
 * Time: 10:54
 */
return [
    'url' => [
        '/' => ['get', 'IndexController@index'],
        'post' => ['post', function() {
            $data = [
                'status' => 1,
                'message' => 'test success!'
            ];
            echo json_encode($data);
        }]
    ],
    'group' => [
        'test' => [
            '/' => ['get', 'IndexController@index'],
            'Closure' => ['get', function () {
                echo 'Group Route Closure Test Success!';
            }]
        ]
    ],
];