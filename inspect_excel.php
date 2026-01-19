<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'testfile/danh_sach_hop_dong_kinh_doanh_2026-01-16.xlsx';
$spreadsheet = IOFactory::load($filePath);
$worksheet = $spreadsheet->getActiveSheet();

echo "=== All Headers from Row 5 ===\n\n";

// Get headers from row 5
$headerRow = $worksheet->getRowIterator(5, 5)->current();
$index = 1;
foreach ($headerRow->getCellIterator() as $cell) {
    $col = $cell->getColumn();
    $val = $cell->getValue();
    if ($val !== null) {
        $snakeCase = strtolower(preg_replace('/\s+/', '_', trim($val)));
        echo sprintf("%2d. Column %s: '%s' => '%s'\n", $index, $col, $val, $snakeCase);
        $index++;
    }
}

echo "\n\n=== Sample Data Row 6 ===\n\n";

// Get data from row 6
$dataRow = $worksheet->getRowIterator(6, 6)->current();
foreach ($dataRow->getCellIterator() as $cell) {
    $col = $cell->getColumn();
    $val = $cell->getValue();
    if ($val !== null) {
        echo "Column $col: " . substr((string)$val, 0, 40) . "\n";
    }
}
