<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Generate License - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-12 px-4">
        <div class="max-w-4xl mx-auto">

            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg shadow-lg p-6 mb-6 text-white">
                <h1 class="text-3xl font-bold">🔐 Admin License Generator</h1>
                <p class="mt-2">Generate license keys for Qlo clients</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            @endif

            <div class="grid md:grid-cols-2 gap-6">

                <!-- Left Column: Generator Form -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Generate New License</h2>

                    <form action="{{ route('admin.license.store') }}" method="POST" id="licenseForm">
                        @csrf

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Client Email *
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                required
                                value="{{ old('email') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="client@hotel.com"
                            >
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Machine ID -->
                        <div class="mb-4">
                            <label for="machine_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Client Machine ID *
                            </label>
                            <input
                                type="text"
                                name="machine_id"
                                id="machine_id"
                                required
                                maxlength="12"
                                value="{{ old('machine_id') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
                                placeholder="a1b2c3d4e5f6"
                            >
                            @error('machine_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-1">12 characters (client will provide)</p>
                        </div>

                        <!-- Expiry Date -->
                        <div class="mb-4">
                            <label for="expiry" class="block text-sm font-medium text-gray-700 mb-2">
                                License Expiry Date *
                            </label>
                            <input
                                type="date"
                                name="expiry"
                                id="expiry"
                                required
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                value="{{ old('expiry', date('Y-m-d', strtotime('+1 year'))) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            @error('expiry')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quick Date Buttons -->
                        <div class="mb-6 flex gap-2 flex-wrap">
                            <button type="button" onclick="setExpiry(30)" class="bg-gray-200 px-3 py-1 rounded text-sm hover:bg-gray-300">
                                +30 Days
                            </button>
                            <button type="button" onclick="setExpiry(90)" class="bg-gray-200 px-3 py-1 rounded text-sm hover:bg-gray-300">
                                +3 Months
                            </button>
                            <button type="button" onclick="setExpiry(180)" class="bg-gray-200 px-3 py-1 rounded text-sm hover:bg-gray-300">
                                +6 Months
                            </button>
                            <button type="button" onclick="setExpiry(365)" class="bg-blue-200 px-3 py-1 rounded text-sm hover:bg-blue-300">
                                +1 Year
                            </button>
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-3 px-4 rounded-md hover:from-purple-700 hover:to-blue-700 font-semibold transition"
                        >
                            🔑 Generate License Key
                        </button>
                    </form>
                </div>

                <!-- Right Column: Generated License -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Generated License Key</h2>

                    @if(session('license_key'))
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-green-700 font-semibold mb-2">✅ License Generated Successfully!</p>

                            <div class="bg-white p-3 rounded border border-gray-300 mb-3">
                                <p class="text-xs text-gray-600 mb-1">License Key:</p>
                                <code id="generated-key" class="text-xs font-mono break-all text-gray-800">
                                    {{ session('license_key') }}
                                </code>
                            </div>

                            <button
                                onclick="copyLicenseKey()"
                                class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 text-sm"
                            >
                                📋 Copy License Key
                            </button>
                        </div>

                        <div class="bg-blue-50 p-4 rounded border border-blue-200">
                            <h3 class="font-semibold text-blue-800 mb-2">License Details:</h3>
                            <div class="text-sm text-blue-700 space-y-1">
                                <p><strong>Email:</strong> {{ session('license_data')['email'] }}</p>
                                <p><strong>Machine ID:</strong> {{ session('license_data')['machine_id'] }}</p>
                                <p><strong>Expiry:</strong> {{ session('license_data')['expiry'] }}</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-400">
                            <div class="text-6xl mb-4">🔑</div>
                            <p>No license generated yet</p>
                            <p class="text-sm mt-2">Fill the form and click generate</p>
                        </div>
                    @endif

                    <!-- Instructions -->
                    <div class="mt-6 bg-yellow-50 p-4 rounded border border-yellow-200">
                        <h3 class="font-semibold text-yellow-800 mb-2">📝 Instructions:</h3>
                        <ol class="text-sm text-yellow-700 space-y-1 list-decimal list-inside">
                            <li>Get Machine ID from client</li>
                            <li>Fill the form and generate license</li>
                            <li>Copy the generated license key</li>
                            <li>Send license key to client via email/WhatsApp</li>
                        </ol>
                    </div>
                </div>

            </div>

            <!-- Recent Licenses (Optional - if you want to show history) -->
            <div class="mt-6 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">📊 All Active Licenses</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hotel</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $hotels = \App\Models\Hotel::with('user')->whereNotNull('license_key')->get();
                            @endphp
                            @forelse($hotels as $hotel)
                                @php
                                    $licenseData = json_decode(base64_decode($hotel->license_key), true);
                                    $expiry = strtotime($licenseData['expiry'] ?? '');
                                    $daysLeft = ceil(($expiry - time()) / 86400);
                                    $isExpired = $daysLeft < 0;
                                    $isExpiringSoon = $daysLeft <= 7 && $daysLeft >= 0;
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-sm">{{ $hotel->hotel_name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $hotel->user->email }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $licenseData['expiry'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($isExpired)
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Expired</span>
                                        @elseif($isExpiringSoon)
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">{{ $daysLeft }} days left</span>
                                        @else
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Active</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No licenses found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setExpiry(days) {
            const today = new Date();
            today.setDate(today.getDate() + days);
            document.getElementById('expiry').value = today.toISOString().split('T')[0];
        }

        function copyLicenseKey() {
            const key = document.getElementById('generated-key').textContent.trim();
            navigator.clipboard.writeText(key).then(() => {
                alert('✅ License key copied to clipboard!');
            });
        }
    </script>
</body>
</html>
