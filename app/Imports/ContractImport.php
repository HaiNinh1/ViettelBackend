<?php

namespace App\Imports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Carbon\Carbon;

class ContractImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Excel headers will be converted to snake_case by Laravel Excel
        // "Số hợp đồng" becomes "so_hop_dong"
        // "Phân loại" becomes "phan_loai"
        // etc.
        
        // Debug: Log the row keys to see what we're getting
        \Log::info('Import row keys:', array_keys($row));
        \Log::info('Import row data:', $row);
        
        // Parse dates from Vietnamese format (dd/mm/yyyy) or Excel serial
        $signingDate = $this->parseDate($row['ngay_ky'] ?? null);
        $startDate = $this->parseDate($row['ngay_hieu_luc'] ?? null);
        $endDate = $this->parseDate($row['ngay_ket_thuc'] ?? null);
        $extensionDate = $this->parseDate($row['ngay_gia_han'] ?? null);

        // Get contract number - try multiple possible column names
        $contractNumber = $row['so_hop_dong'] ?? $row['so_hop'] ?? null;
        
        // Skip if contract number is empty or already exists
        if (empty($contractNumber)) {
            \Log::warning('Skipping row - no contract number');
            return null;
        }
        
        if (Contract::where('contract_number', $contractNumber)->exists()) {
            \Log::warning('Skipping row - contract already exists: ' . $contractNumber);
            return null;
        }

        return new Contract([
            'contract_number' => $contractNumber,
            'classification' => $row['phan_loai'] ?? null,
            'industry' => $row['nganh_nghe'] ?? null,
            'project_name' => $row['ten_du_an'] ?? null,
            'signing_date' => $signingDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'extension_date' => $extensionDate,
            'duration_days' => $this->parseNumber($row['thoi_gian'] ?? null),
            'contract_content' => $row['noi_dung_hop'] ?? null,
            'contract_value' => $this->parseNumber($row['gia_tri_hop'] ?? null),
            'adjusted_value' => $this->parseNumber($row['gia_tri_sau'] ?? null),
            'value_difference' => $this->parseNumber($row['chenh_lech'] ?? null),
            'approval_status' => $row['phe_duyet'] ?? null,
            'status' => 'active',
            'contract_status' => $row['trang_thai'] ?? null,
            'condition_status' => $row['tinh_trang'] ?? null,
            'investor' => $row['chu_dau_tu'] ?? null,
            'legal_entity' => $row['phap_nhan'] ?? null,
            'advance_payment' => $row['tam_ung'] ?? null,
            'notes' => $row['ghi_chu'] ?? null,
            'appendix_number' => $row['so_phu_luc'] ?? null,
            'revision_count' => $this->parseNumber($row['so_lan'] ?? 0),
            'extension_count' => $this->parseNumber($row['so_lan_gia'] ?? 0),
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            // Make validation optional since we handle it in model()
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [];
    }

    /**
     * Specify the row number where headers are located
     */
    public function headingRow(): int
    {
        return 2; // Headers are on row 2 (after the title row)
    }

    /**
     * Parse Vietnamese date format (dd/mm/yyyy) to Carbon
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Handle dd/mm/yyyy format
            if (is_string($date) && strpos($date, '/') !== false) {
                return Carbon::createFromFormat('d/m/Y', $date);
            }
            
            // Handle Excel date serial number
            if (is_numeric($date)) {
                return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date));
            }

            return Carbon::parse($date);
        } catch (\Exception $e) {
            \Log::error('Date parse error: ' . $e->getMessage() . ' for value: ' . $date);
            return null;
        }
    }

    /**
     * Parse number with comma separator
     */
    private function parseNumber($value)
    {
        if (empty($value)) {
            return null;
        }

        // Handle string numbers with commas or dots
        if (is_string($value)) {
            // Remove thousand separators (commas, dots, spaces)
            $value = str_replace([',', '.', ' '], '', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }
}
