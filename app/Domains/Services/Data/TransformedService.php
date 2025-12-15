<?php

declare(strict_types=1);

namespace App\Domains\Services\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Carbon\Carbon;

class TransformedService extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $slug,
        public readonly string $category,
        public readonly string $name,
        public readonly string $description,
        public readonly string $short_description,
        public readonly float $price,
        public readonly ?float $discounted_price,
        public readonly ?string $duration,
        public readonly ?string $primary_image,
        public readonly array $images,
        public readonly ?float $rating,
        public readonly int $reviews_count,
        public readonly bool $is_popular,
        public readonly bool $has_promotion,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly Carbon $created_at,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly Carbon $updated_at,
    ) {}
}
