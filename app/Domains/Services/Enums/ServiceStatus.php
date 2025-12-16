<?php

namespace App\Domains\Services\Enums;

enum ServiceStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ARCHIVED = 'archived';
    case COMING_SOON = 'coming_soon';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Черновик',
            self::ACTIVE => 'Активна',
            self::INACTIVE => 'Неактивна',
            self::ARCHIVED => 'В архиве',
            self::COMING_SOON => 'Скоро будет',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::ACTIVE => 'green',
            self::INACTIVE => 'yellow',
            self::ARCHIVED => 'red',
            self::COMING_SOON => 'blue',
        };
    }

    public static function forSelect(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
