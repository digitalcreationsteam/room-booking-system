<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_type_id',
        'floor_number',
        'base_price',
        'gst_percentage',
        'service_tax_percentage',
        'other_charges',
        'amenities',
        'status'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'service_tax_percentage' => 'decimal:2',
        'other_charges' => 'decimal:2'
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bookingRooms()
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function isAvailable($checkIn, $checkOut)
    {
        return !$this->bookingRooms()
            ->whereHas('booking', function ($query) use ($checkIn, $checkOut) {
                $query->where('booking_status', '!=', 'cancelled')
                    ->where(function ($q) use ($checkIn, $checkOut) {
                        $q->whereBetween('check_in', [$checkIn, $checkOut])
                            ->orWhereBetween('check_out', [$checkIn, $checkOut])
                            ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                                $q2->where('check_in', '<=', $checkIn)
                                    ->where('check_out', '>=', $checkOut);
                            });
                    });
            })->exists();
    }

    // public function calculateTotalPrice($nights)
    // {
    //     $roomCharges = $this->base_price * $nights;
    //     $gstAmount = ($roomCharges * $this->gst_percentage) / 100;
    //     $serviceTax = ($roomCharges * $this->service_tax_percentage) / 100;
    //     $total = $roomCharges + $gstAmount + $serviceTax + $this->other_charges;

    //     return [
    //         'room_charges' => $roomCharges,
    //         'gst_amount' => $gstAmount,
    //         'service_tax' => $serviceTax,
    //         'other_charges' => $this->other_charges,
    //         'total' => $total
    //     ];
    // }


    public function calculateTotalPrice(int $nights)
    {
        $roomCharges   = 0;
        $gstAmount     = 0;
        $serviceTax    = 0;
        $otherCharges  = 0;

        // Night-wise calculation
        for ($i = 1; $i <= $nights; $i++) {

            $dailyRoomPrice   = $this->base_price;
            $dailyOtherCharge = $this->other_charges ?? 0;

            $dailyGst = ($dailyRoomPrice * $this->gst_percentage) / 100;
            $dailyServiceTax = ($dailyRoomPrice * $this->service_tax_percentage) / 100;

            $roomCharges  += $dailyRoomPrice;
            $gstAmount    += $dailyGst;
            $serviceTax   += $dailyServiceTax;
            $otherCharges += $dailyOtherCharge;
        }

        $total = $roomCharges + $gstAmount + $serviceTax + $otherCharges;

        return [
            'room_charges'  => round($roomCharges, 2),
            'gst_amount'    => round($gstAmount, 2),
            'service_tax'   => round($serviceTax, 2),
            'other_charges' => round($otherCharges, 2),
            'total'         => round($total, 2),
        ];
    }


}
