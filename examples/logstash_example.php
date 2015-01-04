<?php
require '../vendor/autoload.php';

use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$fileHandler = new StreamHandler(__DIR__ . '/../logs/logstash_example.log', Logger::NOTICE);
$formatter = new LogstashFormatter('logstash_example', null, null, 'c_', LogstashFormatter::V1);
$fileHandler->setFormatter($formatter);



$log = new Logger('LogstashLogger');
$log->pushHandler($fileHandler);
$log->pushProcessor(new \Monolog\Processor\WebProcessor());
$log->pushProcessor(new \Monolog\Processor\IntrospectionProcessor());

$log->warning('Suspicious input');
