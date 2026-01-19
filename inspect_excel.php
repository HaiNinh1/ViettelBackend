<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'testfile/danh_sach_hop_dong_kinh_doanh_2026-01-16.xlsx';

echo "=== Inspecting Excel File ===\n\n";

$spreadsheet = IOFactory::load($filePath);
$worksheet = $spreadsheet->getActiveSheet();

// Get first 5 rows to see the structure
echo "First 5 rows:\n";
echo str_repeat("-", 100) . "\n";

foreach ($worksheet->getRowIterator(1, 5) as $row) {
    $rowIndex = $row->getRowIndex();
    $cells = [];
    
    foreach ($row->getCellIterator() as $cell) {
        $col = $cell->getColumn();
        $val = $cell->getValue();
        if ($val !== null) {
            $cells[] = "$col: " . substr((string)$val, 0, 25);
        }
    }
    
    echo "Row $rowIndex: " . implode(" | ", array_slice($cells, 0, 6)) . "\n";
}

echo "\n\nHeaders in row 1:\n";
$headerRow = $worksheet->getRowIterator(1, 1)->current();
$headers = [];
foreach ($headerRow->getCellIterator() as $cell) {
    $headers[$cell->getColumn()] = $cell->getValue();
}

foreach ($headers as $col => $header) {
    if ($header !== null) {
        echo "  Column $col: '$header'\n";
    }
}

echo "\n\nHeaders in row 2:\n";
$headerRow2 = $worksheet->getRowIterator(2, 2)->current();
$headers2 = [];
foreach ($headerRow2->getCellIterator() as $cell) {
    $headers2[$cell->getColumn()] = $cell->getValue();
}

foreach ($headers2 as $col => $header) {
    if ($header !== null) {
        echo "  Column $col: '$header'\n";
    }
}
