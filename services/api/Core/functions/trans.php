<?php

use CloudCastle\Core\Lang;

/**
 * @param string $key
 * @param array|null $params
 * @param string|null $lang
 * @return string
 */
function trans(string $key, array|null $params = [], string|null $lang = APP_LANG): string
{
    $instance = Lang::getInstance();
    
    if(!$instance->checkInit()) {
        $instance->init(APP_ROOT . '/resources/lang/' . $lang);
    }
    
    if($message = $instance->get($key)){
        $keys = array_keys($params);
        $values = array_values($params);
        
        return str_replace($keys, $values, $message);
    }
    
    return $key;
}