<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'registration_no',

        'customer_name',
        'customer_mobile',
        'customer_email',
        'customer_address',
        'id_proof_type',
        'id_proof_number',
        'company_name',
        'gst_number',
        'check_in',
        'check_out',
        'number_of_adults',
        'number_of_children',
        'number_of_nights',
        'room_charges',
        'gst_percentage',      // ✅ Added
        'gst_amount',
        'service_tax',
        'other_charges',
        'extra_charges',
        'total_amount',
        'advance_payment',
        'remaining_amount',
        'payment_status',
        'payment_mode',
        'booking_status',
        'cancellation_reason',
        'refund_amount',
        'created_by',
        'discount_type',
        'discount_value',
        'discount_amount',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'room_charges' => 'decimal:2',
        'gst_percentage' => 'decimal:2',   // ✅ Added
        'gst_amount' => 'decimal:2',
        'service_tax' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'extra_charges' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'discount_value'  => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->booking_number = 'BK' . date('Ymd') . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function bookingRooms()
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function extraCharges()
    {
        return $this->hasMany(ExtraCharge::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'booking_rooms')
            ->withPivot('room_price')
            ->withTimestamps();
    }
}
