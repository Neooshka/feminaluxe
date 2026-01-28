<div class="fc-event-main-frame p-1 text-xs leading-tight">
    <div class="fc-event-title font-semibold truncate">{{ $event['title'] }}</div>
    <div class="fc-event-time opacity-90">{{ Carbon\Carbon::parse($event['start'])->format('H:i') }} - {{ Carbon\Carbon::parse($event['end'])->format('H:i') }}</div>
    <div class="opacity-75">{{ $event['extendedProps']['client_phone'] ?? '' }}</div>
</div>