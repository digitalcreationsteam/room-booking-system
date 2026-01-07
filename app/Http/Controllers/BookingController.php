<?php
// File: app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\ExtraCharge;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(Booking $booking)
    {
        try {
            // Soft delete (if you have SoftDeletes trait in Booking model)
            $booking->delete();

            return redirect()->route('bookings.index')
                ->with('success', 'Booking deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete booking: ' . $e->getMessage());
        }
    }

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

        $bookings = $query->latest()->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $roomTypes = RoomType::where('is_active', true)->get();
        $rooms     = Room::where('status', 'available')->with('roomType')->get();

        return view('bookings.create', compact('roomTypes', 'rooms'));
    }

    // Old code for store method
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'registration_no'    => 'required|string|max:50',
    //         'customer_name'      => 'required|string|max:255',
    //         'customer_mobile'    => 'required|string|max:20',
    //         'customer_email'     => 'nullable|email',
    //         'customer_address'   => 'required|string',
    //         'company_name'       => 'nullable|string|max:255',
    //         'gst_number'         => 'nullable|string|max:15',

    //         'check_in'           => 'required|date',
    //         'check_out'          => 'required|date|after:check_in',

    //         'number_of_adults'   => 'required|integer|min:1',
    //         'number_of_children' => 'nullable|integer|min:0',

    //         'room_ids'           => 'required|array|min:1',
    //         'room_ids.*'         => 'exists:rooms,id',

    //         'advance_payment'    => 'nullable|numeric|min:0',
    //         'payment_mode'       => 'nullable|in:cash,card,upi,bank_transfer',
    //     ]);

    //     $checkIn  = Carbon::parse($validated['check_in']);
    //     $checkOut = Carbon::parse($validated['check_out']);
    //     $nights   = max(1, $checkIn->diffInDays($checkOut));

    //     $totalRoomCharges = 0;
    //     $totalGstAmount   = 0;
    //     $gstPercentage    = null;

    //     $selectedRooms = Room::whereIn('id', $validated['room_ids'])->get();

    //     foreach ($selectedRooms as $room) {
    //         $calc = $room->calculateTotalPrice($nights);

    //         $totalRoomCharges += $calc['room_charges'];
    //         $totalGstAmount += $calc['gst_amount'];

    //         if ($gstPercentage === null) {
    //             $gstPercentage = $calc['gst_percentage']; // 🔥 ALWAYS SET
    //         }
    //     }

    //     $totalAmount = $totalRoomCharges + $totalGstAmount;
    //     $advance     = $validated['advance_payment'] ?? 0;
    //     $remaining   = max(0, $totalAmount - $advance);

    //     $booking = Booking::create([
    //         'registration_no'    => $validated['registration_no'],
    //         'customer_name'      => $validated['customer_name'],
    //         'customer_mobile'    => $validated['customer_mobile'],
    //         'customer_email'     => $validated['customer_email'],
    //         'customer_address'   => $validated['customer_address'],
    //         'company_name'       => $validated['company_name'],
    //         'gst_number'         => $validated['gst_number'],

    //         'check_in'           => $checkIn,
    //         'check_out'          => $checkOut,
    //         'number_of_adults'   => $validated['number_of_adults'],
    //         'number_of_children' => $validated['number_of_children'] ?? 0,
    //         'number_of_nights'   => $nights,

    //         'room_charges'       => $totalRoomCharges,
    //         'gst_percentage'     => $gstPercentage, // ✅ SAVES
    //         'gst_amount'         => $totalGstAmount,

    //         'total_amount'       => $totalAmount,
    //         'advance_payment'    => $advance,
    //         'remaining_amount'   => $remaining,
    //         'payment_status'     => $remaining > 0 ? 'partial' : 'paid',
    //         'payment_mode'       => $validated['payment_mode'],
    //         'booking_status'     => 'confirmed',
    //         'created_by'         => auth()->id(),
    //     ]);

    //     foreach ($selectedRooms as $room) {
    //         $booking->bookingRooms()->create([
    //             'room_id'    => $room->id,
    //             'room_price' => $room->base_price,
    //         ]);

    //         $room->update(['status' => 'booked']);
    //     }

    //     return redirect()
    //         ->route('bookings.show', $booking)
    //         ->with('success', 'Booking created successfully!');
    // }





    public function show(Booking $booking)
    {
        $booking->load([
            'customer',
            'bookingRooms.room.roomType',
            'extraCharges',
            'creator',
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
        $rooms     = Room::where('status', 'available')
            ->orWhereIn('id', $booking->rooms->pluck('id'))
            ->with('roomType')
            ->get();

        return view('bookings.edit', compact('booking', 'roomTypes', 'rooms'));
    }


    // Old code for store method
    public function store(Request $request)
    {
        $validated = $request->validate([
            'registration_no'   => 'required|string|max:50',
            'customer_name' => 'required|string|max:255',
            'customer_mobile' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'customer_address' => 'nullable|string',
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
        // $nights = $checkIn->diffInDays($checkOut);
        $nights = max(1, $checkIn->diffInDays($checkOut));

        // Calculate total charges
        $totalRoomCharges = 0;
        $totalGstPercentage = 0;
        $totalGst = 0;
        $totalServiceTax = 0;
        $totalOtherCharges = 0;

        $selectedRooms = Room::whereIn('id', $validated['room_ids'])->get();

        // return $selectedRooms;
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
        // $remainingAmount = 0;
        // $paymentStatus = 'paid';

        $advancePayment = $advancePayment ?? 0;


        $remainingAmount = max(0, $totalAmount - $advancePayment);

        if ($advancePayment == 0) {
            $paymentStatus = 'pending';
        } elseif ($advancePayment == $totalAmount) {
            $paymentStatus = 'paid';
        } else {
            $paymentStatus = 'partial';
        }

        // if ($advancePayment == 0) {
        //     $remainingAmount = 0;
        //     $paymentStatus = 'paid'; // or 'pending'
        // } else {
        //     $remainingAmount = max(0, $totalAmount - $advancePayment);
        //     $paymentStatus = $remainingAmount > 0 ? 'partial' : 'paid';
        // }

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

    public function getBooking($id)
    {
        $booking = Booking::with(['bookingRooms.room', 'customer'])->findOrFail($id);

        return response()->json([
            'booking' => $booking,
            'rooms' => $booking->bookingRooms->map(function ($br) {
                return [
                    'roomNumber' => $br->room->room_number,
                    'roomType' => $br->room->room_type,
                    'ratePerNight' => $br->room_price
                ];
            })
        ]);
    }

    // public function update(Request $request, Booking $booking)
    // {
    //     if ($booking->booking_status === 'cancelled') {
    //         return back()->with('error', 'Cancelled booking cannot be updated.');
    //     }

    //     $validated = $request->validate([
    //         'room_charges'    => 'required|numeric|min:0',
    //         'gst_percentage'  => 'required|numeric|min:0|max:28',
    //         'service_tax'     => 'nullable|numeric|min:0',
    //         'other_charges'   => 'nullable|numeric|min:0',
    //         'advance_payment' => 'nullable|numeric|min:0',

    //         'discount_type'   => 'nullable|in:percentage,fixed',
    //         'discount_value'  => 'nullable|numeric|min:0',
    //     ]);

    //     $roomCharges   = $validated['room_charges'];
    //     $gstPercentage = $validated['gst_percentage'];
    //     $serviceTax    = $validated['service_tax'] ?? 0;
    //     $otherCharges  = $validated['other_charges'] ?? 0;
    //     $advance       = $validated['advance_payment'] ?? 0;

    //     $discountAmount = 0;
    //     if ($validated['discount_type'] === 'percentage') {
    //         $discountAmount = ($roomCharges * $validated['discount_value']) / 100;
    //     } elseif ($validated['discount_type'] === 'fixed') {
    //         $discountAmount = min($validated['discount_value'], $roomCharges);
    //     }

    //     $netRoomCharges = max(0, $roomCharges - $discountAmount);
    //     $gstAmount      = ($netRoomCharges * $gstPercentage) / 100;

    //     $totalAmount = $netRoomCharges + $gstAmount + $serviceTax + $otherCharges;
    //     // $remaining   = max(0, $totalAmount - $advance);

    //     if ($advance == 0) {
    //         $remaining = 0;
    //     } else {
    //         $remaining = max(0, $totalAmount - $advance);
    //     }


    //     $booking->update([
    //         'room_charges'     => $roomCharges,
    //         'discount_type'    => $validated['discount_type'],
    //         'discount_value'   => $validated['discount_value'],
    //         'discount_amount'  => $discountAmount,

    //         'gst_percentage'   => $gstPercentage, // ✅ UPDATES
    //         'gst_amount'       => $gstAmount,

    //         'service_tax'      => $serviceTax,
    //         'other_charges'    => $otherCharges,

    //         'total_amount'     => $totalAmount,
    //         'advance_payment'  => $advance,
    //         'remaining_amount' => $remaining,
    //         'payment_status'   => $remaining > 0 ? 'partial' : 'paid',
    //     ]);

    //     return redirect()
    //         ->route('bookings.show', $booking)
    //         ->with('success', 'Booking updated successfully!');
    // }

    // Last code
    // public function update(Request $request, Booking $booking)
    // {
    //     // Check if booking is cancelled
    //     if ($booking->booking_status === 'cancelled') {
    //         return back()->with('error', 'Cancelled booking cannot be updated.');
    //     }

    //     // Validate all fields
    //     $validated = $request->validate([
    //         'registration_no'      => 'required|string|max:50',
    //         'customer_name'        => 'required|string|max:255',
    //         'customer_mobile'      => 'required|string|max:20',
    //         'customer_email'       => 'nullable|email',
    //         'customer_address'     => 'nullable|string',
    //         'company_name'         => 'nullable|string|max:255',
    //         'gst_number'           => 'nullable|string|max:15',

    //         'check_in'             => 'required|date',
    //         'check_out'            => 'required|date|after:check_in',
    //         'number_of_adults'     => 'required|integer|min:1',
    //         'number_of_children'   => 'nullable|integer|min:0',

    //         'room_ids'             => 'required|array|min:1',
    //         'room_ids.*'           => 'exists:rooms,id',

    //         'room_charges'         => 'required|numeric|min:0',
    //         'discount_type'        => 'nullable|in:percentage,fixed',
    //         'discount_value'       => 'nullable|numeric|min:0',
    //         'gst_percentage'       => 'required|numeric|min:0|max:100',
    //         'service_tax'          => 'nullable|numeric|min:0',
    //         'other_charges'        => 'nullable|numeric|min:0',
    //         'advance_payment'      => 'nullable|numeric|min:0',
    //         'payment_status'       => 'required|in:pending,partial,paid',
    //     ]);

    //     // Calculate dates
    //     $checkIn  = Carbon::parse($validated['check_in']);
    //     $checkOut = Carbon::parse($validated['check_out']);
    //     $nights   = max(1, $checkIn->diffInDays($checkOut));

    //     // Calculate discount
    //     $roomCharges    = $validated['room_charges'];
    //     $discountAmount = 0;

    //     if (!empty($validated['discount_type']) && !empty($validated['discount_value'])) {
    //         if ($validated['discount_type'] === 'percentage') {
    //             $discountAmount = ($roomCharges * $validated['discount_value']) / 100;
    //         } elseif ($validated['discount_type'] === 'fixed') {
    //             $discountAmount = min($validated['discount_value'], $roomCharges);
    //         }
    //     }

    //     // Calculate amounts
    //     $netRoomCharges = max(0, $roomCharges - $discountAmount);
    //     $gstPercentage  = $validated['gst_percentage'];
    //     $gstAmount      = ($netRoomCharges * $gstPercentage) / 100;
    //     $serviceTax     = $validated['service_tax'] ?? 0;
    //     $otherCharges   = $validated['other_charges'] ?? 0;
    //     $advance        = $validated['advance_payment'] ?? 0;

    //     // Calculate total and remaining
    //     $totalAmount = $netRoomCharges + $gstAmount + $serviceTax + $otherCharges;
    //     $remaining   = max(0, $totalAmount - $advance);

    //     // Determine payment status
    //     if ($advance == 0) {
    //         $paymentStatus = 'pending';
    //     } elseif ($remaining > 0) {
    //         $paymentStatus = 'partial';
    //     } else {
    //         $paymentStatus = 'paid';
    //     }

    //     // Update or create customer
    //     $customer = Customer::where('customer_mobile', $validated['customer_mobile'])->first();

    //     if (!$customer) {
    //         $customer = Customer::create([
    //             'customer_name'    => $validated['customer_name'],
    //             'customer_mobile'  => $validated['customer_mobile'],
    //             'customer_email'   => $validated['customer_email'],
    //             'customer_address' => $validated['customer_address'],
    //             'company_name'     => $validated['company_name'],
    //             'gst_number'       => $validated['gst_number'],
    //         ]);
    //     } else {
    //         // Update existing customer info
    //         $customer->update([
    //             'customer_name'    => $validated['customer_name'],
    //             'customer_email'   => $validated['customer_email'],
    //             'customer_address' => $validated['customer_address'],
    //             'company_name'     => $validated['company_name'],
    //             'gst_number'       => $validated['gst_number'],
    //         ]);
    //     }

    //     // Get old room IDs
    //     $oldRoomIds = $booking->rooms->pluck('id')->toArray();
    //     $newRoomIds = $validated['room_ids'];

    //     // Find rooms to remove and add
    //     $roomsToRemove = array_diff($oldRoomIds, $newRoomIds);
    //     $roomsToAdd    = array_diff($newRoomIds, $oldRoomIds);

    //     // Remove old rooms and update their status
    //     if (!empty($roomsToRemove)) {
    //         $booking->bookingRooms()->whereIn('room_id', $roomsToRemove)->delete();
    //         Room::whereIn('id', $roomsToRemove)->update(['status' => 'available']);
    //     }

    //     // Add new rooms
    //     if (!empty($roomsToAdd)) {
    //         $newRooms = Room::whereIn('id', $roomsToAdd)->get();

    //         foreach ($newRooms as $room) {
    //             $booking->bookingRooms()->create([
    //                 'room_id'    => $room->id,
    //                 'room_price' => $room->base_price
    //             ]);

    //             // Update room status
    //             $room->update(['status' => 'booked']);
    //         }
    //     }

    //     // Update booking
    //     $booking->update([
    //         'registration_no'      => $validated['registration_no'],
    //         'customer_id'          => $customer->id,
    //         'customer_name'        => $validated['customer_name'],
    //         'customer_mobile'      => $validated['customer_mobile'],
    //         'customer_email'       => $validated['customer_email'],
    //         'customer_address'     => $validated['customer_address'],
    //         'company_name'         => $validated['company_name'],
    //         'gst_number'           => $validated['gst_number'],

    //         'check_in'             => $checkIn,
    //         'check_out'            => $checkOut,
    //         'number_of_adults'     => $validated['number_of_adults'],
    //         'number_of_children'   => $validated['number_of_children'] ?? 0,
    //         'number_of_nights'     => $nights,

    //         'room_charges'         => $roomCharges,
    //         'discount_type'        => $validated['discount_type'],
    //         'discount_value'       => $validated['discount_value'] ?? 0,
    //         'discount_amount'      => $discountAmount,

    //         'gst_percentage'       => $gstPercentage,
    //         'gst_amount'           => $gstAmount,
    //         'service_tax'          => $serviceTax,
    //         'other_charges'        => $otherCharges,

    //         'total_amount'         => $totalAmount,
    //         'advance_payment'      => $advance,
    //         'remaining_amount'     => $remaining,
    //         'payment_status'       => $paymentStatus,
    //     ]);

    //     return redirect()
    //         ->route('bookings.show', $booking)
    //         ->with('success', 'Booking updated successfully!');
    // }

    // public function update(Request $request, Booking $booking)
    // {
    //     // ❌ Cancelled booking check
    //     if ($booking->booking_status === 'cancelled') {
    //         return back()->with('error', 'Cancelled booking cannot be updated.');
    //     }

    //     // ✅ Validation
    //     $validated = $request->validate([
    //         'registration_no'      => 'required|string|max:50',
    //         'customer_name'        => 'required|string|max:255',
    //         'customer_mobile'      => 'required|string|max:20',
    //         'customer_email'       => 'nullable|email',
    //         'customer_address'     => 'nullable|string',
    //         'company_name'         => 'nullable|string|max:255',
    //         'gst_number'           => 'nullable|string|max:15',

    //         'check_in'             => 'required|date',
    //         'check_out'            => 'required|date|after:check_in',
    //         'number_of_adults'     => 'required|integer|min:1',
    //         'number_of_children'   => 'nullable|integer|min:0',

    //         'room_ids'             => 'required|array|min:1',
    //         'room_ids.*'           => 'exists:rooms,id',

    //         'room_charges'         => 'required|numeric|min:0',
    //         'discount_type'        => 'nullable|in:percentage,fixed',
    //         'discount_value'       => 'nullable|numeric|min:0',
    //         'gst_percentage'       => 'required|numeric|min:0|max:100',
    //         'service_tax'          => 'nullable|numeric|min:0',
    //         'other_charges'        => 'nullable|numeric|min:0',

    //         // 🔥 ADD PAYMENT FIELDS
    //         'payment_amount'       => 'nullable|numeric|min:0',
    //         'payment_mode'         => 'nullable|string|max:50',
    //     ]);

    //     // 📅 Date calculations
    //     $checkIn  = Carbon::parse($validated['check_in']);
    //     $checkOut = Carbon::parse($validated['check_out']);
    //     $nights   = max(1, $checkIn->diffInDays($checkOut));

    //     // 💰 Discount calculation
    //     $roomCharges    = $validated['room_charges'];
    //     $discountAmount = 0;

    //     if (!empty($validated['discount_type']) && !empty($validated['discount_value'])) {
    //         if ($validated['discount_type'] === 'percentage') {
    //             $discountAmount = ($roomCharges * $validated['discount_value']) / 100;
    //         } else {
    //             $discountAmount = min($validated['discount_value'], $roomCharges);
    //         }
    //     }

    //     // 💵 Amount calculations
    //     $netRoomCharges = max(0, $roomCharges - $discountAmount);
    //     $gstAmount      = ($netRoomCharges * $validated['gst_percentage']) / 100;
    //     $serviceTax     = $validated['service_tax'] ?? 0;
    //     $otherCharges   = $validated['other_charges'] ?? 0;

    //     $totalAmount = $netRoomCharges + $gstAmount + $serviceTax + $otherCharges;

    //     // 💳 ADD PAYMENT LOGIC (CORE FIX)
    //     $paymentAmount = $validated['payment_amount'] ?? 0;

    //     $newAdvance = $booking->advance_payment + $paymentAmount;
    //     $remaining  = max(0, $totalAmount - $newAdvance);

    //     if ($newAdvance == 0) {
    //         $paymentStatus = 'pending';
    //     } elseif ($remaining > 0) {
    //         $paymentStatus = 'partial';
    //     } else {
    //         $paymentStatus = 'paid';
    //     }

    //     // 👤 Customer handling
    //     $customer = Customer::updateOrCreate(
    //         ['customer_mobile' => $validated['customer_mobile']],
    //         [
    //             'customer_name'    => $validated['customer_name'],
    //             'customer_email'   => $validated['customer_email'],
    //             'customer_address' => $validated['customer_address'],
    //             'company_name'     => $validated['company_name'],
    //             'gst_number'       => $validated['gst_number'],
    //         ]
    //     );

    //     // 🏨 Room sync
    //     $oldRoomIds = $booking->rooms->pluck('id')->toArray();
    //     $newRoomIds = $validated['room_ids'];

    //     $roomsToRemove = array_diff($oldRoomIds, $newRoomIds);
    //     $roomsToAdd    = array_diff($newRoomIds, $oldRoomIds);

    //     // return $newRoomIds;
    //     if ($roomsToRemove) {
    //         $booking->bookingRooms()->whereIn('room_id', $roomsToRemove)->delete();
    //         Room::whereIn('id', $roomsToRemove)->update(['status' => 'available']);
    //     }

    //     if ($newRoomIds) {
    //         foreach (Room::whereIn('id', $newRoomIds)->get() as $room) {
    //             $booking->bookingRooms()->create([
    //                 'room_id'    => $room->id,
    //                 'room_price' => $room->base_price,
    //             ]);
    //             $room->update(['status' => 'booked']);
    //         }
    //     }

    //     // ✅ Final Booking Update
    //     $booking->update([
    //         'registration_no'    => $validated['registration_no'],
    //         'customer_id'        => $customer->id,
    //         'customer_name'      => $validated['customer_name'],
    //         'customer_mobile'    => $validated['customer_mobile'],
    //         'customer_email'     => $validated['customer_email'],
    //         'customer_address'   => $validated['customer_address'],
    //         'company_name'       => $validated['company_name'],
    //         'gst_number'         => $validated['gst_number'],

    //         'check_in'           => $checkIn,
    //         'check_out'          => $checkOut,
    //         'number_of_adults'   => $validated['number_of_adults'],
    //         'number_of_children' => $validated['number_of_children'] ?? 0,
    //         'number_of_nights'   => $nights,

    //         'room_charges'       => $roomCharges,
    //         'discount_type'      => $validated['discount_type'],
    //         'discount_value'     => $validated['discount_value'] ?? 0,
    //         'discount_amount'    => $discountAmount,

    //         'gst_percentage'     => $validated['gst_percentage'],
    //         'gst_amount'         => $gstAmount,
    //         'service_tax'        => $serviceTax,
    //         'other_charges'      => $otherCharges,

    //         'total_amount'       => $totalAmount,
    //         'advance_payment'    => $newAdvance,
    //         'remaining_amount'   => $remaining,
    //         'payment_status'     => $paymentStatus,
    //         'payment_mode'       => $validated['payment_mode'] ?? $booking->payment_mode,
    //     ]);

    //     return redirect()
    //         ->route('bookings.show', $booking)
    //         ->with('success', 'Booking updated & payment added successfully!');
    // }

    public function update(Request $request, Booking $booking)
    {
        // ❌ Cancelled booking check
        if ($booking->booking_status === 'cancelled') {
            return back()->with('error', 'Cancelled booking cannot be updated.');
        }

        // ✅ Validation
        $validated = $request->validate([
            'registration_no'      => 'required|string|max:50',
            'customer_name'        => 'required|string|max:255',
            'customer_mobile'      => 'required|string|max:20',
            'customer_email'       => 'nullable|email',
            'customer_address'     => 'nullable|string',
            'company_name'         => 'nullable|string|max:255',
            'gst_number'           => 'nullable|string|max:15',

            'check_in'             => 'required|date',
            'check_out'            => 'required|date|after:check_in',
            'number_of_adults'     => 'required|integer|min:1',
            'number_of_children'   => 'nullable|integer|min:0',

            'room_ids'             => 'required|array|min:1',
            'room_ids.*'           => 'exists:rooms,id',

            'room_charges'         => 'required|numeric|min:0',
            'discount_type'        => 'nullable|in:percentage,fixed',
            'discount_value'       => 'nullable|numeric|min:0',
            'gst_percentage'       => 'required|numeric|min:0|max:100',
            'service_tax'          => 'nullable|numeric|min:0',
            'other_charges'        => 'nullable|numeric|min:0',

            'payment_amount'       => 'nullable|numeric|min:0',
            'payment_mode'         => 'nullable|string|max:50',
        ]);

        /* ================= DATES ================= */
        $checkIn  = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $nights   = max(1, $checkIn->diffInDays($checkOut));

        /* ================= DISCOUNT ================= */
        $roomCharges    = $validated['room_charges'];
        $discountAmount = 0;

        if (!empty($validated['discount_type']) && !empty($validated['discount_value'])) {
            $discountAmount = $validated['discount_type'] === 'percentage'
                ? ($roomCharges * $validated['discount_value']) / 100
                : min($validated['discount_value'], $roomCharges);
        }

        /* ================= AMOUNTS ================= */
        $netRoomCharges = max(0, $roomCharges - $discountAmount);
        $gstAmount      = ($netRoomCharges * $validated['gst_percentage']) / 100;
        $serviceTax     = $validated['service_tax'] ?? 0;
        $otherCharges   = $validated['other_charges'] ?? 0;

        $totalAmount = $netRoomCharges + $gstAmount + $serviceTax + $otherCharges;

        /* ================= PAYMENT LOGIC ================= */
        $paymentAmount = $validated['payment_amount'] ?? 0;

        // Add payment
        $newAdvance = $booking->advance_payment + $paymentAmount;
        $remaining  = max(0, $totalAmount - $newAdvance);

        if ($newAdvance == 0) {
            $paymentStatus = 'pending';
        } elseif ($remaining > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'paid';
        }

        // 🔥 FORCE WHEN PAID
        if ($request->payment_status === 'paid') {
            $paymentStatus = 'paid';
            $newAdvance = $totalAmount;
            $remaining  = 0;
        }

        /* ================= CUSTOMER ================= */
        $customer = Customer::updateOrCreate(
            ['customer_mobile' => $validated['customer_mobile']],
            [
                'customer_name'    => $validated['customer_name'],
                'customer_email'   => $validated['customer_email'],
                'customer_address' => $validated['customer_address'],
                'company_name'     => $validated['company_name'],
                'gst_number'       => $validated['gst_number'],
            ]
        );

        /* ================= ROOMS ================= */
        $oldRoomIds = $booking->rooms->pluck('id')->toArray();
        $newRoomIds = $validated['room_ids'];

        $roomsToRemove = array_diff($oldRoomIds, $newRoomIds);

        if ($roomsToRemove) {
            $booking->bookingRooms()->whereIn('room_id', $roomsToRemove)->delete();
            Room::whereIn('id', $roomsToRemove)->update(['status' => 'available']);
        }

        // Re-attach rooms cleanly
        $booking->bookingRooms()->delete();

        foreach (Room::whereIn('id', $newRoomIds)->get() as $room) {
            $booking->bookingRooms()->create([
                'room_id'    => $room->id,
                'room_price' => $room->base_price,
            ]);
            $room->update(['status' => 'booked']);
        }

        /* ================= FINAL UPDATE ================= */
        $booking->update([
            'registration_no'    => $validated['registration_no'],
            'customer_id'        => $customer->id,
            'customer_name'      => $validated['customer_name'],
            'customer_mobile'    => $validated['customer_mobile'],
            'customer_email'     => $validated['customer_email'],
            'customer_address'   => $validated['customer_address'],
            'company_name'       => $validated['company_name'],
            'gst_number'         => $validated['gst_number'],

            'check_in'           => $checkIn,
            'check_out'          => $checkOut,
            'number_of_adults'   => $validated['number_of_adults'],
            'number_of_children' => $validated['number_of_children'] ?? 0,
            'number_of_nights'   => $nights,

            'room_charges'       => $roomCharges,
            'discount_type'      => $validated['discount_type'],
            'discount_value'     => $validated['discount_value'] ?? 0,
            'discount_amount'    => $discountAmount,

            'gst_percentage'     => $validated['gst_percentage'],
            'gst_amount'         => $gstAmount,
            'service_tax'        => $serviceTax,
            'other_charges'      => $otherCharges,

            'total_amount'       => $totalAmount,
            'advance_payment'    => $newAdvance,
            'remaining_amount'   => $remaining,
            'payment_status'     => $paymentStatus,
            'payment_mode'       => $validated['payment_mode'] ?? $booking->payment_mode,
        ]);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Booking updated successfully!');
    }





    public function addExtraCharge(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'charge_type' => 'required|string',
            'description' => 'required|string',
            'amount'      => 'required|numeric|min:0',
            'charge_date' => 'required|date',
        ]);

        $booking->extraCharges()->create($validated);

        // Update booking totals
        $totalExtraCharges = $booking->extraCharges()->sum('amount');
        $newTotal          = $booking->room_charges + $booking->gst_amount +
            $booking->service_tax + $booking->other_charges + $totalExtraCharges;

        $booking->update([
            'extra_charges'    => $totalExtraCharges,
            'total_amount'     => $newTotal,
            'remaining_amount' => $newTotal - $booking->advance_payment,
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
        $newTotal          = $booking->room_charges + $booking->gst_amount +
            $booking->service_tax + $booking->other_charges + $totalExtraCharges;

        $booking->update([
            'extra_charges'    => $totalExtraCharges,
            'total_amount'     => $newTotal,
            'remaining_amount' => $newTotal - $booking->advance_payment,
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Extra charge deleted successfully!');
    }

    public function addPayment(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0',
            'payment_mode'   => 'required|in:cash,card,upi,bank_transfer',
        ]);

        $newAdvance = $booking->advance_payment + $validated['payment_amount'];
        $remaining  = $booking->total_amount - $newAdvance;

        $paymentStatus = 'pending';
        if ($newAdvance >= $booking->total_amount) {
            $paymentStatus = 'paid';
            $remaining     = 0;
        } elseif ($newAdvance > 0) {
            $paymentStatus = 'partial';
        }

        $booking->update([
            'advance_payment'  => $newAdvance,
            'remaining_amount' => $remaining,
            'payment_status'   => $paymentStatus,
            'payment_mode'     => $validated['payment_mode'],
        ]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Payment added successfully!');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string',
            'refund_amount'       => 'nullable|numeric|min:0',
        ]);

        $booking->update([
            'booking_status'      => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'refund_amount'       => $validated['refund_amount'] ?? 0,
        ]);

        // Make rooms available again
        foreach ($booking->rooms as $room) {
            $room->update(['status' => 'available']);
        }

        return redirect()->route('bookings.index', $booking)
            ->with('success', 'Booking cancelled successfully!');
    }

    public function checkIn(Booking $booking)
    {
        $booking->update(['booking_status' => 'checked_in']);

        return redirect()->route('bookings.index', $booking)
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
            'extraCharges',
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
