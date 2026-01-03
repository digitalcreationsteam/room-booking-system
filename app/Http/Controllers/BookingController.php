<?php
// File: app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\ExtraCharge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use App\Models\Customer;


class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['bookingRooms.room', 'creator']);

        if ($request->status) {
            $query->where('booking_status', $request->status);
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->from_date) {
            $query->whereDate('check_in', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('check_out', '<=', $request->to_date);
        }

        $bookings = $query->latest()->paginate(20);

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $roomTypes = RoomType::where('is_active', true)->get();
        $rooms = Room::where('status', 'available')->with('roomType')->get();

        return view('bookings.create', compact('roomTypes', 'rooms'));
    }

    // Old code for store method
    public function store(Request $request)
    {
        $validated = $request->validate([
            'registration_no'   => 'required|string|max:50',
            'customer_name' => 'required|string|max:255',
            'customer_mobile' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'customer_address' => 'required|string',
            'id_proof_type' => 'nullable',
            'id_proof_number' => 'nullable',
            'company_name' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:15',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'number_of_adults' => 'required|integer|min:1',
            'number_of_children' => 'nullable|integer|min:0',
            'number_of_nights' => 'nullable|integer|min:1',
            'room_ids' => 'required|array|min:1',
            'room_ids.*' => 'exists:rooms,id',
            'advance_payment' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|in:cash,card,upi,bank_transfer'
        ]);

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $nights = $checkIn->diffInDays($checkOut);
        // $nights = max(1, $checkIn->diffInDays($checkOut));

        // Calculate total charges
        $totalRoomCharges = 0;
        $totalGstPercentage = 0;
        $totalGst = 0;
        $totalServiceTax = 0;
        $totalOtherCharges = 0;

        $selectedRooms = Room::whereIn('id', $validated['room_ids'])->get();

        foreach ($selectedRooms as $room) {
            $calculation = $room->calculateTotalPrice($nights);
            $totalRoomCharges += $calculation['room_charges'];

            // Get GST percentage from room (if available)
            if (isset($calculation['gst_percentage'])) {
                $totalGstPercentage = $calculation['gst_percentage']; // Use last room's GST %
            }

            $totalGst += $calculation['gst_amount'];
            $totalServiceTax += $calculation['service_tax'];
            $totalOtherCharges += $calculation['other_charges'];
        }
    //    return $totalRoomCharges;

        $totalAmount = $totalRoomCharges + $totalGst + $totalServiceTax + $totalOtherCharges;
        $advancePayment = $validated['advance_payment'] ?? 0;
        $paymentMode = $validated['payment_mode'] ?? null;

        $advancePayment = $advancePayment;
        $remainingAmount = 0;
        $paymentStatus = 'paid';

        $customer = Customer::where('customer_mobile', $validated['customer_mobile'])->first();

        if (!$customer) {
            $customer = Customer::create([
                'customer_name'    => $validated['customer_name'],
                'customer_mobile'  => $validated['customer_mobile'],
                'customer_email'   => $validated['customer_email'],
                'customer_address' => $validated['customer_address'],
                'id_proof_type'    => $validated['id_proof_type'],
                'id_proof_number'  => $validated['id_proof_number'],
                'company_name'     => $validated['company_name'],
                'gst_number'       => $validated['gst_number'],
            ]);
        }

        // Create booking
        $booking = Booking::create([
            'registration_no'  => $validated['registration_no'] ?? null,
            'customer_id' => $customer->id,
            'customer_name' => $validated['customer_name'],
            'customer_mobile' => $validated['customer_mobile'],
            'customer_email' => $validated['customer_email'],
            'customer_address' => $validated['customer_address'],
            'id_proof_type' => $validated['id_proof_type'],
            'id_proof_number' => $validated['id_proof_number'],
            'company_name' => $validated['company_name'],
            'gst_number' => $validated['gst_number'],
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'number_of_adults' => $validated['number_of_adults'],
            'number_of_children' => $validated['number_of_children'] ?? 0,
            'number_of_nights' => $nights,
            'room_charges' => $totalRoomCharges,
            'gst_percentage' => $totalGstPercentage,
            'gst_amount' => $totalGst,
            'service_tax' => $totalServiceTax,
            'other_charges' => $totalOtherCharges,
            'total_amount' => $totalAmount, //net amount
            'advance_payment' => $advancePayment,
            'remaining_amount' => $remainingAmount,
            'payment_status' => $paymentStatus,
            'payment_mode' => $paymentMode,
            'created_by' => auth()->id()
        ]);

        // Attach rooms to booking
        foreach ($selectedRooms as $room) {
            $booking->bookingRooms()->create([
                'room_id' => $room->id,
                'room_price' => $room->base_price
            ]);

            // Update room status
            $room->update(['status' => 'booked']);
        }

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking created successfully!');
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'customer_name'     => 'required|string|max:255',
    //         'customer_mobile'   => 'required|string|max:20',
    //         'customer_email'    => 'nullable|email',
    //         'customer_address'  => 'required|string',
    //         'id_proof_type'     => 'nullable|string',
    //         'id_proof_number'   => 'nullable|string',
    //         'company_name'      => 'nullable|string|max:255',
    //         'gst_number'        => 'nullable|string|max:15',

    //         // booking fields...



    //         'check_in' => 'required|date',
    //         'check_out' => 'required|date|after:check_in',
    //         'number_of_adults' => 'required|integer|min:1',
    //         'number_of_children' => 'nullable|integer|min:0',
    //         'number_of_nights' => 'nullable|integer|min:1',
    //         'room_ids' => 'required|array|min:1',
    //         'room_ids.*' => 'exists:rooms,id',
    //         'advance_payment' => 'nullable|numeric|min:0',
    //         'payment_mode' => 'nullable|in:cash,card,upi,bank_transfer'
    //     ]);

    //     /* 🔍 CUSTOMER CHECK */
    //     $customer = Customer::where('customer_mobile', $validated['customer_mobile'])->first();

    //     if (!$customer) {
    //         $customer = Customer::create([
    //             'customer_name'    => $validated['customer_name'],
    //             'customer_mobile'  => $validated['customer_mobile'],
    //             'customer_email'   => $validated['customer_email'],
    //             'customer_address' => $validated['customer_address'],
    //             'id_proof_type'    => $validated['id_proof_type'],
    //             'id_proof_number'  => $validated['id_proof_number'],
    //             'company_name'     => $validated['company_name'],
    //             'gst_number'       => $validated['gst_number'],
    //         ]);
    //     }

    //     /* 🏨 CREATE BOOKING */
    //     $booking = Booking::create([
    //         'customer_id'        => $customer->id,
    //         'check_in'           => $validated['check_in'],
    //         'check_out'          => $validated['check_out'],
    //         'number_of_adults'   => $validated['number_of_adults'],
    //         'created_by'         => auth()->id(),
    //     ]);



    //     return redirect()
    //         ->route('bookings.show', $booking)
    //         ->with('success', 'Booking created successfully!');
    // }

    // public function show(Booking $booking)
    // {
    //     $booking->load(['bookingRooms.room.roomType', 'extraCharges', 'creator']);
    //     return view('bookings.show', compact('booking'));
    // }

    public function show(Booking $booking)
    {
        $booking->load([
            'customer',
            'bookingRooms.room.roomType',
            'extraCharges',
            'creator'
        ]);

        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        if ($booking->booking_status === 'cancelled') {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Cannot edit cancelled booking!');
        }

        $roomTypes = RoomType::where('is_active', true)->get();
        $rooms = Room::where('status', 'available')
            ->orWhereIn('id', $booking->rooms->pluck('id'))
            ->with('roomType')
            ->get();

        return view('bookings.edit', compact('booking', 'roomTypes', 'rooms'));
    }

    public function update(Request $request, Booking $booking)
    {
        // ❌ Cancelled booking protect
        if ($booking->booking_status === 'cancelled') {
            return redirect()
                ->route('bookings.show', $booking->id)
                ->with('error', 'Cancelled booking cannot be updated.');
        }

        // ✅ Validation
        $validated = $request->validate([
            'customer_name'       => 'required|string|max:255',
            'registration_no'     => 'required|string|max:255',
            'customer_mobile'     => 'required|string|max:20',
            'customer_email'      => 'nullable|email',
            'customer_address'    => 'required|string',
            'company_name'        => 'nullable|string|max:255',
            'gst_number'          => 'nullable|string|max:20',
            'discount_type'  => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',

            'number_of_adults'    => 'required|integer|min:1',
            'number_of_children' => 'nullable|integer|min:0',

            // 💰 Charges
            'room_charges'        => 'required|numeric|min:0',
            'gst_percentage'      => 'required|numeric|min:0|max:28',
            'gst_discount'        => 'nullable|numeric|min:0|max:100',
            'service_tax'         => 'nullable|numeric|min:0',
            'other_charges'       => 'nullable|numeric|min:0',

            'advance_payment'     => 'nullable|numeric|min:0',
        ]);

        $roomCharges   = $validated['room_charges'];
        $gstPercentage = $validated['gst_percentage'];
        $serviceTax    = $validated['service_tax'] ?? 0;
        $otherCharges  = $validated['other_charges'] ?? 0;
        $advancePayment = $validated['advance_payment'] ?? 0;

        $discountType  = $validated['discount_type'] ?? null;
        $discountValue = $validated['discount_value'] ?? 0;

        /* 🔻 Discount calculation */
        $discountAmount = 0;

        if ($discountType === 'percentage') {
            $discountAmount = ($roomCharges * $discountValue) / 100;
        } elseif ($discountType === 'fixed') {
            $discountAmount = min($discountValue, $roomCharges);
        }

        $discountedRoomCharges = max(0, $roomCharges - $discountAmount);

        /* 🔢 GST calculation AFTER discount */
        $gstAmount = ($discountedRoomCharges * $gstPercentage) / 100;

        /* 🔢 Final total */
        $finalTotal =
            $discountedRoomCharges +
            $gstAmount +
            $serviceTax +
            $otherCharges;

        /* 🔢 Remaining */
        $remainingAmount = 0;

        /* 🔢 Payment status */
        // if ($advancePayment >= $finalTotal) {
        //     $paymentStatus = 'paid';
        // } elseif ($advancePayment > 0) {
        //     $paymentStatus = 'partial';
        // } else {
        //     $paymentStatus = 'pending';
        // }

        // ✅ Update booking
        $booking->update([
            'room_charges'     => $roomCharges,

            'discount_type'    => $discountType,
            'discount_value'   => $discountValue,
            'discount_amount'  => $discountAmount,

            'gst_percentage'   => $gstPercentage,
            'gst_amount'       => $gstAmount,

            'service_tax'      => $serviceTax,
            'other_charges'    => $otherCharges,

            'total_amount'     => $finalTotal,
            'advance_payment'  => $advancePayment,
            'remaining_amount' => $remainingAmount,
            // 'payment_status'   => $paymentStatus,
        ]);

        // $booking->update([
        //     'customer_name'       => $validated['customer_name'],
        //     'customer_mobile'     => $validated['customer_mobile'],
        //     'customer_email'      => $validated['customer_email'],
        //     'customer_address'    => $validated['customer_address'],
        //     'company_name'        => $validated['company_name'],
        //     'gst_number'          => $validated['gst_number'],

        //     'number_of_adults'    => $validated['number_of_adults'],
        //     'number_of_children' => $validated['number_of_children'],

        //     'room_charges'        => $validated['room_charges'],
        //     'gst_percentage'      => $gstPercentage,
        //     'gst_discount'        => $gstDiscountPct,
        //     'gst_amount'          => $finalGst,

        //     'service_tax'         => $serviceTax,
        //     'other_charges'       => $otherCharges,

        //     'total_amount'        => $finalTotal,
        //     'advance_payment'     => $advancePayment,
        //     'remaining_amount'    => $remainingAmount,

        //     'payment_status'      => $paymentStatus,
        // ]);

        return redirect()
            ->route('bookings.show', $booking->id)
            ->with('success', 'Booking updated successfully!');
    }

    public function addExtraCharge(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'charge_type' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'charge_date' => 'required|date'
        ]);

        $booking->extraCharges()->create($validated);

        // Update booking totals
        $totalExtraCharges = $booking->extraCharges()->sum('amount');
        $newTotal = $booking->room_charges + $booking->gst_amount +
            $booking->service_tax + $booking->other_charges + $totalExtraCharges;

        $booking->update([
            'extra_charges' => $totalExtraCharges,
            'total_amount' => $newTotal,
            'remaining_amount' => $newTotal - $booking->advance_payment
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Extra charge added successfully!');
    }

    public function deleteExtraCharge(ExtraCharge $charge)
    {
        $booking = $charge->booking;
        $charge->delete();

        // Recalculate totals
        $totalExtraCharges = $booking->extraCharges()->sum('amount');
        $newTotal = $booking->room_charges + $booking->gst_amount +
            $booking->service_tax + $booking->other_charges + $totalExtraCharges;

        $booking->update([
            'extra_charges' => $totalExtraCharges,
            'total_amount' => $newTotal,
            'remaining_amount' => $newTotal - $booking->advance_payment
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Extra charge deleted successfully!');
    }

    public function addPayment(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:cash,card,upi,bank_transfer'
        ]);

        $newAdvance = $booking->advance_payment + $validated['payment_amount'];
        $remaining = $booking->total_amount - $newAdvance;

        $paymentStatus = 'pending';
        if ($newAdvance >= $booking->total_amount) {
            $paymentStatus = 'paid';
            $remaining = 0;
        } elseif ($newAdvance > 0) {
            $paymentStatus = 'partial';
        }

        $booking->update([
            'advance_payment' => $newAdvance,
            'remaining_amount' => $remaining,
            'payment_status' => $paymentStatus,
            'payment_mode' => $validated['payment_mode']
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Payment added successfully!');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string',
            'refund_amount' => 'nullable|numeric|min:0'
        ]);

        $booking->update([
            'booking_status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'refund_amount' => $validated['refund_amount'] ?? 0
        ]);

        // Make rooms available again
        foreach ($booking->rooms as $room) {
            $room->update(['status' => 'available']);
        }

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking cancelled successfully!');
    }

    public function checkIn(Booking $booking)
    {
        $booking->update(['booking_status' => 'checked_in']);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Customer checked in successfully!');
    }

    public function checkOut(Booking $booking)
    {
        $booking->update(['booking_status' => 'checked_out']);

        // Make rooms available
        foreach ($booking->rooms as $room) {
            $room->update(['status' => 'available']);
        }

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Customer checked out successfully!');
    }

    // public function invoice(Booking $booking)
    // {
    //     $booking->load(['bookingRooms.room.roomType', 'extraCharges']);

    //     return view('bookings.invoice', compact('booking'));
    // }


    public function invoice(Booking $booking)
    {
        // Booking relations
        $booking->load([
            'bookingRooms.room.roomType',
            'extraCharges'
        ]);

        // Current logged-in user
        $user = Auth::user();

        // Hotel info (agar user_id se hotel linked hai)
        $hotel = $user->hotel ?? null;
        // OR agar Booking se hotel milta ho:
        // $hotel = $booking->hotel ?? null;

        return view('bookings.invoice', compact(
            'booking',
            'user',
            'hotel'
        ));
    }
}
