<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'charge_type',
        'description',
        'amount',
        'charge_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'charge_date' => 'date'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
