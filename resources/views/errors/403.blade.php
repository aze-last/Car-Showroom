<x-layouts.public-showroom>
    @include('errors.partials.content', [
        'code' => 403,
        'title' => 'Access restricted',
        'message' => 'You do not have permission to view this page. If you believe this is a mistake, please sign in with the correct account or contact the gallery.',
    ])
</x-layouts.public-showroom>
