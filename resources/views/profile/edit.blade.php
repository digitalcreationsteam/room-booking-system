@extends('layouts.app')

@section('title', 'Profile')
@section('header', 'Profile Settings')

@section('content')

<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PATCH')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ================= USER INFO ================= --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">User Information</h3>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Name *</label>
                <input type="text" name="name"
                       value="{{ old('name', $user->name) }}"
                       class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror">

                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Email *</label>
                <input type="email" name="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror">

                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ================= HOTEL INFO ================= --}}
        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">Hotel Information</h3>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Hotel Name *</label>
                <input type="text" name="hotel_name"
                       value="{{ old('hotel_name', $hotel->hotel_name ?? '') }}"
                       class="w-full border rounded px-3 py-2 @error('hotel_name') border-red-500 @enderror">

                @error('hotel_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Hotel Address</label>
                <textarea name="hotel_address"
                          class="w-full border rounded px-3 py-2 @error('hotel_address') border-red-500 @enderror"
                          rows="3">{{ old('hotel_address', $hotel->hotel_address ?? '') }}</textarea>

                @error('hotel_address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">Mobile</label>
                    <input type="text" name="hotel_mobile"
                           value="{{ old('hotel_mobile', $hotel->hotel_mobile ?? '') }}"
                           class="w-full border rounded px-3 py-2 @error('hotel_mobile') border-red-500 @enderror">

                    @error('hotel_mobile')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium">Telephone</label>
                    <input type="text" name="hotel_telephone"
                           value="{{ old('hotel_telephone', $hotel->hotel_telephone ?? '') }}"
                           class="w-full border rounded px-3 py-2 @error('hotel_telephone') border-red-500 @enderror">

                    @error('hotel_telephone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1 font-medium">GST Number</label>
                    <input type="text" name="hotel_gst_number"
                           value="{{ old('hotel_gst_number', $hotel->hotel_gst_number ?? '') }}"
                           class="w-full border rounded px-3 py-2 @error('hotel_gst_number') border-red-500 @enderror">

                    @error('hotel_gst_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block mb-1 font-medium">Hotel Email</label>
                    <input type="email" name="hotel_email"
                           value="{{ old('hotel_email', $hotel->hotel_email ?? '') }}"
                           class="w-full border rounded px-3 py-2 @error('hotel_email') border-red-500 @enderror">

                    @error('hotel_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- ✅ NEW FIELD: L&T NUMBER --}}
            <div class="mb-4">
                <label class="block mb-1 font-medium">L&T Number</label>
                <input type="text" name="hotel_l_t_number"
                       value="{{ old('hotel_l_t_number', $hotel->hotel_l_t_number ?? '') }}"
                       class="w-full border rounded px-3 py-2 @error('hotel_l_t_number') border-red-500 @enderror">

                @error('hotel_l_t_number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>

    <div class="mt-6">
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded font-semibold">
            Save Profile
        </button>
    </div>

</form>
@endsection
