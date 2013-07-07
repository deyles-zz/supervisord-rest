<?php

require_once('Liaison/util/Configuration.php');
require_once('SupervisorRestAPI.php');

use Liaison\utils\Configuration;

$configuration = new Configuration(
    array(
        'host' => 'yourdomain.com',
        'port' => '9001',
        'username' => 'test',
        'password' => 'test'
    )
);

$app = new SupervisorRestAPI($configuration);
$composer = new SupervisorRestAPIDecorator();
$composer->decorate($app);
$app->run();