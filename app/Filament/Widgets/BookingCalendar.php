<?php

namespace App\Filament\Widgets;

use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Domains\Bookings\Models\Booking;
use App\Filament\Resources\BookingResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class BookingCalendar extends FullCalendarWidget
{
    public Model|string|null $model = Booking::class;

    public static function canView(): bool
    {
        return false; // Виджет не будет отображаться
    }

    public function getFormSchema(): array
    {
        return BookingResource::form(Schema::make())->getComponents();
    }

    public function fetchEvents(array $info): array
    {
        $start = Carbon::parse($info['start']);
        $end = Carbon::parse($info['end']);

        return Booking::query()
            ->where('appointment_time', '>=', $start)
            ->where('appointment_time', '<=', $end)
            ->with('service')
            ->get()
            ->map(function (Booking $booking) {
                $duration = $booking->service->duration ?? 60; // minutes — добавь поле в Service, если нет

                $endTime = $booking->appointment_time->clone()->addMinutes($duration);

                return [
                    'id' => $booking->id,
                    'title' => "{$booking->client_name} — {$booking->service->name}",
                    'start' => $booking->appointment_time->toIso8601String(),
                    'end' => $endTime->toIso8601String(),
                    'allDay' => false,
                    'backgroundColor' => match ($booking->status) {
                        'confirmed' => '#22c55e', // green-500
                        'pending' => '#eab308', // yellow-500
                        'cancelled' => '#ef4444', // red-500
                        default => '#6b7280', // gray-500
                    },
                    'borderColor' => match ($booking->status) {
                        'confirmed' => '#16a34a',
                        'pending' => '#ca8a04',
                        'cancelled' => '#dc2626',
                        default => '#4b5563',
                    },
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'client_name' => $booking->client_name,
                        'client_phone' => $booking->client_phone,
                        'client_email' => $booking->client_email ?? null,
                        'service_name' => $booking->service->name,
                        'status' => $booking->status,
                        'source' => $booking->source,
                    ],
                ];
            })
            ->toArray();
    }

    /**
     * Кастомизация через eventDidMount — самый гибкий и рекомендуемый способ в 4.x
     * Возвращает строку JS-кода
     */
    public function eventDidMount(): string
    {
        return <<<JS
            function({ event, el }) {
                // Добавляем плавный hover и cursor-pointer
                el.classList.add('transition', 'duration-150', 'hover:shadow-md', 'hover:scale-[1.02]', 'cursor-pointer');

                // Tooltip с дополнительной информацией
                el.setAttribute('title',
                    event.title + '\\n' +
                    'Телефон: ' + (event.extendedProps.client_phone || '—') + '\\n' +
                    'Статус: ' + (event.extendedProps.status_label || '—')
                );
            }
        JS;
    }

    /**
     * Если всё же хочешь использовать eventContent (полная замена содержимого)
     * — возвращает JS-функцию
     */
    public function eventContent(): string
    {
        return <<<JS
            function(info) {
                const event = info.event;
                const timeText = info.timeText || '';

                let statusBadge = '';
                if (event.extendedProps.status_label) {
                    let bgColor = '';
                    switch (event.extendedProps.status) {
                        case 'confirmed': bgColor = 'bg-green-100 text-green-800'; break;
                        case 'pending':   bgColor = 'bg-amber-100 text-amber-800'; break;
                        case 'cancelled': bgColor = 'bg-red-100 text-red-800'; break;
                        default:          bgColor = 'bg-gray-100 text-gray-800';
                    }
                    statusBadge = '<span class="inline-block px-1.5 py-0.5 text-[9px] font-medium rounded-full ' + bgColor + '>' + event.extendedProps.status_label + '</span>';
                }

                return {
                    html: '<div class="p-1 text-xs leading-tight overflow-hidden h-full flex flex-col justify-between">'
                            +'<div class="flex items-start justify-between gap-1">'
                                +'<div class="font-semibold truncate flex-1">'+event.title+'</div>'
                                +statusBadge
                            +'</div>'
                            +'<div class="opacity-90 mt-0.5 flex items-center gap-1">'
                                +'<svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">'
                                    +'<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>'
                                +'</svg>'
                                +'<span class="truncate">'+(event.extendedProps.client_phone || '—')+'</span>'
                            +'</div>'
                            +'<div class="text-[10px] opacity-75 mt-0.5 text-right">'
                                +timeText+
                            +'</div>'
                        +'</div>'
                };
            }
        JS;
    }

    // Остальные методы без изменений (getConfig, getCreateAction и т.д.)
    public function getConfig(): array
    {
        return [
            'initialView' => 'timeGridWeek',
            'allDaySlot' => false,
            'slotMinTime' => '09:00:00',
            'slotMaxTime' => '21:00:00',
            'slotDuration' => '01:00:00',
            'slotLabelInterval' => '01:00:00',
            'selectable' => true,
            'selectMirror' => true,
            'editable' => true,
            'eventResizableFromStart' => true,
            'eventDurationEditable' => true,
            'navLinks' => true,
            'weekends' => true,
            'locale' => 'ru',
            'firstDay' => 1,
            'timeZone' => 'Europe/Moscow',
            'height' => 'auto',
            'contentHeight' => 'auto',
            'aspectRatio' => 1.8,
            'businessHours' => [
                'daysOfWeek' => [1, 2, 3, 4, 5, 6],
                'startTime' => '09:00',
                'endTime' => '21:00',
            ],
            'buttonText' => [
                'today' => 'Сегодня',
                'month' => 'Месяц',
                'week' => 'Неделя',
                'day' => 'День',
                'list' => 'Список',
            ],
            'slotLabelFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'hour12' => false,
                'meridiem' => false,
            ],
        ];
    }

    public function getCreateAction(): Action
    {
        return Action::make('create')
            ->label('Создать запись')
            ->modalHeading('Новая запись на перманентный макияж')
            ->modalDescription('Заполните детали для новой записи клиента.')
            ->modalSubmitActionLabel('Создать')
            ->modalCancelAction(
                Action::make('close')
                    ->label('Закрыть')
                    ->color('gray')
                    ->close()
            )
            ->mountUsing(function (array $arguments) {
                return [
                    'appointment_time' => Carbon::parse($arguments['start'] ?? now())->toDateTimeString(),
                ];
            })
            ->successNotification(
                Notification::make()
                    ->success()
                    ->title('Запись создана')
                    ->body('Новая запись успешно добавлена в календарь.')
            );
    }

    public function getEditAction(): Action
    {
        return Action::make('edit')
            ->label('Редактировать запись')
            ->modalHeading('Редактирование записи')
            ->modalDescription('Измените детали записи клиента.')
            ->modalSubmitActionLabel('Сохранить изменения')
            ->modalCancelAction(
                Action::make('close')
                    ->label('Закрыть')
                    ->color('gray')
                    ->close()
            )
            ->successNotification(
                Notification::make()
                    ->success()
                    ->title('Запись обновлена')
                    ->body('Изменения в записи успешно сохранены.')
            );
    }

    public function getViewAction(): Action
    {
        return Action::make('view')
            ->label('Просмотреть запись')
            ->modalHeading('Детали записи')
            ->modalDescription('Просмотр информации о записи клиента.')
            ->modalCancelAction(
                Action::make('close')
                    ->label('Закрыть')
                    ->color('gray')
                    ->close()
            );
    }

    public function onEventDrop(array $event, array $oldEvent, array $relatedEvents, array $delta, ?array $oldResource, ?array $newResource): bool
    {
        $booking = Booking::find($event['id']);
        if ($booking) {
            $booking->appointment_time = Carbon::parse($event['start']);
            $booking->save();
            Notification::make()
                ->success()
                ->title('Запись перемещена')
                ->body('Запись успешно перемещена на новое время.')
                ->send();
        }

        return false;
    }

    public function onEventResize(array $event, array $oldEvent, array $relatedEvents, array $startDelta, array $endDelta): bool
    {
        $booking = Booking::find($event['id']);
        if ($booking) {
            $booking->appointment_time = Carbon::parse($event['start']);
            // Update duration if needed, assuming service duration changes
            $booking->save();
            Notification::make()
                ->success()
                ->title('Продолжительность изменена')
                ->body('Продолжительность записи успешно обновлена.')
                ->send();
        }

        return false;
    }
}
