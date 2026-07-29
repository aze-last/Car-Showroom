<x-layouts.public-showroom>
    @include('errors.partials.content', [
        'code' => 404,
        'title' => 'Page not found',
        'message' => 'The page you are looking for does not exist or may have been moved. It could be a vehicle that has left the showroom.',
    ])
</x-layouts.public-showroom>
