<?php

	declare(strict_types=1);

	namespace App\Mail;

	use App\Domains\Bookings\Models\Booking;
	use App\Filament\Resources\BookingResource;
	use Illuminate\Bus\Queueable;
	use Illuminate\Contracts\Queue\ShouldQueue;
	use Illuminate\Mail\Mailable;
	use Illuminate\Mail\Mailables\Address;
	use Illuminate\Mail\Mailables\Content;
	use Illuminate\Mail\Mailables\Envelope;
	use Illuminate\Queue\SerializesModels;

	class AdminNewBookingNotification extends Mailable implements ShouldQueue
	{
		use Queueable, SerializesModels;

		public int $tries = 3;
		public int $backoff = 60;

		public function __construct(
			public readonly Booking $booking
		)
		{
			$this->booking->loadMissing('service');
		}

		public function envelope(): Envelope
		{
			$serviceName = $this->booking->service?->name ?? 'Услуга не указана';
			$date = $this->booking->appointment_time?->format('d.m.Y H:i') ?? '';

			return new Envelope(
				from: new Address(
					config('mail.from.address', 'noreply@feminaluxe.ru'),
					config('mail.from.name', 'Feminaluxe')
				),
				replyTo: $this->booking->client_email
					? [new Address($this->booking->client_email, $this->booking->client_name)]
					: [],
				subject: "Новая запись #{$this->booking->id}: {$serviceName} ({$date})",
			);
		}

		public function content(): Content
		{
			return new Content(
				view: 'emails.admin-new-booking',
				with: [
					'adminUrl' => BookingResource::getUrl('edit', ['record' => $this->booking]),
				],
			);
		}

		public function attachments(): array
		{
			return [];
		}
	}
