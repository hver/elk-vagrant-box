<?php
require '../vendor/autoload.php';

use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Formatter\NormalizerFormatter;
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
    $levelIso   = getRandomLogLevel();
    $logMessage = getLogMessage($faker, $seconds, $levelIso, 'Logger.Main');
    $log->$levelIso($logMessage);
}

for ($i = 0; $i < 100; $i++) {
    $seconds    = (int)purebell(3000, 3300, 50, 1);
    $levelIso   = getRandomLogLevel();
    $logMessage = getLogMessage($faker, $seconds, $levelIso, 'Logger.Translator');
    $log->$levelIso($logMessage);
}

for ($i = 0; $i < 40; $i++) {
    $seconds    = (int)purebell(1000, 1060, 10, 1);
    $levelIso   = 'error';
    $logMessage = getLogMessage($faker, $seconds, $levelIso, 'Logger.Translator');
    $log->$levelIso($logMessage);
}
/**
 * @return string
 */
function getRandomLogLevel()
{
    $random = rand(0, 100);
    if ($random >= 0 && $random <= 60) {
        return 'debug';
    }
    if ($random > 60 && $random <= 75) {
        return 'notice';
    }
    if ($random > 75 && $random <= 88) {
        return 'info';
    }
    if ($random > 88 && $random <= 94) {
        return 'warning';
    }
    if ($random > 94 && $random <= 99) {
        return 'error';
    }
    if ($random > 99 && $random <= 100) {
        return 'critical';
    }
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
function getLogMessage(Faker\Generator $faker, $seconds, $levelIso, $channel)
{
    @$template = [
        "@timestamp"  => date('Y-m-d\TH:i:s.uP', time() - $seconds),
        "@version"    => 1,
        "host"        => "vagrant-ubuntu-trusty-64",
        "message"     => $faker->catchPhrase,
        "type"        => "logstash_example",
        "channel"     => $channel,
        "level"       => strtoupper($levelIso),
        "url"         => "www.example.com/{$faker->state}/{$faker->firstName}",
        "ip"          => $faker->ipv4,
        "http_method" => "GET",
        "server"      => "localhost",
        "referrer"    => null,
        "user_agent"  => $faker->userAgent,
        "file"        => "/logstash_example.php",
        "line"        => 120
    ];
    return json_encode($template);
}


