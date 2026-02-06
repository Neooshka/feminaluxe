<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class StaticPageController extends Controller
{
    /**
     * Упрощенное уведомление об использовании файлов cookie
     */
    public function cookiePolicy()
    {
        return Inertia::render('Static/CookiePolicy', [
            'title' => 'Использование файлов cookie',
            'lastUpdated' => '2026-02-06',
        ]);
    }

    /**
     * Политика конфиденциальности
     */
    public function privacy()
    {
        return Inertia::render('Static/PrivacyPolicy', [
            'title' => 'Политика конфиденциальности',
            'effectiveDate' => '2026-02-06',
            'company' => [
                'name' => 'ИП Украинцев Дмитрий Вячеславович',
                'inn' => '691605427114',
                'ogrn' => '323695200028147',
                'address' => 'Тверская обл, г Удомля, ул Александрова, д 10, кв 128',
                'email' => 'neeeo@mail.ru',
                'phone' => '+7 (993) 759-81-80',
            ],
        ]);
    }

    /**
     * Пользовательское соглашение
     */
    public function agreement()
    {
        return Inertia::render('Static/UserAgreement', [
            'title' => 'Пользовательское соглашение',
            'effectiveDate' => '2026-02-06',
            'company' => [
                'name' => 'ИП Украинцев Дмитрий Вячеславович',
                'inn' => '691605427114',
                'ogrn' => '323695200028147',
                'address' => 'Тверская обл, г Удомля, ул Александрова, д 10, кв 128',
                'email' => 'neeeo@mail.ru',
                'phone' => '+7 (993) 759-81-80',
                'bank' => [
                    'account' => '40802810100004782133',
                    'bank_name' => 'АО "ТИНЬКОФФ БАНК"',
                    'bik' => '044525974',
                    'correspondent' => '30101810145250000974',
                ],
            ],
            'links' => [
                'booking' => route('bookings.create'),
                'contact' => route('contact'),
                'agreement' => route('agreement'),
                'privacy' => route('privacy'),
            ],
        ]);
    }
}