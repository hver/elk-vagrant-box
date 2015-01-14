<?php
require __DIR__ . '/../vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

// Create new logging channel
$log = new Logger('Logger.example');

// Create a handler which writes in the console.
// This handler only log messages with importance levels higher than Logger::WARNING
$cliHandler = new StreamHandler("php://stdout", Logger::NOTICE);

// Add the console handler to logger
$log->pushHandler($cliHandler);

// Add an error message to log.
$log->error('Bad data');
// First parameter is the message, second is an array with additional info
$log->warning('Processing took too long', ['time' => time()]);
// Because of the minimal log level, this debug will be ignored
$log->debug('Who reads debug log?');
