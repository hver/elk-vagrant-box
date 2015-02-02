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

for ($i = 0; $i < 1000; $i++) {
    $seconds    = (int)purebell(0, 3600, 600, 1);
    $memoryPeak = (int)purebell(30000000, 33000000, 500000, 1);
    $ms         = (int)purebell(1000, 4000, 500, 1);
    $logMessage = getLogMessage($faker, $seconds, $memoryPeak, $ms, 'search_request');
    $log->info($logMessage);
}

for ($i = 0; $i < 1000; $i++) {
    $seconds    = (int)purebell(0, 3600, 600, 1);
    $memoryPeak = (int)purebell(1000000, 2000000, 100000, 1);
    $ms         = (int)purebell(20, 50, 5, 1);
    $logMessage = getLogMessage($faker, $seconds, $memoryPeak, $ms, '404_page');
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

    return $random_number;
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
        "user_agent"        => $faker->userAgent,
        "memory_peak_usage" => $memoryPeak,
        "c_request_type"    => $type,
        "c_request_time_ms" => $ms
    ];

    return json_encode($template);
}


