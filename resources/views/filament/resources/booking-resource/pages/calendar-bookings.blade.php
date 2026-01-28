<x-filament-panels::page>
    <x-filament-panels::header :actions="$this->getHeaderActions()">
        <x-slot name="heading">
            Календарь бронирований
        </x-slot>
        <x-slot name="description">
            Просмотр и управление записями в календарном виде
        </x-slot>
    </x-filament-panels::header>

    <div class="space-y-6">
        {{-- Управление видом --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-white rounded-lg shadow">
            <div class="flex items-center space-x-4">
                <button 
                    wire:click="prevPeriod"
                    class="p-2 text-gray-600 hover:bg-gray-100 rounded"
                    title="Предыдущий период"
                >
                    <x-heroicon-o-chevron-left class="w-5 h-5" />
                </button>

                <button 
                    wire:click="goToToday"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200"
                >
                    Сегодня
                </button>

                <button 
                    wire:click="nextPeriod"
                    class="p-2 text-gray-600 hover:bg-gray-100 rounded"
                    title="Следующий период"
                >
                    <x-heroicon-o-chevron-right class="w-5 h-5" />
                </button>

                <h2 class="text-lg font-semibold ml-4">
                    @if($viewType === 'week')
                        Неделя {{ Carbon\Carbon::parse($currentDate)->startOfWeek()->format('d.m') }} - {{ Carbon\Carbon::parse($currentDate)->endOfWeek()->format('d.m.Y') }}
                    @elseif($viewType === 'month')
                        {{ Carbon\Carbon::parse($currentDate)->translatedFormat('F Y') }}
                    @else
                        {{ Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                    @endif
                </h2>
            </div>

            <div class="flex space-x-2">
                <button 
                    wire:click="setViewType('day')"
                    class="px-3 py-1 text-sm rounded {{ $viewType === 'day' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    День
                </button>
                <button 
                    wire:click="setViewType('week')"
                    class="px-3 py-1 text-sm rounded {{ $viewType === 'week' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    Неделя
                </button>
                <button 
                    wire:click="setViewType('month')"
                    class="px-3 py-1 text-sm rounded {{ $viewType === 'month' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    Месяц
                </button>
            </div>
        </div>

        {{-- Статистика --}}
        @php
            $stats = $this->getStatistics();
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Всего записей</p>
                        <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <x-heroicon-o-calendar class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Подтверждено</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['confirmed'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <x-heroicon-o-check class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Ожидание</p>
                        <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="p-3 bg-amber-100 rounded-full">
                        <x-heroicon-o-clock class="w-6 h-6 text-amber-600" />
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Отменено</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <x-heroicon-o-x-mark class="w-6 h-6 text-red-600" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Календарь --}}
        @if($viewType === 'week')
            {{-- Недельный вид --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-24 p-3 text-left text-sm font-medium text-gray-500 border-r">
                                    Время
                                </th>
                                @foreach($this->getWeekDates() as $day)
                                    <th class="p-3 text-center border-r">
                                        <div class="space-y-1">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $day['weekday'] }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $day['day'] }}.{{ $day['month'] }}
                                            </div>
                                            @if($day['isToday'])
                                                <div class="inline-block px-2 py-1 text-xs font-medium text-blue-600 bg-blue-100 rounded-full">
                                                    Сегодня
                                                </div>
                                            @endif
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($timeSlots as $time)
                                <tr>
                                    <td class="p-3 text-sm font-medium text-gray-900 bg-gray-50 border-r">
                                        {{ $time }}
                                    </td>
                                    @foreach($this->getWeekDates() as $day)
                                        @php
                                            $bookings = $this->getBookingsForDate($day['date'])[$time] ?? [];
                                        @endphp
                                        <td class="p-2 border-r min-h-[60px] {{ $day['isSelected'] ? 'bg-blue-50' : '' }}">
                                            @foreach($bookings as $booking)
                                                <a 
                                                    href="{{ BookingResource::getUrl('edit', ['record' => $booking->id]) }}"
                                                    class="block mb-1 p-2 rounded text-sm cursor-pointer transition-colors hover:opacity-90 {{ match($booking->status) {
                                                        'confirmed' => 'bg-green-100 text-green-800 border-l-4 border-green-500',
                                                        'pending' => 'bg-amber-100 text-amber-800 border-l-4 border-amber-500',
                                                        'cancelled' => 'bg-red-100 text-red-800 border-l-4 border-red-500',
                                                        default => 'bg-gray-100 text-gray-800 border-l-4 border-gray-500',
                                                    } }}"
                                                    title="{{ $booking->client_name }} - {{ $booking->service->name }}"
                                                >
                                                    <div class="font-medium truncate">{{ $booking->client_name }}</div>
                                                    <div class="text-xs truncate">{{ $booking->service->name }}</div>
                                                    <div class="text-xs">{{ $booking->client_phone }}</div>
                                                </a>
                                            @endforeach
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif($viewType === 'day')
            {{-- Дневной вид --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b">
                    <h3 class="text-lg font-semibold">
                        {{ Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
                    </h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($this->getBookingsForDay($selectedDate) as $booking)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ match($booking->status) {
                                            'confirmed' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-amber-100 text-amber-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        } }}">
                                            {{ match($booking->status) {
                                                'confirmed' => 'Подтверждено',
                                                'pending' => 'Ожидание',
                                                'cancelled' => 'Отменено',
                                                default => $booking->status,
                                            } }}
                                        </span>
                                        <span class="font-medium text-gray-900">
                                            {{ $booking->appointment_time->format('H:i') }}
                                        </span>
                                    </div>
                                    <h4 class="mt-2 text-lg font-semibold">{{ $booking->client_name }}</h4>
                                    <div class="mt-1 space-y-1 text-sm text-gray-600">
                                        <div class="flex items-center space-x-2">
                                            <x-heroicon-o-phone class="w-4 h-4" />
                                            <span>{{ $booking->client_phone }}</span>
                                        </div>
                                        @if($booking->client_email)
                                            <div class="flex items-center space-x-2">
                                                <x-heroicon-o-envelope class="w-4 h-4" />
                                                <span>{{ $booking->client_email }}</span>
                                            </div>
                                        @endif
                                        <div class="flex items-center space-x-2">
                                            <x-heroicon-o-sparkles class="w-4 h-4" />
                                            <span>{{ $booking->service->name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a 
                                        href="{{ BookingResource::getUrl('edit', ['record' => $booking->id]) }}"
                                        class="px-3 py-1 text-sm text-white bg-primary-600 rounded hover:bg-primary-700"
                                    >
                                        Редактировать
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        @else
            {{-- Месячный вид --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="grid grid-cols-7 gap-px bg-gray-200">
                    @foreach(['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'] as $day)
                        <div class="p-3 text-center text-sm font-medium text-gray-700 bg-gray-50">
                            {{ $day }}
                        </div>
                    @endforeach
                    
                    @php
                        $start = Carbon\Carbon::parse($currentDate)->startOfMonth()->startOfWeek();
                        $end = Carbon\Carbon::parse($currentDate)->endOfMonth()->endOfWeek();
                    @endphp
                    
                    @for($date = $start->copy(); $date->lte($end); $date->addDay())
                        @php
                            $isCurrentMonth = $date->month == Carbon\Carbon::parse($currentDate)->month;
                            $isToday = $date->isToday();
                            $bookingsCount = Booking::whereDate('appointment_time', $date->toDateString())->count();
                        @endphp
                        <div 
                            class="min-h-[100px] p-2 bg-white {{ !$isCurrentMonth ? 'bg-gray-50 text-gray-400' : '' }} {{ $isToday ? 'ring-2 ring-primary-500' : '' }}"
                            wire:click="changeDate('{{ $date->toDateString() }}')"
                        >
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-sm font-medium {{ $isToday ? 'text-primary-600' : '' }}">
                                    {{ $date->format('j') }}
                                </span>
                                @if($bookingsCount > 0)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ $bookingsCount }}
                                    </span>
                                @endif
                            </div>
                            
                            @if($isCurrentMonth)
                                @foreach(Booking::whereDate('appointment_time', $date->toDateString())->limit(2)->get() as $booking)
                                    <div 
                                        class="mb-1 p-1 text-xs rounded truncate cursor-pointer hover:opacity-90 {{ match($booking->status) {
                                            'confirmed' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-amber-100 text-amber-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        } }}"
                                        onclick="window.location.href='{{ BookingResource::getUrl('edit', ['record' => $booking->id]) }}'; event.stopPropagation();"
                                    >
                                        <div class="font-medium truncate">{{ $booking->client_name }}</div>
                                        <div class="truncate">{{ $booking->appointment_time->format('H:i') }}</div>
                                    </div>
                                @endforeach
                                
                                @if($bookingsCount > 2)
                                    <div class="text-xs text-gray-500 text-center">
                                        +{{ $bookingsCount - 2 }} ещё
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        @endif
    </div>

    <style>
        [x-cloak] { display: none !important; }
        .min-h-\[60px\] { min-height: 60px; }
        .min-h-\[100px\] { min-height: 100px; }
    </style>
</x-filament-panels::page>