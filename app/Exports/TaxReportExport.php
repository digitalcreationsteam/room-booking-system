<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TaxReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    protected $bookings;
    protected $counter = 1;

    public function __construct($bookings)
    {
        $this->bookings = $bookings;
    }

    public function collection()
    {
        return $this->bookings;
    }

    public function headings(): array
    {
        return [
            'SR No',
            'DATE',
            'INVOICE NO',
            'CUST NAME',
            'COMPANY NAME',
            'GST NO',
            'NET AMT',
            'GST AMT',
            'OTHER AMT',
            'GROSS AMT',
        ];
    }

    public function map($booking): array
    {
        $taxableAmount = $booking->room_charges - $booking->discount_amount;
        $otherCharge = $booking->service_tax + $booking->other_charges;

        return [
            $this->counter++,
            optional($booking->created_at)->format('d-m-Y'),
            $booking->booking_number,
            $booking->customer_name,
            $booking->company_name ?? '',
            $booking->gst_number ?? '',
            number_format($taxableAmount, 2),
            number_format($booking->gst_amount, 2),
            number_format($otherCharge, 2),
            number_format($booking->total_amount, 2),
        ];
    }

    /**
     * ✅ Excel column formatting
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_NUMBER_00,
            'I' => NumberFormat::FORMAT_NUMBER_00,
            'J' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }
    
}
