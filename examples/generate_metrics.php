<?php
require __DIR__ . '/../vendor/autoload.php';

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$fileHandler = new StreamHandler(__DIR__ . '/../logs/logstash_example.log');
$formatter   = new LineFormatter("%message%\n");
$fileHandler->setFormatter($formatter);

$log = new Logger('LogstashLogger');
$log->pushHandler($fileHandler);

$faker = Faker\Factory::create();

for ($i = 0; $i < 200; $i++) {
    $seconds = purebell(-3600 * 8, 3600 * 16, 60 * 24, 1);
    if (!$seconds < 0) {
        continue;
    }
    $memoryPeak = rand(1000000, 2500000);
    $ms         = rand(20, 50);
    $logMessage = getLogMessage($faker, $seconds, $memoryPeak, $ms, '404_page');
    $log->info($logMessage);
}

for ($i = 0; $i < 600; $i++) {
    $seconds = purebell(-3600 * 12, 3600 * 12, 600 * 24, 1, 600 * 24, 1);
    if (!$seconds < 0) {
        continue;
    }
    $memoryPeak = rand(10000000, 40000000);
    $ms         = rand(1000, 4000);
    $logMessage = getLogMessage($faker, $seconds, $memoryPeak, $ms, 'search_request');
    $log->info($logMessage);
}

for ($i = 0; $i < 400; $i++) {
    $seconds    = purebell(3600 * 12, 3600 * 36, 600 * 24, 1);
    $memoryPeak = rand(10000000, 40000000);
    $ms         = rand(1000, 4000);
    $logMessage = getLogMessage($faker, $seconds, $memoryPeak, $ms, 'search_request');
    $log->info($logMessage);
}

for ($i = 0; $i < 5; $i++) {
    $seconds    = rand(20 * 3600, 24 * 3600);
    $memoryPeak = rand(60000000, 160000000);
    $ms         = rand(5000, 16000);
    $logMessage = getLogMessage($faker, $seconds, $memoryPeak, $ms, 'search_request');
    $log->info($logMessage);
}

for ($i = 0; $i < 800; $i++) {
    $seconds    = purebell(3600 * 12, 3600 * 36, 500 * 24, 1);
    $memoryPeak = rand(3000000, 6000000);
    $ms         = rand(30, 100);
    $logMessage = getLogMessage($faker, $seconds, $memoryPeak, $ms, 'landing_page');
    $log->info($logMessage);
}

for ($i = 0; $i < 1200; $i++) {
    $seconds = purebell(-3600 * 12, 3600 * 12, 600 * 24, 1);
    if (!$seconds < 0) {
        continue;
    }
    $memoryPeak = rand(3000000, 6000000);
    $ms         = rand(30, 100);
    $logMessage = getLogMessage($faker, $seconds, $memoryPeak, $ms, 'landing_page');
    $log->info($logMessage);
}

for ($i = 0; $i < 500; $i++) {
    $seconds    = rand(0, 3600 * 24);
    $memoryPeak = rand(1500000, 3900000);
    $ms         = rand(100, 2500);
    $logMessage = getLogMessage($faker, $seconds, $memoryPeak, $ms, 'ajax_api');
    $log->info($logMessage);
}

/**
 * from http://www.eboodevelopment.com/php-random-number-generator-with-normal-distribution-bell-curve/
 *
 * @param     $min
 * @param     $max
 * @param     $std_deviation
 * @param int $step
 *
 * @return float
 */
function purebell($min, $max, $std_deviation, $step = 1)
{
    $rand1           = (float)mt_rand() / (float)mt_getrandmax();
    $rand2           = (float)mt_rand() / (float)mt_getrandmax();
    $gaussian_number = sqrt(-2 * log($rand1)) * cos(2 * M_PI * $rand2);
    $mean            = ($max + $min) / 2;
    $random_number   = ($gaussian_number * $std_deviation) + $mean;
    $random_number   = round($random_number / $step) * $step;
    if ($random_number < $min || $random_number > $max) {
        $random_number = purebell($min, $max, $std_deviation);
    }

    return (int)round($random_number);
}

/**
 * @param $faker
 * @param $seconds
 * @param $levelIso
 *
 * @return array
 */
function getLogMessage(Faker\Generator $faker, $seconds, $memoryPeak, $ms, $type)
{
    if (rand(0, 10) == 4) {
        $userAgent = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
    } else {
        $userAgent = $faker->userAgent;
    }
    @$template = [
        "@timestamp"        => date('Y-m-d\TH:i:s.uP', time() - $seconds),
        '@version'          => 1,
        "host"              => "vagrant-ubuntu-trusty-64",
        "message"           => 'Metrics',
        "type"              => "metrics_example",
        "channel"           => "Logger.Metrics",
        "level"             => "INFO",
        "url"               => "www.example.com/{$faker->state}/{$faker->firstName}",
        "ip"                => $faker->ipv4,
        "http_method"       => "GET",
        "server"            => "localhost",
        "referrer"          => null,
        "user_agent"        => $userAgent,
        "memory_peak_usage" => $memoryPeak,
        "c_request_type"    => $type,
        "c_request_time_ms" => $ms
    ];

    return json_encode($template);
}


