<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\{
    FromCollection,
    ShouldAutoSize,
    WithColumnFormatting,
    WithHeadings,
    WithMapping
};
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
            'SR NO',
            'DATE',
            'INVOICE NO',
            'CUSTOMER NAME',
            'COMPANY NAME',
            'GST NO',
            'NET AMOUNT',
            'GST AMOUNT',
            'OTHER CHARGES',
            'GROSS AMOUNT',
        ];
    }

    public function map($booking): array
    {
        $taxableAmount = $booking->room_charges - $booking->discount_amount;
        $otherCharge   = $booking->service_tax + $booking->other_charges;

        return [
            $this->counter++,
            $booking->created_at,          // ✅ raw date
            $booking->booking_number,
            $booking->customer_name,
            $booking->company_name ?? '',
            $booking->gst_number ?? '',
            $taxableAmount,                // ✅ numeric
            $booking->gst_amount,
            $otherCharge,
            $booking->total_amount,
        ];
    }

    /**
     * ✅ Excel-native formatting
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_NUMBER_00,
            'H' => NumberFormat::FORMAT_NUMBER_00,
            'I' => NumberFormat::FORMAT_NUMBER_00,
            'J' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }
}
