<?php

namespace FlyingCalculation\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MachineDetailsExport implements FromArray, WithEvents, WithTitle
{
    protected array $rows = [];
    protected array $boldRows = [];
    protected array $sectionRows = [];
    protected array $tableHeaderRows = [];
    protected array $blankRows = [];
    protected string $sheetTitle;

    public function __construct(
        protected int $planeType,
        protected string $planeName,
        protected $totalHours,
        protected $totalMins,
        protected $totalFlyingCost,
        protected $groundHandling,
        protected $crewHandling,
        protected array $flights,
        protected $subTotal,
        protected $gst,
        protected $grandTotal,
        protected string $planeCityText,
        protected $price
    ) {
        $this->sheetTitle = substr($this->planeName, 0, 30);
        $this->buildRows();
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

                $sheet->mergeCells('A1:F1');

                foreach ($this->sectionRows as $row) {
                    $sheet->mergeCells('A' . $row . ':F' . $row);
                }

                $lastRow = count($this->rows);
                $sheet->getStyle('A1:F' . $lastRow)->applyFromArray([
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 11,
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                foreach ($this->boldRows as $row => $size) {
                    $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => $size,
                        ],
                    ]);
                }

                foreach ($this->tableHeaderRows as $row) {
                    $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                    ]);
                }

                foreach ($this->blankRows as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(8);
                }

                $sheet->getColumnDimension('A')->setWidth(24);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(22);
                $sheet->getColumnDimension('F')->setWidth(22);
            },
        ];
    }

    protected function buildRows(): void
    {
        $planeCityRate = $this->planeCityText !== ''
            ? ' [Base: ' . $this->planeCityText . ', Rate: ' . $this->price . ']'
            : ' [Rate: ' . $this->price . ']';

        $this->addRow([$this->planeName . ' Details' . $planeCityRate], 16);
        $this->addRow([]);
        $this->addRow(['Flight Details'], 14, true);
        $this->addRow(['Departure Time', 'Departure', 'Flight Time', 'Arrival', 'Arrival Time', 'Particular'], 12, false, true);

        $normalizedFlights = [];
        foreach ($this->flights as $flight) {
            $flight->departure_time = date('d-m-Y H:i:s', strtotime($flight->departure_time));
            $flight->arrival_time = date('d-m-Y H:i:s', strtotime($flight->arrival_time));
            $normalizedFlights[] = $flight;

            $this->addRow([
                $flight->departure_time,
                $flight->departure,
                $flight->hours . ' hours ' . $flight->minutes . ' mins',
                $flight->arrival,
                $flight->arrival_time,
                $flight->details,
            ]);
        }

        // $this->addRow([]);
        // $this->addRow(['Cost Estimate'], 14, true);
        // $this->addRow(['Date', 'Departure', 'Arrival', 'Flight Time', 'Cost', 'Particular'], 12, false, true);

        // foreach ($normalizedFlights as $flight) {
        //     $this->addRow([
        //         $flight->departure_time,
        //         $flight->departure,
        //         $flight->arrival,
        //         $flight->hours . ' hours ' . $flight->minutes . ' mins',
        //         ' ' . round($flight->cost, 2),
        //         $flight->details,
        //     ]);
        // }

        $this->addRow([]);
        $this->addRow(['Cost Details', 'Amount'], 12, false, true);
        $this->addRow(['Total Flight Time', $this->totalHours . ' hours ' . $this->totalMins . ' mins']);
        $this->addRow(['Flying Cost', ' ' . $this->totalFlyingCost]);
        $this->addRow(['Ground Handling', ' ' . $this->groundHandling]);
        $this->addRow(['Crew Handling', ' ' . $this->crewHandling]);
        $this->addRow(['Other Charges', 'As per actual']);
        $this->addRow(['Sub Total', ' ' . $this->subTotal]);

        if ($this->planeType !== 3) {
            $this->addRow(['GST @ 18%', ' ' . $this->gst]);
        }

        $this->addRow(['Grand Total', ' ' . $this->grandTotal]);
    }

    protected function addRow(array $row, ?int $fontSize = null, bool $isSection = false, bool $isTableHeader = false): void
    {
        $isBlankRow = count($row) === 0;
        if ($isBlankRow) {
            $row = [''];
        }

        $this->rows[] = $row;
        $rowNumber = count($this->rows);

        if ($fontSize !== null) {
            $this->boldRows[$rowNumber] = $fontSize;
        }

        if ($isSection) {
            $this->sectionRows[] = $rowNumber;
        }

        if ($isTableHeader) {
            $this->tableHeaderRows[] = $rowNumber;
        }

        if ($isBlankRow) {
            $this->blankRows[] = $rowNumber;
        }
    }
}
