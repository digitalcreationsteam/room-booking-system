@extends('layouts.app')

@section('title', 'Edit Booking')
@section('header', 'Edit Booking')

@section('content')

@if($booking->booking_status === 'cancelled')
    <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
        This booking has been cancelled and cannot be edited.
    </div>
@endif

<form action="{{ route('bookings.update', $booking->id) }}" method="POST">
@csrf
@method('PUT')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

{{-- ================= LEFT SECTION ================= --}}
<div class="lg:col-span-2">

{{-- CUSTOMER DETAILS --}}
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Customer Details</h3>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium mb-1">Registration No *</label>
            <input type="text" name="registration_no"
                value="{{ old('registration_no', $booking->registration_no) }}"
                class="w-full border rounded px-3 py-2 @error('registration_no') border-red-500 @enderror">
            @error('registration_no')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Customer Name *</label>
            <input type="text" name="customer_name"
                value="{{ old('customer_name', $booking->customer_name) }}"
                class="w-full border rounded px-3 py-2 @error('customer_name') border-red-500 @enderror">
            @error('customer_name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Mobile *</label>
            <input type="text" name="customer_mobile"
                value="{{ old('customer_mobile', $booking->customer_mobile) }}"
                class="w-full border rounded px-3 py-2 @error('customer_mobile') border-red-500 @enderror">
            @error('customer_mobile')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="customer_email"
            value="{{ old('customer_email', $booking->customer_email) }}"
            class="w-full border rounded px-3 py-2">
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Address</label>
        <textarea name="customer_address" rows="2"
            class="w-full border rounded px-3 py-2">{{ old('customer_address', $booking->customer_address) }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Company Name</label>
            <input type="text" name="company_name"
                value="{{ old('company_name', $booking->company_name) }}"
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">GST Number</label>
            <input type="text" name="gst_number"
                value="{{ old('gst_number', $booking->gst_number) }}"
                class="w-full border rounded px-3 py-2">
        </div>
    </div>
</div>

{{-- ================= BOOKING DETAILS ================= --}}
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Booking Details</h3>

    {{-- Dates --}}
    <div class="grid grid-cols-3 gap-4 mb-4">

        {{-- Check-in --}}
        <div>
            <label class="block text-sm font-medium mb-1">Check-in *</label>
            <input type="datetime-local"
                   name="check_in"
                   value="{{ old('check_in', optional($booking->check_in)->format('Y-m-d\TH:i')) }}"
                   class="w-full px-3 py-2 border rounded @error('check_in') border-red-500 @enderror">
            @error('check_in')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Check-out --}}
        <div>
            <label class="block text-sm font-medium mb-1">Check-out *</label>
            <input type="datetime-local"
                   name="check_out"
                   value="{{ old('check_out', optional($booking->check_out)->format('Y-m-d\TH:i')) }}"
                   class="w-full px-3 py-2 border rounded @error('check_out') border-red-500 @enderror">
            @error('check_out')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Adults --}}
        <div>
            <label class="block text-sm font-medium mb-1">Adults</label>
            <input type="number"
                   name="number_of_adults"
                   min="1"
                   value="{{ old('number_of_adults', $booking->number_of_adults) }}"
                   class="w-full px-3 py-2 border rounded">
            @error('number_of_adults')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Children --}}
        <div>
            <label class="block text-sm font-medium mb-1">Children</label>
            <input type="number"
                   name="number_of_children"
                   min="0"
                   value="{{ old('number_of_children', $booking->number_of_children) }}"
                   class="w-full px-3 py-2 border rounded">
            @error('number_of_children')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- 🔍 ROOM SEARCH --}}
    <div class="mb-3">
        <input type="text" id="roomSearch"
            placeholder="Search room no / type / price"
            class="w-full border rounded px-3 py-2">
    </div>

    {{-- ROOMS --}}
    <div class="mb-4">
        <label class="block text-sm font-medium mb-2">Select Rooms *</label>

        <div class="grid grid-cols-2 gap-4 max-h-64 overflow-y-auto border rounded p-4
            @error('room_ids') border-red-500 @enderror">

            @foreach ($rooms as $room)
                <label class="room-item flex items-center p-3 border rounded hover:bg-gray-50"
                    data-room="{{ strtolower($room->room_number) }}"
                    data-type="{{ strtolower($room->roomType->name) }}"
                    data-price="{{ $room->base_price }}">

                   <input type="checkbox"
                        name="room_ids[]"
                        value="{{ $room->id }}"
                        class="mr-3 room-checkbox"
                        data-price="{{ $room->base_price }}"
                        {{ in_array($room->id, old('room_ids', $booking->rooms->pluck('id')->toArray())) ? 'checked' : '' }}>

                    <div>
                        <div class="font-semibold">Room {{ $room->room_number }}</div>
                        <div class="text-sm text-gray-600">{{ $room->roomType->name }}</div>
                        <div class="text-sm text-blue-600">
                            ₹{{ number_format($room->base_price, 2) }}
                        </div>
                    </div>
                </label>
            @endforeach

        </div>

        @error('room_ids')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>


</div>

{{-- ================= RIGHT SECTION ================= --}}
<div>
<div class="bg-white rounded-lg shadow p-6 sticky top-4">

<h3 class="text-lg font-semibold mb-4">Payment Summary</h3>

<div class="mb-3">
    <label class="block text-sm font-medium mb-1">Room Charges *</label>
    <input type="number" step="0.01" id="room_charges" name="room_charges"
        value="{{ old('room_charges', $booking->room_charges) }}"
        class="w-full border rounded px-3 py-2">
</div>

<div class="mb-3">
    <label class="block text-sm font-medium mb-1">Discount</label>

    <select id="discount_type" name="discount_type"
        class="w-full border rounded px-3 py-2 mb-2">
        <option value="">No Discount</option>
        <option value="percentage" {{ old('discount_type', $booking->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
        <option value="fixed" {{ old('discount_type', $booking->discount_type) == 'fixed' ? 'selected' : '' }}>Flat Amount (₹)</option>
    </select>

    <input type="number" step="0.01" id="discount_value" name="discount_value"
        value="{{ old('discount_value', $booking->discount_value ?? 0) }}"
        class="w-full border rounded px-3 py-2 mb-2"
        placeholder="Enter discount value">

    <input type="text" id="discount_amount"
        class="w-full border rounded px-3 py-2 bg-gray-100 text-green-600 font-semibold"
        placeholder="Discount amount"
        readonly>
</div>

<div class="mb-3">
    <label class="block text-sm font-medium mb-1">GST %</label>
    <input type="number" step="0.01" id="gst_percentage" name="gst_percentage"
        value="{{ old('gst_percentage', $booking->gst_percentage) }}"
        class="w-full border rounded px-3 py-2">
</div>

<div class="mb-3">
    <label class="block text-sm font-medium mb-1">GST Amount</label>
    <input type="text" id="gst_amount"
        class="w-full border rounded px-3 py-2 bg-gray-100"
        placeholder="GST amount"
        readonly>
</div>

<div class="mb-3">
    <label class="block text-sm font-medium mb-1">Service Tax</label>
    <input type="number" step="0.01" id="service_tax" name="service_tax"
        value="{{ old('service_tax', $booking->service_tax ?? 0) }}"
        class="w-full border rounded px-3 py-2">
</div>

<div class="mb-3">
    <label class="block text-sm font-medium mb-1">Other Charges</label>
    <input type="number" step="0.01" id="other_charges" name="other_charges"
        value="{{ old('other_charges', $booking->other_charges ?? 0) }}"
        class="w-full border rounded px-3 py-2">
</div>

<div class="mb-3">
    <label class="block text-sm font-medium mb-1">Advance Paid</label>
    <input type="number" step="0.01"
           id="advance_payment"
           name="advance_payment"
           value="{{ old('advance_payment', $booking->advance_payment) }}"
           class="w-full border rounded px-3 py-2">
</div>

<div class="mb-3">
    <label class="block text-sm font-medium mb-1 text-red-600">Remaining Amount</label>
    <input type="text"
           id="remaining_amount"
           value="{{ number_format($booking->remaining_amount, 2) }}"
           class="w-full border rounded px-3 py-2 bg-gray-100 text-red-600 font-bold"
           readonly>
</div>

<div class="mb-3">
    <label class="block text-sm font-medium mb-1">Payment Status</label>
    <select id="payment_status"
            name="payment_status"
            class="w-full border rounded px-3 py-2">
        <option value="pending" {{ old('payment_status', $booking->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="partial" {{ old('payment_status', $booking->payment_status) == 'partial' ? 'selected' : '' }}>Partial</option>
        <option value="paid" {{ old('payment_status', $booking->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
    </select>
</div>

<button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded font-semibold">
    Update Booking
</button>

<a href="{{ route('bookings.show', $booking) }}"
    class="block text-center mt-3 text-gray-600 hover:text-gray-800">Cancel</a>

</div>
</div>
</div>
</form>

{{-- ================= ROOM SEARCH SCRIPT ================= --}}
<script>
document.getElementById('roomSearch').addEventListener('keyup', function () {
    let q = this.value.toLowerCase();
    document.querySelectorAll('.room-item').forEach(room => {
        let match =
            room.dataset.room.includes(q) ||
            room.dataset.type.includes(q) ||
            room.dataset.price.includes(q);
        room.style.display = match ? 'flex' : 'none';
    });
});
</script>

{{-- ================= PAYMENT CALCULATION SCRIPT ================= --}}
<script>
let userChanged = false;

[
    room_charges, discount_type, discount_value,
    gst_percentage, service_tax, other_charges, advance_payment
].forEach(el => {
    el.addEventListener('input', () => {
        userChanged = true;
        calculatePayment();
    });
});

function calculatePayment() {

    if (!userChanged) return;

    let room = +room_charges.value || 0;
    let gstP = +gst_percentage.value || 0;
    let service = +service_tax.value || 0;
    let other = +other_charges.value || 0;
    let advance = +advance_payment.value || 0;

    let dType = discount_type.value;
    let dVal = +discount_value.value || 0;

    let discount = 0;
    if (dType === 'percentage') discount = room * dVal / 100;
    if (dType === 'fixed') discount = Math.min(dVal, room);

    let taxable = room - discount;
    let gst = taxable * gstP / 100;
    let total = taxable + gst + service + other;
    let remaining = total - advance;

    discount_amount.value = discount.toFixed(2);
    gst_amount.value = gst.toFixed(2);
    remaining_amount.value = remaining.toFixed(2);
}
</script>

{{-- ================= AUTO ROOM CALCULATION SCRIPT ================= --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    const roomCheckboxes = document.querySelectorAll('.room-checkbox');

    const checkIn        = document.querySelector('[name="check_in"]');
    const checkOut       = document.querySelector('[name="check_out"]');

    const roomCharges    = document.getElementById('room_charges');
    const discountType   = document.getElementById('discount_type');
    const discountValue  = document.getElementById('discount_value');
    const gstPercentage  = document.getElementById('gst_percentage');
    const serviceTax     = document.getElementById('service_tax');
    const otherCharges   = document.getElementById('other_charges');
    const advanceInput   = document.getElementById('advance_payment');

    const discountAmount = document.getElementById('discount_amount');
    const gstAmount      = document.getElementById('gst_amount');
    const remainingField = document.getElementById('remaining_amount');

    /* 🟢 Calculate nights */
    // function getNights() {
    //     if (!checkIn.value || !checkOut.value) return 1;

    //     const inDate  = new Date(checkIn.value);
    //     const outDate = new Date(checkOut.value);

    //     const diff = outDate - inDate;
    //     const nights = diff / (1000 * 60 * 60 * 24);

    //     return nights > 0 ? nights : 1;
    // }
function getNights() {
    if (!checkIn.value || !checkOut.value) return 1;

    const inDate  = new Date(checkIn.value);
    const outDate = new Date(checkOut.value);

    // milliseconds difference
    const diffMs = outDate.getTime() - inDate.getTime();

    // invalid or same time
    if (diffMs <= 0) return 1;

    // convert ms → days (with time)
    const diffDays = diffMs / (1000 * 60 * 60 * 24);

    // 🔥 CEIL ensures time-based billing
    return Math.max(1, Math.ceil(diffDays));
}

    /* 🟢 MASTER CALCULATION */
    function calculateAll() {

        const nights = getNights();

        // Room total
        let perNightTotal = 0;
        roomCheckboxes.forEach(cb => {
            if (cb.checked) {
                perNightTotal += parseFloat(cb.dataset.price || 0);
            }
        });

        let roomTotal = perNightTotal * nights;
        roomCharges.value = roomTotal.toFixed(2);

        // Discount
        let discount = 0;
        const dVal = +discountValue.value || 0;

        if (discountType.value === 'percentage') {
            discount = roomTotal * dVal / 100;
        }
        if (discountType.value === 'fixed') {
            discount = Math.min(dVal, roomTotal);
        }

        discountAmount.value = discount.toFixed(2);

        // GST
        const gst = (roomTotal - discount) * (+gstPercentage.value || 0) / 100;
        gstAmount.value = gst.toFixed(2);

        // Total
        const service = +serviceTax.value || 0;
        const other   = +otherCharges.value || 0;
        const advance = +advanceInput.value || 0;

        const total = (roomTotal - discount) + gst + service + other;
        const remaining = Math.max(0, total - advance);

        remainingField.value = remaining.toFixed(2);
    }

    /* 🔁 EVENTS */
    roomCheckboxes.forEach(cb => cb.addEventListener('change', calculateAll));
    checkIn.addEventListener('change', calculateAll);
    checkOut.addEventListener('change', calculateAll);

    [
        discountType,
        discountValue,
        gstPercentage,
        serviceTax,
        otherCharges,
        advanceInput
    ].forEach(el => el.addEventListener('input', calculateAll));

    /* 🚀 Initial load */
    calculateAll();
});
</script>

@endsection
