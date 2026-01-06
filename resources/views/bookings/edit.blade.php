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
            <label>Registration No *</label>
            <input type="text" name="registration_no"
                value="{{ old('registration_no', $booking->registration_no) }}"
                class="w-full border rounded px-3 py-2">
                  @error('registration_no')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
        </div>

        <div>
            <label>Customer Name *</label>
            <input type="text" name="customer_name"
                value="{{ old('customer_name', $booking->customer_name) }}"
                class="w-full border rounded px-3 py-2" >
                  @error('customer_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
        </div>

        <div>
            <label>Mobile *</label>
            <input type="text" name="customer_mobile"
                value="{{ old('customer_mobile', $booking->customer_mobile) }}"
                class="w-full border rounded px-3 py-2" >
                  @error('customer_mobile')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
        </div>
    </div>

    <div class="mb-4">
        <label>Email</label>
        <input type="email" name="customer_email"
            value="{{ old('customer_email', $booking->customer_email) }}"
            class="w-full border rounded px-3 py-2">
    </div>

    <div class="mb-4">
        <label>Address </label>
        <textarea name="customer_address"
            class="w-full border rounded px-3 py-2">{{ old('customer_address', $booking->customer_address) }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <input type="text" name="company_name"
            value="{{ $booking->company_name }}"
            class="border rounded px-3 py-2" placeholder="Company">

        <input type="text" name="gst_number"
            value="{{ $booking->gst_number }}"
            class="border rounded px-3 py-2" placeholder="GST Number">
    </div>
</div>

{{-- ================= BOOKING DETAILS ================= --}}
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Booking Details</h3>

    {{-- Dates --}}
    <div class="grid grid-cols-3 gap-4 mb-4">

        {{-- Check-in --}}
        <div>
            <label class="block text-sm mb-1">Check-in *</label>
            <input type="datetime-local"
                   name="check_in"
                   value="{{ old('check_in', optional($booking->check_in)->format('Y-m-d\TH:i')) }}"
                   class="w-full px-3 py-2 border rounded">
            @error('check_in')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Check-out --}}
        <div>
            <label class="block text-sm mb-1">Check-out *</label>
            <input type="datetime-local"
                   name="check_out"
                   value="{{ old('check_out', optional($booking->check_out)->format('Y-m-d\TH:i')) }}"
                   class="w-full px-3 py-2 border rounded">
            @error('check_out')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Adults --}}
        <div>
            <label class="block text-sm mb-1">Adults</label>
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
            <label class="block text-sm mb-1">Children</label>
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

    {{-- ROOMS --}}
    <div class="mb-4">
        <label class="block text-sm font-medium mb-2">Select Rooms *</label>

        <div class="grid grid-cols-2 gap-4 max-h-64 overflow-y-auto border rounded p-4">

            @foreach ($rooms as $room)
                <label class="flex items-center p-3 border rounded hover:bg-gray-50">

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
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>


</div>

{{-- ================= RIGHT SECTION ================= --}}
<div>
<div class="bg-white rounded-lg shadow p-6 sticky top-4">

<h3 class="text-lg font-semibold mb-4">Payment Summary</h3>

{{-- <div class="mb-3">
    <label>Room Charges *</label>
    <input type="number" step="0.01" id="room_charges" name="room_charges"
        value="{{ $booking->room_charges }}"
        class="w-full border rounded px-3 py-2 bg-gray-100"
        readonly>
</div> --}}

<div class="mb-3">
    <label>Room Charges *</label>
    <input type="number" step="0.01" id="room_charges" name="room_charges"
        value="{{ $booking->room_charges }}"
        class="w-full border rounded px-3 py-2">
</div>


<div class="mb-3">
    <label>Discount</label>

    <select id="discount_type" name="discount_type"
        class="w-full border rounded px-3 py-2 mb-2">
        <option value="">No Discount</option>
        <option value="percentage" @selected($booking->discount_type=='percentage')>Percentage (%)</option>
        <option value="fixed" @selected($booking->discount_type=='fixed')>Flat Amount (₹)</option>
    </select>

    <input type="number" step="0.01" id="discount_value" name="discount_value"
        value="{{ $booking->discount_value ?? 0 }}"
        class="w-full border rounded px-3 py-2 mb-2">

    <input type="text" id="discount_amount"
        class="w-full border rounded px-3 py-2 bg-gray-100 text-green-600 font-semibold"
        readonly>
</div>

<div class="mb-3">
    <label>GST %</label>
    <input type="number" step="0.01" id="gst_percentage" name="gst_percentage"
        value="{{ $booking->gst_percentage }}"
        class="w-full border rounded px-3 py-2">
</div>

<div class="mb-3">
    <label>GST Amount</label>
    <input type="text" id="gst_amount"
        class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
</div>

<div class="mb-3">
    <label>Service Tax</label>
    <input type="number" step="0.01" id="service_tax" name="service_tax"
        value="{{ $booking->service_tax ?? 0 }}"
        class="w-full border rounded px-3 py-2">
</div>

<div class="mb-3">
    <label>Other Charges</label>
    <input type="number" step="0.01" id="other_charges" name="other_charges"
        value="{{ $booking->other_charges ?? 0 }}"
        class="w-full border rounded px-3 py-2">
</div>

{{-- <div class="mb-3">
    <label>Advance Paid</label>
    <input type="number" step="0.01" id="advance_payment" name="advance_payment"
        value="{{ $booking->advance_payment }}"
        class="w-full border rounded px-3 py-2">
</div>

<div class="mb-4">
    <label class="text-red-600">Remaining Amount</label>
    <input type="text" id="remaining_amount"
        value="{{ number_format($booking->remaining_amount,2) }}"
        class="w-full border rounded px-3 py-2 bg-gray-100 text-red-600 font-bold"
        readonly>
</div>

<div class="mb-4">
    <label>Payment Status</label>
    <select id="payment_status" name="payment_status"
        class="w-full border rounded px-3 py-2">
        <option value="pending" @selected($booking->payment_status=='pending')>Pending</option>
        <option value="partial" @selected($booking->payment_status=='partial')>Partial</option>
        <option value="paid" @selected($booking->payment_status=='paid')>Paid</option>
    </select>
</div> --}}

<div class="mb-3">
    <label>Advance Paid</label>
    <input type="number" step="0.01"
           id="advance_payment"
           name="advance_payment"
           value="{{ $booking->advance_payment }}"
           class="w-full border rounded px-3 py-2">
</div>

<div class="mb-3">
    <label class="text-red-600">Remaining Amount</label>
    <input type="text"
           id="remaining_amount"
           value="{{ number_format($booking->remaining_amount,2) }}"
           class="w-full border rounded px-3 py-2 bg-gray-100 text-red-600 font-bold"
           readonly>
</div>

<div class="mb-3">
    <label>Payment Status</label>
    <select
    id="payment_status"
            name="payment_status"
            class="w-full border rounded px-3 py-2">
        <option value="pending" @selected($booking->payment_status=='pending')>Pending</option>
        <option value="partial" @selected($booking->payment_status=='partial')>Partial</option>
        <option value="paid" @selected($booking->payment_status=='paid')>Paid</option>
    </select>
</div>

<button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded font-semibold">
    Update Booking
</button>

</div>
</div>
</div>
</form>

{{-- ================= SCRIPT ================= --}}
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
    // remaining_amount.value = Math.max(0, remaining).toFixed(2);

    // payment_status.value =
    //     remaining <= 0 ? 'paid' :
    //     advance > 0 ? 'partial' : 'pending';
}
</script>


{{-- <script>
document.addEventListener('DOMContentLoaded', function () {

    const roomCheckboxes = document.querySelectorAll('.room-checkbox');
    const roomChargesInput = document.getElementById('room_charges');

    function updateRoomCharges() {
        let total = 0;

        roomCheckboxes.forEach(cb => {
            if (cb.checked) {
                total += parseFloat(cb.dataset.price || 0);
            }
        });

        roomChargesInput.value = total.toFixed(2);

        // mark as user-changed so calculation runs
        userChanged = true;
        calculatePayment();
    }

    // attach change event
    roomCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateRoomCharges);
    });

    // calculate on page load (edit booking)
    updateRoomCharges();
});
</script> --}}

{{-- <script>
document.addEventListener('DOMContentLoaded', function () {

    const roomCheckboxes = document.querySelectorAll('.room-checkbox');

    const room_charges     = document.getElementById('room_charges');
    const discount_type    = document.getElementById('discount_type');
    const discount_value   = document.getElementById('discount_value');
    const gst_percentage   = document.getElementById('gst_percentage');
    const service_tax      = document.getElementById('service_tax');
    const other_charges    = document.getElementById('other_charges');
    const advance_payment  = document.getElementById('advance_payment');
    const discount_amount  = document.getElementById('discount_amount');
    const gst_amount       = document.getElementById('gst_amount');
    const remaining_amount = document.getElementById('remaining_amount');
    const payment_status   = document.getElementById('payment_status');

    let manualRoomCharge = false;

    // 🔹 If admin edits room charges manually
    room_charges.addEventListener('input', () => {
        manualRoomCharge = true;
        calculatePayment();
    });

    // 🔹 Auto calculate room charges from selected rooms
    function updateRoomChargesFromRooms() {
        if (manualRoomCharge) return;

        let total = 0;
        roomCheckboxes.forEach(cb => {
            if (cb.checked) {
                total += parseFloat(cb.dataset.price || 0);
            }
        });

        room_charges.value = total.toFixed(2);
        calculatePayment();
    }

    roomCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            manualRoomCharge = false; // rooms change → auto mode
            updateRoomChargesFromRooms();
        });
    });

    // 🔹 Main payment calculation
    function calculatePayment() {

        let room    = +room_charges.value || 0;
        let gstP    = +gst_percentage.value || 0;
        let service = +service_tax.value || 0;
        let other   = +other_charges.value || 0;
        let advance = +advance_payment.value || 0;

        let dType = discount_type.value;
        let dVal  = +discount_value.value || 0;

        let discount = 0;
        if (dType === 'percentage') discount = room * dVal / 100;
        if (dType === 'fixed') discount = Math.min(dVal, room);

        let taxable  = room - discount;
        let gst      = taxable * gstP / 100;
        let total    = taxable + gst + service + other;
        let remaining = total - advance;

        discount_amount.value  = discount.toFixed(2);
        gst_amount.value       = gst.toFixed(2);
        remaining_amount.value = Math.max(0, remaining).toFixed(2);

        payment_status.value =
            remaining <= 0 ? 'paid' :
            advance > 0 ? 'partial' : 'pending';
    }

    // 🔹 Attach calculation events
    [
        discount_type, discount_value,
        gst_percentage, service_tax,
        other_charges, advance_payment
    ].forEach(el => el.addEventListener('input', calculatePayment));

    // 🔹 Initial load (edit booking)
    updateRoomChargesFromRooms();
});
</script> --}}


{{-- <script>
document.addEventListener('DOMContentLoaded', function () {

    const roomCheckboxes = document.querySelectorAll('.room-checkbox');

    const room_charges   = document.getElementById('room_charges');
    const discount_type  = document.getElementById('discount_type');
    const discount_value = document.getElementById('discount_value');
    const gst_percentage = document.getElementById('gst_percentage');
    const service_tax    = document.getElementById('service_tax');
    const other_charges  = document.getElementById('other_charges');
    const discount_amount = document.getElementById('discount_amount');
    const gst_amount      = document.getElementById('gst_amount');

    let manualRoomCharge = false;

    room_charges.addEventListener('input', () => {
        manualRoomCharge = true;
        calculate();
    });

    function updateRoomCharges() {
        if (manualRoomCharge) return;

        let total = 0;
        roomCheckboxes.forEach(cb => {
            if (cb.checked) {
                total += parseFloat(cb.dataset.price || 0);
            }
        });

        room_charges.value = total.toFixed(2);
        calculate();
    }

    roomCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            manualRoomCharge = false;
            updateRoomCharges();
        });
    });

    function calculate() {
        let room = +room_charges.value || 0;
        let gstP = +gst_percentage.value || 0;
        let service = +service_tax.value || 0;
        let other = +other_charges.value || 0;

        let dType = discount_type.value;
        let dVal  = +discount_value.value || 0;

        let discount = 0;
        if (dType === 'percentage') discount = room * dVal / 100;
        if (dType === 'fixed') discount = Math.min(dVal, room);

        let taxable = room - discount;
        let gst = taxable * gstP / 100;

        discount_amount.value = discount.toFixed(2);
        gst_amount.value = gst.toFixed(2);
    }

    [
        discount_type,
        discount_value,
        gst_percentage,
        service_tax,
        other_charges
    ].forEach(el => el.addEventListener('input', calculate));

    updateRoomCharges();
});
</script> --}}


<script>
document.addEventListener('DOMContentLoaded', function () {

    const roomCheckboxes = document.querySelectorAll('.room-checkbox');

    const checkIn        = document.querySelector('input[name="check_in"]');
    const checkOut       = document.querySelector('input[name="check_out"]');

    const roomCharges    = document.getElementById('room_charges');
    const discountType   = document.getElementById('discount_type');
    const discountValue  = document.getElementById('discount_value');
    const gstPercentage  = document.getElementById('gst_percentage');

    const discountAmount = document.getElementById('discount_amount');
    const gstAmount      = document.getElementById('gst_amount');

    /* 🔹 Calculate number of nights */
    function getNights() {
        if (!checkIn.value || !checkOut.value) return 0;

        const inDate  = new Date(checkIn.value);
        const outDate = new Date(checkOut.value);

        const diff = outDate - inDate;
        const nights = diff / (1000 * 60 * 60 * 24);

        return nights > 0 ? nights : 1;

        // return nights > 0 ? nights : 0;
    }

    /* 🔹 Calculate room charges based on dates & rooms */
    function calculateRoomCharges() {
        const nights = getNights();
        let perNightTotal = 0;

        roomCheckboxes.forEach(cb => {
            if (cb.checked) {
                perNightTotal += parseFloat(cb.dataset.price || 0);
            }
        });

        const total = nights * perNightTotal;
        roomCharges.value = total.toFixed(2);

        calculateDiscountGST();
    }

    /* 🔹 Discount + GST (display only) */
    function calculateDiscountGST() {

        let room = +roomCharges.value || 0;
        let gstP = +gstPercentage.value || 0;
        let dVal = +discountValue.value || 0;

        let discount = 0;
        if (discountType.value === 'percentage') {
            discount = room * dVal / 100;
        }
        if (discountType.value === 'fixed') {
            discount = Math.min(dVal, room);
        }

        let taxable = room - discount;
        let gst = taxable * gstP / 100;

        discountAmount.value = discount.toFixed(2);
        gstAmount.value = gst.toFixed(2);
    }

    /* 🔹 Events */
    roomCheckboxes.forEach(cb =>
        cb.addEventListener('change', calculateRoomCharges)
    );

    checkIn.addEventListener('change', calculateRoomCharges);
    checkOut.addEventListener('change', calculateRoomCharges);

    [discountType, discountValue, gstPercentage]
        .forEach(el => el.addEventListener('input', calculateDiscountGST));

    /* 🔹 Initial load (Edit booking) */
    calculateRoomCharges();
});
</script>


@endsection
