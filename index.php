<?php

require __DIR__.'/vendor/autoload.php';

use App\Application;
use App\Service\Context;
use GuzzleHttp\Client;

$httpClient = new Client();
$context = new Context();
$context->setHttpClient($httpClient);
$app = new Application($context);

echo $app->run($_GET);