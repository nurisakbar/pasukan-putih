<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking indexes on pasiens table:\n";
$indexes = DB::select("SHOW INDEX FROM pasiens WHERE Key_name LIKE 'idx_%'");
foreach ($indexes as $index) {
    echo "- {$index->Key_name} on column {$index->Column_name}\n";
}

echo "\nChecking indexes on villages table:\n";
$villageIndexes = DB::select("SHOW INDEX FROM villages WHERE Key_name LIKE '%district%'");
foreach ($villageIndexes as $index) {
    echo "- {$index->Key_name} on column {$index->Column_name}\n";
}

echo "\nChecking indexes on districts table:\n";
$districtIndexes = DB::select("SHOW INDEX FROM districts WHERE Key_name LIKE '%regency%'");
foreach ($districtIndexes as $index) {
    echo "- {$index->Key_name} on column {$index->Column_name}\n";
}
