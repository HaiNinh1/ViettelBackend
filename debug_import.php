<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TestImport implements ToArray, WithHeadingRow
{
    public function headingRow(): int
    {
        return 5;
    }
    
    public function array(array $rows)
    {
        echo "=== Import Test ===\n\n";
        echo "Total rows: " . count($rows) . "\n\n";
        
        if (count($rows) > 0) {
            echo "Headers (keys of first row):\n";
            $keys = array_keys($rows[0]);
            foreach ($keys as $i => $key) {
                echo sprintf("%2d. '%s'\n", $i+1, $key);
            }
            
            echo "\n\nFirst row values:\n";
            foreach ($rows[0] as $key => $value) {
                if ($value !== null) {
                    echo "  $key: " . substr((string)$value, 0, 40) . "\n";
                }
            }
        }
    }
}

$filePath = 'testfile/danh_sach_hop_dong_kinh_doanh_2026-01-16.xlsx';
Excel::import(new TestImport(), $filePath);
