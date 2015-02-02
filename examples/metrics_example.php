<?php

require __DIR__ . '/../vendor/autoload.php';

use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\MemoryPeakUsageProcessor;

$log = new Logger('Logger.Metrics');

$webProcessor = new \Monolog\Processor\WebProcessor();
$webProcessor->addExtraField('user_agent', 'HTTP_USER_AGENT');
$log->pushProcessor($webProcessor);
$log->pushProcessor(new MemoryPeakUsageProcessor(true, false));

$fileHandler = new StreamHandler(__DIR__ . '/../logs/logstash_example.log');
$formatter   = new LogstashFormatter('logstash_example', null, null, 'c_', LogstashFormatter::V1);
$fileHandler->setFormatter($formatter);
$log->pushHandler($fileHandler);

$log->info('Metrics',
    [
        'request_type'    => 'search_result',
        'request_time_ms' => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3) * 1000
    ]
);
