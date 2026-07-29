<x-layouts.public-showroom>
    @include('errors.partials.content', [
        'code' => 419,
        'title' => 'Your session expired',
        'message' => 'For your security, your session timed out after a period of inactivity. Nothing was lost — please go back and try that action again.',
    ])
</x-layouts.public-showroom>
