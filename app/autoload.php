<?php
namespace MFS\Bliki;

function autoload($class_name)
{
    static $files = null;

    if (null === $files) {
        $root = __DIR__.'/';

        $files = array(
            'MFS\Bliki\App' => $root.'App.class.php',
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
