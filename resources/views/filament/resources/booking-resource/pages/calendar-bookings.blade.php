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
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Легенда --}}
            <div class="lg:col-span-1 space-y-4">
                <div class="p-4 bg-dark rounded-lg shadow">
                    <h3 class="font-semibold text-lg mb-4">Легенда</h3>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 rounded" style="background-color: #10b981;"></div>
                            <span class="text-sm">Подтверждено</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 rounded" style="background-color: #f59e0b;"></div>
                            <span class="text-sm">Ожидание</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 rounded" style="background-color: #ef4444;"></div>
                            <span class="text-sm">Отменено</span>
                        </div>
                    </div>
                </div>

                {{-- Статистика --}}
                <div class="p-4 bg-dark rounded-lg shadow">
                    <h3 class="font-semibold text-lg mb-4">Статистика на сегодня</h3>
                    <div class="space-y-3">
                        @php
                            $today = now()->startOfDay();
                            $bookingsToday = \App\Domains\Bookings\Models\Booking::whereDate('appointment_time', $today)->count();
                            $confirmedToday = \App\Domains\Bookings\Models\Booking::whereDate('appointment_time', $today)
                                ->where('status', 'confirmed')->count();
                            $pendingToday = \App\Domains\Bookings\Models\Booking::whereDate('appointment_time', $today)
                                ->where('status', 'pending')->count();
                        @endphp
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Всего записей:</span>
                            <span class="font-semibold">{{ $bookingsToday }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Подтверждено:</span>
                            <span class="font-semibold text-green-600">{{ $confirmedToday }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Ожидание:</span>
                            <span class="font-semibold text-amber-600">{{ $pendingToday }}</span>
                        </div>
                    </div>
                </div>

                {{-- Быстрые действия --}}
                <div class="p-4 bg-dark rounded-lg shadow">
                    <h3 class="font-semibold text-lg mb-4">Быстрые действия</h3>
                    <div class="space-y-2">
                        <a 
                            href="{{ \App\Filament\Resources\BookingResource::getUrl('create') }}"
                            class="block w-full px-4 py-2 text-sm text-center text-white bg-primary-600 rounded hover:bg-primary-700"
                        >
                            Новая запись
                        </a>
                        <button 
                            wire:click="$dispatch('refreshCalendar')"
                            class="block w-full px-4 py-2 text-sm text-center text-gray-700 bg-gray-100 rounded hover:bg-gray-200"
                        >
                            Обновить календарь
                        </button>
                    </div>
                </div>
            </div>

            {{-- Календарь --}}
            <div class="lg:col-span-3">
                @livewire(\App\Filament\Widgets\BookingCalendar::class)
            </div>
        </div>
    </div>
</x-filament-panels::page>