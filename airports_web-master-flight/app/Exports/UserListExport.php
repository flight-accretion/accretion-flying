<?php

namespace FlyingCalculation\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class UserListExport implements FromArray, WithEvents, WithTitle
{
    protected array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function title(): string
    {
        return 'sheet1';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A2:E2');
                $sheet->mergeCells('A3:E3');

                foreach (['A2:E2' => 14, 'A3:E3' => 12, 'A5:E5' => 12] as $range => $size) {
                    $sheet->getStyle($range)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => $size,
                        ],
                        'alignment' => [
                            'horizontal' => 'center',
                        ],
                    ]);
                }

                foreach (range('A', 'E') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
