<?php

require __DIR__.'/app/autoload.php';
$app = new \MFS\Bliki\App();

require 'AppServer/autoload.php';
$handler = new \MFS\AppServer\SCGI\Handler('tcp://127.0.0.1:9999');

// require $root.'/Middleware/PHP_Compat/autoload.php';
// $app = new \MFS\AppServer\Middleware\PHP_Compat\PHP_Compat($app);

$handler->serve($app);
