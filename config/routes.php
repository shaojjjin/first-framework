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
        'Closure' => ['get', function () {
            echo 'Closure Route Test Success!';
        }],
        'post' => ['post', function () {
            echo 'test post!';
        }]
    ],
    'group' => [
        'dash' => [
            '/' => ['get', 'DashController@index'],
            'Closure' => ['get', function () {
                echo 'Group Route Closure Test Success!';
            }]
        ]
    ],
];