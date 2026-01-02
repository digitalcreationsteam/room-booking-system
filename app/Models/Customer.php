<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_mobile',
        'customer_email',
        'customer_address',
        'id_proof_type',
        'id_proof_number',
        'company_name',
        'gst_number',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
