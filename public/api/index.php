<?php

declare(strict_types=1);

use App\Infrastructure\Config\Configuration;
use App\Infrastructure\Container;
use App\Infrastructure\Http\Request;

$projectRoot = dirname(__DIR__, 2);

require_once $projectRoot . '/src/autoload.php';

$container = new Container(new Configuration($projectRoot));
$container->session()->start();

$container->kernel()->handle(Request::capture())->send();
