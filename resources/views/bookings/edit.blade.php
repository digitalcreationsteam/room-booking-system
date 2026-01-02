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
            <label>Customer Name *</label>
            <input type="text" name="customer_name"
                   value="{{ old('customer_name', $booking->customer_name) }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label>Mobile *</label>
            <input type="text" name="customer_mobile"
                   value="{{ old('customer_mobile', $booking->customer_mobile) }}"
                   class="w-full border rounded px-3 py-2" required>
        </div>
    </div>

    <div class="mb-4">
        <label>Email</label>
        <input type="email" name="customer_email"
               value="{{ old('customer_email', $booking->customer_email) }}"
               class="w-full border rounded px-3 py-2">
    </div>

    <div class="mb-4">
        <label>Address *</label>
        <textarea name="customer_address" required
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

{{-- BOOKING DETAILS --}}
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Booking Details</h3>

    <div class="grid grid-cols-3 gap-4">
        <input type="number" name="number_of_adults" min="1"
               value="{{ $booking->number_of_adults }}"
               class="border rounded px-3 py-2" placeholder="Adults">
        <input type="number" name="number_of_children" min="0"
               value="{{ $booking->number_of_children }}"
               class="border rounded px-3 py-2" placeholder="Children">
    </div>
</div>

</div>

{{-- ================= RIGHT SECTION ================= --}}
<div>
<div class="bg-white rounded-lg shadow p-6 sticky top-4">
<h3 class="text-lg font-semibold mb-4">Payment Summary</h3>

{{-- ROOM --}}
<div class="mb-3">
    <label>Room Charges *</label>
    <input type="number" step="0.01" id="room_charges"
           name="room_charges"
           value="{{ $booking->room_charges }}"
           class="w-full border rounded px-3 py-2">
</div>

{{-- DISCOUNT (STANDARD UI) --}}
<div class="mb-3 border rounded p-3 bg-gray-50">
    <label class="font-medium block mb-2">Discount</label>

    <select id="discount_type" name="discount_type"
            class="w-full border rounded px-3 py-2 mb-2">
        <option value="">No Discount</option>
        <option value="percentage" @selected($booking->discount_type=='percentage')>
            Percentage (%)
        </option>
        <option value="fixed" @selected($booking->discount_type=='fixed')>
            Flat Amount (₹)
        </option>
    </select>

    <input type="number" step="0.01" min="0"
           id="discount_value" name="discount_value"
           value="{{ $booking->discount_value ?? 0 }}"
           class="w-full border rounded px-3 py-2 mb-2"
           placeholder="Discount Value">

    <input type="text" id="discount_amount"
           class="w-full border rounded px-3 py-2 bg-gray-100 text-green-600 font-semibold"
           readonly placeholder="Discount Amount">
</div>

{{-- GST --}}
<div class="mb-3">
    <label>GST %</label>
    <input type="number" step="0.01" id="gst_percentage"
           name="gst_percentage"
           value="{{ $booking->gst_percentage }}"
           class="w-full border rounded px-3 py-2">
</div>

<div class="mb-3">
    <label>GST Amount</label>
    <input type="text" id="gst_amount"
           class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
</div>

{{-- OTHER --}}
<div class="mb-3">
    <label>Service Tax</label>
    <input type="number" step="0.01" id="service_tax"
           name="service_tax"
           value="{{ $booking->service_tax ?? 0 }}"
           class="w-full border rounded px-3 py-2">
</div>

<div class="mb-3">
    <label>Other Charges</label>
    <input type="number" step="0.01" id="other_charges"
           name="other_charges"
           value="{{ $booking->other_charges ?? 0 }}"
           class="w-full border rounded px-3 py-2">
</div>

{{-- PAYMENT --}}
<div class="mb-3">
    <label>Advance Paid</label>
    <input type="number" step="0.01" id="advance_payment"
           name="advance_payment"
           value="{{ $booking->advance_payment }}"
           class="w-full border rounded px-3 py-2">
</div>

<div class="mb-4">
    <label class="text-red-600">Remaining Amount</label>
    <input type="text" id="remaining_amount"
           class="w-full border rounded px-3 py-2 bg-gray-100 text-red-600 font-bold"
           readonly>
</div>

<div class="mb-4">
    <label>Payment Status</label>
    <select id="payment_status" name="payment_status"
            class="w-full border rounded px-3 py-2">
        <option value="pending">Pending</option>
        <option value="partial">Partial</option>
        <option value="paid">Paid</option>
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
function calculatePayment() {

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
    remaining_amount.value = Math.max(0, remaining).toFixed(2);

    payment_status.value =
        remaining <= 0 ? 'paid' :
        advance > 0 ? 'partial' : 'pending';
}

document.querySelectorAll('input,select')
    .forEach(el => el.addEventListener('input', calculatePayment));

calculatePayment();
</script>

@endsection
