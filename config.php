<?php

define('DIR_ROOT', dirname(__FILE__));
define('ENVIRONMENT_FILE', DIR_ROOT . '/.environment');

if(isset($_ENV['DATABASE_DRIVER']) && isset($_ENV['DATABASE_HOST'])){
    $params = $_ENV;
}else{
    if (!file_exists(ENVIRONMENT_FILE)) die('File "' . ENVIRONMENT_FILE . '" not exist. Please create file.');
    $params = parse_ini_file(ENVIRONMENT_FILE, false, INI_SCANNER_RAW);
}

$requiredParams = array(
    'DATABASE_DRIVER',
    'DATABASE_ENCODING',
    
    'DATABASE_HOST',
    'DATABASE_PORT',
    'DATABASE_NAME',
    'DATABASE_USER',
    'DATABASE_PASSWORD',
    'DATABASE_DESCRIPTION',
    
    'DATABASE_HOST_EXT',
    'DATABASE_PORT_EXT',
    'DATABASE_NAME_EXT',
    'DATABASE_USER_EXT',
    'DATABASE_PASSWORD_EXT',
    'DATABASE_DESCRIPTION_EXT',
);

array_map(function ($name) use ($params) {
    if (!isset($params[$name])) {
        die('Param ' . $name . ' not set in file ' . ENVIRONMENT_FILE);
    }else{
        define($name, $params[$name]);
    }
}, $requiredParams);
