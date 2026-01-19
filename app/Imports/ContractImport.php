<?php

namespace App\Imports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Carbon\Carbon;

class ContractImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    private $rowCount = 0;
    private $successCount = 0;
    
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowCount++;
        
        // Maatwebsite Excel converts headers to ASCII snake_case:
        // 'Phân loại' => 'phan_loai'
        // 'Số hợp đồng' => 'so_hop_dong'
        // 'Ngành nghề' => 'nganh_nghe'
        // etc.
        
        // Get contract number
        $contractNumber = $row['so_hop_dong'] ?? null;
        
        if (empty($contractNumber)) {
            return null;
        }
        
        // Skip duplicates
        if (Contract::where('contract_number', $contractNumber)->exists()) {
            return null;
        }

        $this->successCount++;

        return new Contract([
            'contract_number' => $contractNumber,
            'classification' => $row['phan_loai'] ?? null,
            'industry' => $row['nganh_nghe'] ?? null,
            'project_name' => $row['ten_du_an'] ?? null,
            'signing_date' => $this->parseDate($row['ngay_ky'] ?? null),
            'start_date' => $this->parseDate($row['ngay_hieu_luc'] ?? null),
            'end_date' => $this->parseDate($row['ngay_ket_thuc'] ?? null),
            'extension_date' => $this->parseDate($row['ngay_gia_han'] ?? null),
            'duration_days' => $this->parseNumber($row['thoi_gian_thuc_hien'] ?? null),
            'contract_content' => $row['noi_dung_hop_dong'] ?? null,
            'contract_value' => $this->parseNumber($row['gia_tri_hop_dong'] ?? null),
            'adjusted_value' => $this->parseNumber($row['gia_tri_sau_thue'] ?? null),
            'approval_status' => $row['phe_duyet'] ?? null,
            'value_difference' => $this->parseNumber($row['chenh_lech'] ?? null),
            'investor' => $row['chu_dau_tu'] ?? null,
            'contract_status' => $row['trang_thai'] ?? null,
            'condition_status' => $row['tinh_trang'] ?? null,
            'legal_entity' => $row['phap_nhan'] ?? null,
            'advance_payment' => $row['tam_ung'] ?? null,
            'notes' => $row['ghi_chu_tam_ung'] ?? null,
            'appendix_number' => $row['so_phu_luc'] ?? null,
            'revision_count' => $this->parseNumber($row['so_lan_thanh_toan'] ?? 0),
            'extension_count' => $this->parseNumber($row['so_lan_gia_han'] ?? 0),
            'status' => 'active',
        ]);
    }

    /**
     * Specify the row number where headers are located
     */
    public function headingRow(): int
    {
        return 5;
    }

    /**
     * Parse date from Excel serial or Vietnamese format
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Handle Excel date serial number
            if (is_numeric($date)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date));
            }
            
            // Handle dd/mm/yyyy format
            if (is_string($date) && strpos($date, '/') !== false) {
                return Carbon::createFromFormat('d/m/Y', $date);
            }

            return Carbon::parse($date);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse number with various formats
     */
    private function parseNumber($value)
    {
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $cleaned = preg_replace('/[^\d.-]/', '', $value);
            return is_numeric($cleaned) ? (float) $cleaned : null;
        }

        return null;
    }
}
