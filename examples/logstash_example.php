<?php
require __DIR__ . '/../vendor/autoload.php';

use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$fileHandler = new StreamHandler(__DIR__ . '/../logs/logstash_example.log', Logger::NOTICE);
$formatter   = new LogstashFormatter('logstash_example', null, null, 'c_', LogstashFormatter::V1);
$fileHandler->setFormatter($formatter);

$log = new Logger('Logger.Example');

$webProcessor = new \Monolog\Processor\WebProcessor();
$webProcessor->addExtraField('user_agent', 'HTTP_USER_AGENT');

$log->pushHandler($fileHandler);
$log->pushProcessor($webProcessor);
$log->pushProcessor(new \Monolog\Processor\IntrospectionProcessor());

$log->warning('Suspicious input');
