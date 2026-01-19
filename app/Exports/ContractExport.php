<?php

namespace App\Exports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContractExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Contract::with('employee')->get();
    }

    /**
     * Map data for each row
     */
    public function map($contract): array
    {
        static $index = 0;
        $index++;

        return [
            $index, // STT
            $contract->classification, // Phân loại
            $contract->contract_number, // Số hợp đồng
            $contract->industry, // Ngành nghề
            $contract->project_name, // Tên dự án
            $contract->signing_date ? $contract->signing_date->format('d/m/Y') : '', // Ngày ký
            $contract->start_date ? $contract->start_date->format('d/m/Y') : '', // Ngày hiệu lực
            $contract->end_date ? $contract->end_date->format('d/m/Y') : '', // Ngày kết thúc
            $contract->extension_date ? $contract->extension_date->format('d/m/Y') : '', // Ngày gia hạn
            $contract->duration_days, // Thời gian
            $contract->contract_content, // Nội dung hợp
            $contract->contract_value, // Giá trị hợp
            $contract->adjusted_value, // Giá trị sau
            $contract->approval_status, // Phê duyệt
            $contract->value_difference, // Chênh lệch
            $contract->investor, // Chủ đầu tư
            $contract->status, // Trạng thái
            $contract->condition_status, // Tình trạng
            $contract->legal_entity, // Pháp nhân
            $contract->advance_payment, // Tạm ứng
            $contract->notes, // Ghi chú
            $contract->appendix_number, // Số phụ lục
            $contract->revision_count, // Số lần
            $contract->extension_count, // Số lần gia
            $contract->created_at ? $contract->created_at->format('d/m/Y') : '', // Ngày tạo
        ];
    }

    /**
     * Define headers
     */
    public function headings(): array
    {
        return [
            'STT',
            'Phân loại',
            'Số hợp đồng',
            'Ngành nghề',
            'Tên dự án',
            'Ngày ký',
            'Ngày hiệu lực',
            'Ngày kết thúc',
            'Ngày gia hạn',
            'Thời gian',
            'Nội dung hợp',
            'Giá trị hợp',
            'Giá trị sau',
            'Phê duyệt',
            'Chênh lệch',
            'Chủ đầu tư',
            'Trạng thái',
            'Tình trạng',
            'Pháp nhân',
            'Tạm ứng',
            'Ghi chú',
            'Số phụ lục',
            'Số lần',
            'Số lần gia',
            'Ngày tạo',
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
