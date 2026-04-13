<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$db = app('db');
$columns = $db->select('DESCRIBE loans');
foreach($columns as $col){
    echo $col->Field . ' (' . $col->Type . ')' . PHP_EOL;
}
?>
