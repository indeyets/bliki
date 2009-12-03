<?php
namespace MFS\Bliki;

function autoload($class_name)
{
    static $files = null;

    if (null === $files) {
        $root = __DIR__.'/';

        $files = array(
            'MFS\Bliki\App'             => $root.'App.class.php',
            'MFS\Bliki\BlogHandler'     => $root.'space handlers/BlogHandler.class.php',
            'MFS\Bliki\ErrorHandler'    => $root.'space handlers/ErrorHandler.class.php',

            'MFS\Bliki\RuntimeException'    => $root.'exceptions.php',
            'MFS\Bliki\Error404Exception'   => $root.'exceptions.php',
        );
    }

    if (isset($files[$class_name]))
        require $files[$class_name];
}

spl_autoload_register('MFS\Bliki\autoload');

// add symfony-templater
require realpath(__DIR__.'/..').'/libraries/templating/lib/sfTemplateAutoloader.php';
\sfTemplateAutoloader::register();

// yaml-handler
require realpath(__DIR__.'/..').'/libraries/yaml/yaml.php';
