<?php

declare(strict_types=1);

namespace App\Domains\Services\Http\Controllers;

use App\Domains\Services\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        $services = Service::with('media')->get()->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'price' => $service->price,
                'images' => $service->getMedia('before_after')->map(fn($media) => $media->getUrl('thumb')),
            ];
        });

        return Inertia::render('Services/Index', [
            'services' => $services,
        ])->withViewData([
            'seo' => [
                'title' => 'Услуги перманентного макияжа | Feminaluxe',
                'description' => 'Брови, губы, межреснички с профессиональными мастерами. Цены и фото до/после.',
            ],
        ]);
    }

    public function show(Service $service): Response
    {
        $service->load('media', 'reviews.approved');

        return Inertia::render('Services/Show', [
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'price' => $service->price,
                'images' => $service->getMedia('before_after')->map(fn($media) => [
                    'thumb' => $media->getUrl('thumb'),
                    'full' => $media->getUrl(),
                ]),
                'reviews' => $service->reviews->map(fn($review) => [
                    'author' => $review->author,
                    'content' => $review->content,
                    'rating' => $review->rating,
                ]),
            ],
        ])->withViewData([
            'seo' => [
                'title' => $service->name . ' | Feminaluxe',
                'description' => \Illuminate\Support\Str::limit($service->description, 150),
            ],
        ]);
    }
}
