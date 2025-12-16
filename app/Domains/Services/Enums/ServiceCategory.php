<?php

namespace App\Domains\Services\Enums;

enum ServiceCategory: string
{
    case BROWS = 'brows';
    case LIPS = 'lips';
    case EYELASHES = 'eyelashes';
    case CORRECTION = 'correction';
    case REMOVAL = 'removal';
    case TRAINING = 'training';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::BROWS => 'Перманентный макияж бровей',
            self::LIPS => 'Перманентный макияж губ',
            self::EYELASHES => 'Межресничный перманент',
            self::CORRECTION => 'Коррекция татуажа',
            self::REMOVAL => 'Удаление татуажа',
            self::TRAINING => 'Обучение',
            self::OTHER => 'Другие услуги',
        };
    }

    public function slug(): string
    {
        return match($this) {
            self::BROWS => 'permanent-makeup-brows',
            self::LIPS => 'permanent-makeup-lips',
            self::EYELASHES => 'eyelash-permanent',
            self::CORRECTION => 'tattoo-correction',
            self::REMOVAL => 'tattoo-removal',
            self::TRAINING => 'training',
            self::OTHER => 'other-services',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::BROWS => 'Пудровое напыление, волосковый метод, комбинированные брови',
            self::LIPS => 'Акварельный татуаж, контур губ, увеличение объема',
            self::EYELASHES => 'Межресничное пространство, стрелки, подводка',
            self::CORRECTION => 'Исправление неудачного татуажа, коррекция цвета',
            self::REMOVAL => 'Лазерное удаление, коррекция перманентного макияжа',
            self::TRAINING => 'Курсы по перманентному макияжу для начинающих и мастеров',
            self::OTHER => 'Сопутствующие услуги и процедуры',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::BROWS => 'eye-brow',
            self::LIPS => 'lips',
            self::EYELASHES => 'eye',
            self::CORRECTION => 'refresh',
            self::REMOVAL => 'trash',
            self::TRAINING => 'academic-cap',
            self::OTHER => 'dots-circle-horizontal',
        };
    }

    public static function forSelect(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public static function getCategoryBySlug(string $slug): ?self
    {
        return collect(self::cases())
            ->first(fn ($case) => $case->slug() === $slug);
    }
}
