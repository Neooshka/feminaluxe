<?php

namespace App\Domains\Services\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ServiceIndexData extends Data
{
    /**
     * @param DataCollection<int, TransformedService> $services
     */
    public function __construct(
        public readonly DataCollection $services,
    )
    {
    }
}
