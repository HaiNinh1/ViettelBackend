<?php

namespace App\Exports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ContractExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting
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
            $contract->contract_value ?? 0, // Giá trị hợp
            $contract->adjusted_value ?? 0, // Giá trị sau
            $contract->approval_status, // Phê duyệt
            $contract->value_difference ?? 0, // Chênh lệch
            $contract->investor, // Chủ đầu tư
            $contract->status, // Trạng thái
            $contract->condition_status, // Tình trạng
            $contract->legal_entity, // Pháp nhân
            $contract->advance_payment, // Tạm ứng
            $contract->notes, // Ghi chú
            $contract->appendix_number, // Số phụ lục
            $contract->revision_count ?? 0, // Số lần
            $contract->extension_count ?? 0, // Số lần gia
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
     * Column formatting with thousand separator (Vietnamese format)
     */
    public function columnFormats(): array
    {
        return [
            'L' => '#,##0', // Giá trị hợp (column 12) - with thousand separator
            'M' => '#,##0', // Giá trị sau (column 13) - with thousand separator
            'O' => '#,##0', // Chênh lệch (column 15) - with thousand separator
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Make header bold
        $sheet->getStyle('1')->getFont()->setBold(true);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);  // STT
        $sheet->getColumnDimension('B')->setWidth(12); // Phân loại
        $sheet->getColumnDimension('C')->setWidth(25); // Số hợp đồng
        $sheet->getColumnDimension('D')->setWidth(20); // Ngành nghề
        $sheet->getColumnDimension('E')->setWidth(30); // Tên dự án
        $sheet->getColumnDimension('L')->setWidth(18); // Giá trị hợp
        $sheet->getColumnDimension('M')->setWidth(18); // Giá trị sau
        $sheet->getColumnDimension('O')->setWidth(18); // Chênh lệch
        
        // Apply number format with dot as thousand separator (Vietnamese format)
        // Get the highest row number
        $highestRow = $sheet->getHighestRow();
        
        // Format columns L, M, O with custom number format using dot separator
        $sheet->getStyle('L2:L' . $highestRow)
            ->getNumberFormat()
            ->setFormatCode('#.##0');
        
        $sheet->getStyle('M2:M' . $highestRow)
            ->getNumberFormat()
            ->setFormatCode('#.##0');
        
        $sheet->getStyle('O2:O' . $highestRow)
            ->getNumberFormat()
            ->setFormatCode('#.##0');
        
        return [];
    }
}
