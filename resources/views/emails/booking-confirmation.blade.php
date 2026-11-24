@component('mail::message')
# Подтверждение записи

Здравствуйте, {{ $booking->client_name }}!

Ваша запись на услугу "{{ $booking->service->name }}" подтверждена.

**Детали:**
- Дата и время: {{ $booking->appointment_time->format('d.m.Y H:i') }}
- Цена: {{ $booking->service->price }} руб.
- Статус: {{ $booking->status }}

Если у вас есть вопросы, свяжитесь с нами по email: info@feminaluxe.ru или телефону: +7 (964) 646-89-27.

Спасибо, что выбрали Feminaluxe!
@endcomponent
