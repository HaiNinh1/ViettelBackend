<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Contract;

echo "=== Direct Import Test ===\n\n";

$filePath = 'testfile/danh_sach_hop_dong_kinh_doanh_2026-01-16.xlsx';
$spreadsheet = IOFactory::load($filePath);
$worksheet = $spreadsheet->getActiveSheet();

// Get headers from row 5
$headers = [];
$headerRow = $worksheet->getRowIterator(5, 5)->current();
foreach ($headerRow->getCellIterator() as $cell) {
    $col = $cell->getColumn();
    $val = $cell->getValue();
    if ($val !== null) {
        // Keep as-is (with Vietnamese characters)
        $headers[$col] = strtolower(preg_replace('/\s+/', '_', trim($val)));
    }
}

echo "Headers: " . implode(', ', array_slice($headers, 0, 8)) . "...\n\n";

// Process data rows starting from row 6
$successCount = 0;
$skipCount = 0;
$errorCount = 0;

foreach ($worksheet->getRowIterator(6) as $row) {
    $rowIndex = $row->getRowIndex();
    $data = [];
    
    foreach ($row->getCellIterator() as $cell) {
        $col = $cell->getColumn();
        if (isset($headers[$col])) {
            $data[$headers[$col]] = $cell->getValue();
        }
    }
    
    // Get contract number - with Vietnamese column name
    $contractNumber = $data['số_hợp_đồng'] ?? null;
    
    if (empty($contractNumber)) {
        $skipCount++;
        continue;
    }
    
    // Check if exists
    if (Contract::where('contract_number', $contractNumber)->exists()) {
        echo "Row $rowIndex: SKIP duplicate - $contractNumber\n";
        $skipCount++;
        continue;
    }
    
    try {
        $contract = Contract::create([
            'contract_number' => $contractNumber,
            'classification' => $data['phân_loại'] ?? null,
            'industry' => $data['ngành_nghề'] ?? null,
            'project_name' => $data['tên_dự_án'] ?? null,
            'status' => 'active',
        ]);
        
        $successCount++;
        if ($successCount <= 5) {
            echo "Row $rowIndex: OK - $contractNumber\n";
        }
        
    } catch (\Exception $e) {
        $errorCount++;
        echo "Row $rowIndex: ERROR - {$e->getMessage()}\n";
    }
}

echo "\n=== Results ===\n";
echo "Success: $successCount\n";
echo "Skipped: $skipCount\n";
echo "Errors: $errorCount\n";
echo "Total in DB: " . Contract::count() . "\n";

// Show sample
echo "\nSample contracts:\n";
Contract::take(3)->get(['id', 'contract_number', 'project_name'])->each(function($c) {
    echo "  {$c->id}: {$c->contract_number} | " . substr($c->project_name ?? 'N/A', 0, 30) . "\n";
});
