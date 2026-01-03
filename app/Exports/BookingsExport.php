<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BookingsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $bookings;

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
            'Booking Number',
            'Customer Name',
            'Mobile',
            'Company Name',
            'GST Number',
            'Check In',
            'Check Out',
            'Nights',
            'Rooms',
            'Room Charges',
            'GST',
            'Service Tax',
            'Extra Charges',
            'Total Amount',
            'Advance Payment',
            'Remaining',
            'Payment Status',
            'Booking Status'
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->booking_number,
            $booking->customer_name,
            $booking->customer_mobile,
            $booking->company_name ?? 'N/A',
            $booking->gst_number ?? 'N/A',
            $booking->check_in->format('d-m-Y'),
            $booking->check_out->format('d-m-Y'),
            $booking->number_of_nights,
            $booking->bookingRooms->pluck('room.room_number')->implode(', '),
            $booking->room_charges,
            $booking->gst_amount,
            $booking->service_tax,
            $booking->extra_charges,
            $booking->total_amount,
            $booking->advance_payment,
            $booking->remaining_amount,
            ucfirst($booking->payment_status),
            ucfirst($booking->booking_status)
        ];
    }
}
