@php
$menu = [
    ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'fas fa-home'],
    ['label' => 'Room Types', 'route' => 'room-types.index', 'icon' => 'fas fa-layer-group'],
    ['label' => 'Rooms', 'route' => 'rooms.index', 'icon' => 'fas fa-door-closed'],
    ['label' => 'Bookings', 'route' => 'bookings.index', 'icon' => 'fas fa-calendar-check'],
    ['label' => 'Create Booking', 'route' => 'bookings.create', 'icon' => 'fas fa-calendar-plus'],
    ['label' => 'Reports', 'route' => 'reports.bookings', 'icon' => 'fas fa-chart-bar'],
    ['label' => 'Revenue Report', 'route' => 'reports.revenue', 'icon' => 'fas fa-wallet'],
    ['label' => 'Tax Report', 'route' => 'reports.tax', 'icon' => 'fas fa-receipt'],
    ['label' => 'Occupancy Report', 'route' => 'reports.occupancy', 'icon' => 'fas fa-users'],
    ['label' => 'Profile', 'route' => 'profile.edit', 'icon' => 'fas fa-user'],
];
@endphp
<aside class="w-64 bg-white shadow-lg min-h-screen">
    {{-- Header --}}
    <div class="px-6 py-5 border-b">
        <h2 class="text-xl font-bold text-blue-600 flex items-center gap-2">
            <i class="fas fa-user-shield"></i>
            Lodging System
        </h2>
    </div>

    {{-- Menu --}}
    <nav class="p-4 space-y-1">
        @foreach($menu as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-4 py-2 rounded transition
               {{ request()->routeIs($item['route'].'*')
                    ? 'bg-blue-600 text-white'
                    : 'text-gray-700 hover:bg-gray-100' }}">
                
                <i class="{{ $item['icon'] }} w-5 text-center"></i>
                <span class="font-medium">{{ $item['label'] }}</span></p>
            </a>
        @endforeach
    </nav>
</aside>
