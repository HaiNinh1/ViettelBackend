<?php

namespace App\Imports;

use App\Models\Contract;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class ContractImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Parse dates from Vietnamese format (dd/mm/yyyy)
        $signingDate = $this->parseDate($row['ngay_ky'] ?? null);
        $startDate = $this->parseDate($row['ngay_hieu_luc'] ?? null);
        $endDate = $this->parseDate($row['ngay_ket_thuc'] ?? null);
        $extensionDate = $this->parseDate($row['ngay_gia_han'] ?? null);

        return new Contract([
            'contract_number' => $row['so_hop_dong'] ?? null,
            'classification' => $row['phan_loai'] ?? null,
            'industry' => $row['nganh_nghe'] ?? null,
            'project_name' => $row['ten_du_an'] ?? null,
            'signing_date' => $signingDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'extension_date' => $extensionDate,
            'duration_days' => $row['thoi_gian'] ?? null,
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
            'revision_count' => $row['so_lan'] ?? 0,
            'extension_count' => $row['so_lan_gia'] ?? 0,
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'so_hop_dong' => 'required|unique:contracts,contract_number',
            'ngay_hieu_luc' => 'required',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'so_hop_dong.required' => 'Số hợp đồng là bắt buộc',
            'so_hop_dong.unique' => 'Số hợp đồng đã tồn tại',
            'ngay_hieu_luc.required' => 'Ngày hiệu lực là bắt buộc',
        ];
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

        // Remove commas and convert to float
        return (float) str_replace(',', '', $value);
    }
}
