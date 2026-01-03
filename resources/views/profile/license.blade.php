<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>License Management - Qlo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-12 px-4">
        <div class="max-w-4xl mx-auto">

            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-3xl font-bold text-gray-800">License Management</h1>
                <p class="text-gray-600 mt-2">Manage your Qlo software license</p>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <strong>Error!</strong> {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                    <strong>Warning!</strong> {{ session('warning') }}
                </div>
            @endif

            <div class="grid md:grid-cols-2 gap-6">

                <!-- Left Column: Machine ID & Current License -->
                <div>
                    <!-- Machine ID Card -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">🖥️ Your Machine ID</h2>
                        <div class="bg-gray-50 p-4 rounded border border-gray-200">
                            <p class="text-sm text-gray-600 mb-2">Machine ID:</p>
                            <div class="flex items-center justify-between">
                                <code id="machine-id" class="text-lg font-mono text-blue-600 font-bold">
                                    {{ $machine_id }}
                                </code>
                                <button onclick="copyMachineId()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-sm">
                                    📋 Copy
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-3">
                            📌 Send this Machine ID to admin for license generation
                        </p>
                    </div>

                    <!-- Current License Status -->
                    @if($license_info)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">📄 Current License</h2>

                            @if($license_info['is_expired'])
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                                    <p class="text-red-700 font-semibold">⚠️ License Expired</p>
                                    <p class="text-red-600 text-sm">Expired on: {{ $license_info['expiry'] }}</p>
                                </div>
                            @elseif($license_info['is_expiring_soon'])
                                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4">
                                    <p class="text-yellow-700 font-semibold">⚠️ License Expiring Soon</p>
                                    <p class="text-yellow-600 text-sm">{{ $license_info['days_remaining'] }} days remaining</p>
                                </div>
                            @else
                                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                                    <p class="text-green-700 font-semibold">✅ License Active</p>
                                    <p class="text-green-600 text-sm">{{ $license_info['days_remaining'] }} days remaining</p>
                                </div>
                            @endif

                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-semibold">{{ $license_info['email'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Expiry Date:</span>
                                    <span class="font-semibold">{{ $license_info['expiry'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Machine ID:</span>
                                    <span class="font-mono text-sm">{{ $license_info['machine_id'] }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="text-center py-8">
                                <div class="text-6xl mb-4">🔒</div>
                                <h3 class="text-xl font-semibold text-gray-700 mb-2">No License Found</h3>
                                <p class="text-gray-500">Please activate your license to use the system</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column: Activate/Renew License Form -->
                <div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">
                            @if($license_info)
                                🔄 Renew License
                            @else
                                🔑 Activate License
                            @endif
                        </h2>

                        <form action="{{ $license_info ? route('profile.license.renew') : route('profile.license.activate') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="license_key" class="block text-sm font-medium text-gray-700 mb-2">
                                    License Key *
                                </label>
                                <textarea
                                    name="license_key"
                                    id="license_key"
                                    rows="6"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                                    placeholder="Paste your license key here...&#10;&#10;Example:&#10;eyJlbWFpbCI6ImNsaWVudEBnbWFpbC5jb20iLCJleHBpcnkiOiIyMDI1LTEyLTMxIiwibWFjaGluZV9pZCI6ImExYjJjM2Q0ZTVmNiJ9"
                                ></textarea>
                                @error('license_key')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 font-semibold transition"
                            >
                                @if($license_info)
                                    🔄 Renew License
                                @else
                                    🔑 Activate License
                                @endif
                            </button>
                        </form>

                        <!-- Instructions -->
                        <div class="mt-6 bg-blue-50 p-4 rounded border border-blue-200">
                            <h3 class="font-semibold text-blue-800 mb-2">📝 How to get license key:</h3>
                            <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside">
                                <li>Copy your Machine ID from above</li>
                                <li>Send Machine ID to admin</li>
                                <li>Admin will generate and send license key</li>
                                <li>Paste license key here and activate</li>
                            </ol>
                        </div>

                        <!-- Back to Dashboard -->
                        @if($license_info && !$license_info['is_expired'])
                            <a href="{{ route('dashboard') }}" class="block text-center mt-4 text-blue-600 hover:text-blue-800">
                                ← Back to Dashboard
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function copyMachineId() {
            const machineId = document.getElementById('machine-id').textContent.trim();
            navigator.clipboard.writeText(machineId).then(() => {
                alert('✅ Machine ID copied to clipboard!');
            });
        }
    </script>
</body>
</html>
