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
        
        // Excel headers have Vietnamese characters after snake_case conversion:
        // số_hợp_đồng, phân_loại, ngành_nghề, tên_dự_án, ngày_ký, etc.
        
        // Get contract number - with Vietnamese column name
        $contractNumber = $row['số_hợp_đồng'] ?? $row['so_hop_dong'] ?? null;
        
        // Skip if no contract number
        if (empty($contractNumber)) {
            return null;
        }
        
        // Skip duplicates
        if (Contract::where('contract_number', $contractNumber)->exists()) {
            return null;
        }

        $this->successCount++;
        \Log::info("Row {$this->rowCount}: Creating contract {$contractNumber}");

        return new Contract([
            'contract_number' => $contractNumber,
            'classification' => $row['phân_loại'] ?? $row['phan_loai'] ?? null,
            'industry' => $row['ngành_nghề'] ?? $row['nganh_nghe'] ?? null,
            'project_name' => $row['tên_dự_án'] ?? $row['ten_du_an'] ?? null,
            'signing_date' => $this->parseDate($row['ngày_ký'] ?? $row['ngay_ky'] ?? null),
            'start_date' => $this->parseDate($row['ngày_hiệu_lực'] ?? $row['ngay_hieu_luc'] ?? null),
            'end_date' => $this->parseDate($row['ngày_kết_thúc'] ?? $row['ngay_ket_thuc'] ?? null),
            'extension_date' => $this->parseDate($row['ngày_gia_hạn'] ?? $row['ngay_gia_han'] ?? null),
            'duration_days' => $this->parseNumber($row['thời_gian_thực_hiện'] ?? $row['thoi_gian'] ?? null),
            'contract_content' => $row['nội_dung_hợp_đồng'] ?? $row['noi_dung_hop'] ?? null,
            'contract_value' => $this->parseNumber($row['giá_trị_hợp_đồng'] ?? $row['gia_tri_hop'] ?? null),
            'adjusted_value' => $this->parseNumber($row['giá_trị_sau_thuế'] ?? $row['gia_tri_sau'] ?? null),
            'value_difference' => $this->parseNumber($row['chênh_lệch'] ?? $row['chenh_lech'] ?? null),
            'approval_status' => $row['phê_duyệt'] ?? $row['phe_duyet'] ?? null,
            'status' => 'active',
            'contract_status' => $row['trạng_thái'] ?? $row['trang_thai'] ?? null,
            'condition_status' => $row['tình_trạng'] ?? $row['tinh_trang'] ?? null,
            'investor' => $row['chủ_đầu_tư'] ?? $row['chu_dau_tu'] ?? null,
            'legal_entity' => $row['pháp_nhân'] ?? $row['phap_nhan'] ?? null,
            'advance_payment' => $row['tạm_ứng'] ?? $row['tam_ung'] ?? null,
            'notes' => $row['ghi_chú_tạm_ứng'] ?? $row['ghi_chu'] ?? null,
            'appendix_number' => $row['số_phụ_lục'] ?? $row['so_phu_luc'] ?? null,
            'revision_count' => $this->parseNumber($row['số_lần_thanh_toán'] ?? $row['so_lan'] ?? 0),
            'extension_count' => $this->parseNumber($row['số_lần_gia_hạn'] ?? $row['so_lan_gia'] ?? 0),
        ]);
    }

    /**
     * Specify the row number where headers are located
     */
    public function headingRow(): int
    {
        return 5; // Headers are on row 5
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
        if (empty($value) || $value === '-') {
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
