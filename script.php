<?php

require_once 'vendor/autoload.php';

use Volt\Services\Excel;
use Volt\Services\Commission;
use Volt\Models\User;

$arguments_file_name = isset($argv[1]) ? $argv[1] : null;
$csv_file = $arguments_file_name ? $arguments_file_name : 'example.csv';
$excel = new Excel($csv_file);
$commission = new Commission($excel);
$commission->processDataForCommission();
$comission_list = $commission->getResult();

foreach ($comission_list as $commission) {
	echo $commission . "\r\n";
}
