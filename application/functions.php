<?php
function first_function()
{
    echo 'the first function';
}

/**
 * 读取配置,当配置项不存在则时返回 $default
 * @param string $key
 * @param null $default
 * @return bool
 */
function config($key = '', $default = null)
{
    if (!$key) return false;

    global $app_config;

    $config_keys = explode('.', $key);

    $data = $app_config;
    foreach ($config_keys as $k => $v) {
        if (!isset($data[$v])) return $default; //判断是否存在该配置项
        $data = $data[$v];
    }

    return $data;
}