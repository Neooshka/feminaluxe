<?php

	declare(strict_types=1);

	namespace App\Listeners;

	use App\Events\BookingCreated;
	use App\Mail\AdminNewBookingNotification;
	use App\Mail\BookingConfirmation;
	use App\Models\User;
	use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
	use Illuminate\Contracts\Queue\ShouldQueue;
	use Illuminate\Queue\InteractsWithQueue;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Mail;
	use Throwable;

	class SendBookingConfirmation implements ShouldQueue, ShouldHandleEventsAfterCommit
	{
		use InteractsWithQueue;

		public int $tries = 3;
		public int $backoff = 30;

		public function handle(BookingCreated $event): void
		{
			$booking = $event->booking;

			// 1. Уведомление клиенту
			if(!empty($booking->client_email)) {
				Mail::to($booking->client_email)->queue(new BookingConfirmation($booking));
			}

			// 2. Получаем email всех активных администраторов
			$adminEmails = User::query()
			                   ->active()
			                   ->admins()
			                   ->whereNotNull('email')
			                   ->pluck('email')
			                   ->filter(fn(string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false)
			                   ->unique()
			                   ->values();

			// Fallback на конфиг, если в БД нет активных админов
			if($adminEmails->isEmpty()) {
				$fallbackEmail = config('mail.admin_email');

				if(!empty($fallbackEmail) && filter_var($fallbackEmail, FILTER_VALIDATE_EMAIL) !== false) {
					$adminEmails = collect([$fallbackEmail]);
				}
			}

			if($adminEmails->isEmpty()) {
				Log::warning('No active admin emails found. New booking notification skipped.', [
					'booking_id' => $booking->id,
				]);
				return;
			}

			// Отправляем каждому админу отдельной задачей в очередь
			foreach($adminEmails as $email) {
				Mail::to($email)->queue(new AdminNewBookingNotification($booking));
			}
		}

		public function failed(BookingCreated $event, Throwable $exception): void
		{
			Log::error('Failed to queue booking email notifications', [
				'booking_id' => $event->booking->id,
				'error' => $exception->getMessage(),
			]);
		}
	}
