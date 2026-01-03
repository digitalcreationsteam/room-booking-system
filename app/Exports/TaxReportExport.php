<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
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
            'Sr No.',
            'Date',
            'Booking #',
            'Customer Name',
            'Customer Mobile No',
            'Company Name',
            'GST Number',
            'Taxable Amount',
            'GST Amount',
            'Total Amount'
        ];
    }

    public function map($booking): array
    {
        return [
            $this->counter++,                                   
            optional($booking->created_at)->format('d-m-Y'),   
            $booking->booking_number,                           
            $booking->customer_name,                            
            " " . ($booking->customer_mobile ?? ''),        
            $booking->company_name ?? '',                    
            $booking->gst_number ?? '',                    
            number_format($booking->room_charges, 2),           
            number_format($booking->gst_amount, 2),            
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
            'E' => NumberFormat::FORMAT_TEXT,                   
            'H' => NumberFormat::FORMAT_NUMBER_00,              
            'I' => NumberFormat::FORMAT_NUMBER_00,              
            'J' => NumberFormat::FORMAT_NUMBER_00,             
        ];
    }
}
