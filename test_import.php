<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Imports\ContractImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

echo "=== Testing Excel Import ===\n\n";

$filePath = 'testfile/danh_sach_hop_dong_kinh_doanh_2026-01-16.xlsx';

echo "File: $filePath\n\n";

// Enable query logging
DB::enableQueryLog();

$countBefore = \App\Models\Contract::count();
echo "Before: $countBefore contracts\n";

try {
    $import = new ContractImport();
    Excel::import($import, $filePath);
    
    $countAfter = \App\Models\Contract::count();
    echo "After: $countAfter contracts\n";
    echo "Imported: " . ($countAfter - $countBefore) . "\n";
    
    // Show sample queries
    $queries = DB::getQueryLog();
    echo "\nTotal queries: " . count($queries) . "\n";
    
    if (count($queries) > 0) {
        echo "First INSERT query:\n";
        foreach ($queries as $q) {
            if (strpos($q['query'], 'INSERT') !== false) {
                echo substr($q['query'], 0, 200) . "...\n";
                break;
            }
        }
    }
    
} catch (\Exception $e) {
    echo "\n=== ERROR ===\n";
    echo "Type: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n\n";
    
    // Get last query
    $queries = DB::getQueryLog();
    if (count($queries) > 0) {
        $last = end($queries);
        echo "Last query: " . substr($last['query'], 0, 300) . "\n";
    }
}
