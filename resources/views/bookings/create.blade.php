@extends('layouts.app')

@section('title', 'Create Booking')
@section('header', 'Create New Booking')

@section('content')

<form action="{{ route('bookings.store') }}" method="POST">
@csrf

<input type="hidden" id="customer_id" name="customer_id">

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

{{-- ================= LEFT SIDE ================= --}}
<div class="lg:col-span-2">

{{-- ================= CUSTOMER DETAILS ================= --}}
<div class="bg-white rounded-lg shadow p-6 mb-6">
<h3 class="text-lg font-semibold mb-4">Customer Details</h3>

{{-- NAME + MOBILE --}}
<div class="grid grid-cols-2 gap-4 mb-4">
<div>
<label class="block text-sm font-medium mb-1">Customer Name *</label>
<input type="text" name="customer_name"
value="{{ old('customer_name') }}"
class="w-full px-3 py-2 border rounded @error('customer_name') border-red-500 @enderror">

@error('customer_name')
<p class="text-red-600 text-sm mt-1">{{ $message }}</p>
@enderror
</div>

<div class="relative">
<label class="block text-sm font-medium mb-1">Mobile Number *</label>
<input type="text"
id="customer_mobile"
name="customer_mobile"
autocomplete="off"
value="{{ old('customer_mobile') }}"
class="w-full px-3 py-2 border rounded @error('customer_mobile') border-red-500 @enderror">

<div id="customerResults"
class="absolute bg-white border rounded shadow w-full mt-1 hidden z-50 max-h-60 overflow-y-auto">
</div>

@error('customer_mobile')
<p class="text-red-600 text-sm mt-1">{{ $message }}</p>
@enderror
</div>
</div>

{{-- EMAIL --}}
<div class="mb-4">
<label class="block text-sm font-medium mb-1">Email</label>
<input type="email" name="customer_email"
value="{{ old('customer_email') }}"
class="w-full px-3 py-2 border rounded">
</div>

{{-- ADDRESS --}}
<div class="mb-4">
<label class="block text-sm font-medium mb-1">Address *</label>
<textarea name="customer_address" rows="2"
class="w-full px-3 py-2 border rounded">{{ old('customer_address') }}</textarea>
</div>

{{-- ID PROOF --}}
<div class="grid grid-cols-2 gap-4 mb-4">
<div>
<label class="block text-sm font-medium mb-1">ID Proof Type</label>
<select name="id_proof_type" class="w-full px-3 py-2 border rounded">
<option value="">Select</option>
<option value="aadhar">Aadhar</option>
<option value="pan">PAN</option>
<option value="passport">Passport</option>
<option value="driving_license">Driving License</option>
</select>
</div>

<div>
<label class="block text-sm font-medium mb-1">ID Proof Number</label>
<input type="text" name="id_proof_number"
class="w-full px-3 py-2 border rounded">
</div>
</div>

{{-- COMPANY --}}
<div class="grid grid-cols-2 gap-4">
<div>
<label class="block text-sm font-medium mb-1">Company Name</label>
<input type="text" name="company_name"
class="w-full px-3 py-2 border rounded">
</div>

<div>
<label class="block text-sm font-medium mb-1">GST Number</label>
<input type="text" name="gst_number"
class="w-full px-3 py-2 border rounded">
</div>
</div>

</div>

{{-- ================= BOOKING DETAILS ================= --}}
<div class="bg-white rounded-lg shadow p-6">
<h3 class="text-lg font-semibold mb-4">Booking Details</h3>

<div class="grid grid-cols-3 gap-4 mb-4">
<div>
<label class="block text-sm mb-1">Check-in *</label>
<input type="datetime-local" name="check_in"
class="w-full px-3 py-2 border rounded">
</div>

<div>
<label class="block text-sm mb-1">Check-out *</label>
<input type="datetime-local" name="check_out"
class="w-full px-3 py-2 border rounded">
</div>

<div>
<label class="block text-sm mb-1">Adults *</label>
<input type="number" name="number_of_adults"
value="1" min="1"
class="w-full px-3 py-2 border rounded">
</div>

<div>
<label class="block text-sm mb-1">Children *</label>
<input type="number" name="number_of_children"
value="0" min="0"
class="w-full px-3 py-2 border rounded">
</div>

</div>

<label class="block text-sm font-medium mb-2">Select Rooms *</label>
<div class="grid grid-cols-2 gap-4 max-h-64 overflow-y-auto border rounded p-4">
@foreach ($rooms as $room)
<label class="flex items-center p-3 border rounded hover:bg-gray-50">
<input type="checkbox" name="room_ids[]" value="{{ $room->id }}" class="mr-3">
<div>
<div class="font-semibold">Room {{ $room->room_number }}</div>
<div class="text-sm text-gray-600">{{ $room->roomType->name }}</div>
<div class="text-sm text-blue-600">₹{{ number_format($room->base_price,2) }}</div>
</div>
</label>
@endforeach
</div>

</div>
</div>

{{-- ================= PAYMENT ================= --}}
<div>
<div class="bg-white rounded-lg shadow p-6 sticky top-4">
<h3 class="text-lg font-semibold mb-4">Payment</h3>

<div class="mb-4">
<label class="block text-sm mb-1">Advance Payment</label>
<input type="number" name="advance_payment"
value="0"
class="w-full px-3 py-2 border rounded">
</div>

<div class="mb-6">
<label class="block text-sm mb-1">Payment Mode</label>
<select name="payment_mode" class="w-full px-3 py-2 border rounded">
<option value="cash">Cash</option>
<option value="card">Card</option>
<option value="upi">UPI</option>
<option value="bank_transfer">Bank Transfer</option>
</select>
</div>

<button type="submit"
class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded font-semibold">
Create Booking
</button>

<a href="{{ route('bookings.index') }}"
class="block text-center mt-3 text-gray-600">Cancel</a>
</div>
</div>

</div>
</form>

{{-- ================= CUSTOMER SEARCH SCRIPT ================= --}}
<script>
const mobileInput = document.getElementById('customer_mobile');
const resultBox = document.getElementById('customerResults');

mobileInput.addEventListener('keyup', function () {
let q = this.value;
if (q.length < 2) {
resultBox.classList.add('hidden');
return;
}

fetch(`/customers/search?q=${q}`)
.then(res => res.json())
.then(data => {
resultBox.innerHTML = '';
if (data.length === 0) {
resultBox.classList.add('hidden');
return;
}

data.forEach(c => {
let div = document.createElement('div');
div.className = 'p-2 hover:bg-gray-100 cursor-pointer border-b';
div.innerHTML = `<strong>${c.customer_name}</strong><br><small>${c.customer_mobile}</small>`;
div.onclick = () => fillCustomer(c);
resultBox.appendChild(div);
});
resultBox.classList.remove('hidden');
});
});

function fillCustomer(c) {
document.getElementById('customer_id').value = c.id;
document.querySelector('[name="customer_name"]').value = c.customer_name;
document.querySelector('[name="customer_mobile"]').value = c.customer_mobile;
document.querySelector('[name="customer_email"]').value = c.customer_email ?? '';
document.querySelector('[name="customer_address"]').value = c.customer_address ?? '';
document.querySelector('[name="id_proof_type"]').value = c.id_proof_type ?? '';
document.querySelector('[name="id_proof_number"]').value = c.id_proof_number ?? '';
document.querySelector('[name="company_name"]').value = c.company_name ?? '';
document.querySelector('[name="gst_number"]').value = c.gst_number ?? '';
resultBox.classList.add('hidden');
}
</script>

@endsection
