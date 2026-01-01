@php
    $menu = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => '🏠'],

        ['label' => 'Room Types', 'route' => 'room-types.index', 'icon' => '🛏️'],
        ['label' => 'Rooms', 'route' => 'rooms.index', 'icon' => '🚪'],

        ['label' => 'Bookings', 'route' => 'bookings.index', 'icon' => '📖'],
        ['label' => 'Create Booking', 'route' => 'bookings.create', 'icon' => '➕'],

        ['label' => 'Reports', 'route' => 'reports.bookings', 'icon' => '📊'],
        ['label' => 'Revenue Report', 'route' => 'reports.revenue', 'icon' => '💰'],
        ['label' => 'Tax Report', 'route' => 'reports.tax', 'icon' => '🧾'],
        ['label' => 'Occupancy Report', 'route' => 'reports.occupancy', 'icon' => '🏨'],

        ['label' => 'Profile', 'route' => 'profile.edit', 'icon' => '👤'],
    ];
@endphp

<aside class="w-64 bg-white shadow-lg">
    <div class="px-6 py-5 border-b">
        <h2 class="text-xl font-bold text-blue-600">Hotel Admin</h2>
    </div>

    <nav class="p-4 space-y-1">
        @foreach($menu as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-4 py-2 rounded
               {{ request()->routeIs($item['route'].'*')
                    ? 'bg-blue-600 text-white'
                    : 'text-gray-700 hover:bg-gray-100' }}">
                <span>{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>
