<?php
$data = getInput();

$last_time = $data[0][0];
$last_long = 0;
$last_night = isNight($last_time);
$slow_time = 0;
$total_long = 0;
unset($data[0]);
foreach ($data as $value) {
    $time = $value[0];
    $long = $value[1];
    $diff_time = $time - $last_time;
    $diff_long = $long - $last_long;
    $is_slow = $diff_long / $diff_time * 60 * 60 <= 10;
    $is_night = isNight($time);
    if ($is_slow) {
        $slow_time += $time * ($is_night && $last_night ? 1.25 : 1);
    } else {
        $total_long += $long * ($is_night && $last_night ? 1.25 : 1);
    }
    $last_long = $long;
    $last_time = $time;
    $last_night = $is_night;
}

$fare_sum = 410;
$fare_sum += floor($slow_time / 90) * 90 * 80;
if ($total_long > 1052) $fare_sum += floor(($total_long - 1052) / 237) * 80;
echo $fare_sum;

function getInput()
{
    $tmpArr = [];
    while ($line = fgets(STDIN)) {
        $tmpArr[] = trim($line);
    }

    $inputs = [];
    $last_time = 0;
    foreach ($tmpArr as $value) {
        $tmp = explode(' ', $value);
        if (count($tmp) !== 2) exit(1);
        if (!preg_match("/\d{2}:\d{2}:\d{2}\.\d{3}/", $tmp[0]) || !preg_match("/\d{1,2}\.\d/", $tmp[1])) exit(1);
        $time = time2int($tmp[0]);
        if ($time < $last_time) exit(1);
        $long = (float)$tmp[1];
        $inputs[] = [$time, $long];
    }
    return $inputs;
}

function time2int($time)
{
    $time = explode(":", $time);
    return ($time[0] * 60 + $time[1]) * 60 + $time[2];
}

function isNight($time)
{
    $timestamp = ($time * 1000) % (24 * 60 * 60) / 1000;//切り捨て回避
    if (0 <= $timestamp && $timestamp < 5 * 60 * 60) return true;
    if (22 * 60 * 60 <= $timestamp && $timestamp < 24 * 60 * 60) return true;
    return false;
}