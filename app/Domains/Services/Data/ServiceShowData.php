<?php

namespace App\Domains\Services\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ServiceShowData extends Data
{
    /**
     * @param DataCollection<int, array> $reviews
     * @param DataCollection<int, TransformedService> $relatedServices
     */
    public function __construct(
        public readonly int            $id,
        public readonly string         $slug,
        public readonly string         $category,
        public readonly string         $name,
        public readonly string         $description,
        public readonly string         $short_description,
        public readonly string         $detailed_description,
        public readonly float          $price,
        public readonly ?float         $discounted_price,
        public readonly ?string        $duration,
        public readonly ?string        $primary_image,
        public readonly array          $images,
        public readonly array          $benefits,
        public readonly array          $contraindications,
        public readonly ?float         $rating,
        public readonly int            $reviews_count,
        public readonly int            $views_count,
        public readonly DataCollection $reviews,
        public readonly DataCollection $faqs,
        public readonly DataCollection $relatedServices,
        public readonly bool           $is_available,
        public readonly bool           $requires_consultation,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly Carbon         $created_at,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly Carbon         $updated_at,
    )
    {
    }
}
