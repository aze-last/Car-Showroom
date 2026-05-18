<section class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Inquiries</h2>
            <p class="text-sm text-slate-500">Manage customer inquiries from vehicle detail pages.</p>
        </div>
    </div>

    <article class="admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-4 py-3 sm:px-5">Date</th>
                        <th scope="col" class="px-4 py-3">Vehicle</th>
                        <th scope="col" class="px-4 py-3">Customer</th>
                        <th scope="col" class="px-4 py-3">Contact</th>
                        <th scope="col" class="px-4 py-3">Message</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                        <th scope="col" class="px-4 py-3 text-right sm:px-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($inquiries as $inquiry)
                        <tr wire:key="inquiry-row-{{ $inquiry->id }}" class="{{ $inquiry->status === 'unread' ? 'bg-amber-50/30 font-medium' : 'bg-white' }} hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ $inquiry->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">
                                <p class="text-slate-900">{{ $inquiry->unit?->name ?? 'Deleted Unit' }}</p>
                                <p class="text-xs text-slate-500">#{{ $inquiry->unit?->public_id }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-900">{{ $inquiry->name }}</td>
                            <td class="px-4 py-3">
                                <p class="text-slate-700">{{ $inquiry->email }}</p>
                                <p class="text-xs text-slate-500">{{ $inquiry->phone }}</p>
                            </td>
                            <td class="max-w-xs px-4 py-3 text-slate-600 truncate" title="{{ $inquiry->message }}">
                                {{ $inquiry->message }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $inquiry->status === 'unread' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-800' }}">
                                    {{ ucfirst($inquiry->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right sm:px-5">
                                <div class="flex justify-end gap-2">
                                    @if ($inquiry->status === 'unread')
                                        <button wire:click="markAsRead({{ $inquiry->id }})" class="text-xs text-slate-600 hover:text-slate-900">Mark as Read</button>
                                    @else
                                        <button wire:click="markAsUnread({{ $inquiry->id }})" class="text-xs text-slate-600 hover:text-slate-900">Mark as Unread</button>
                                    @endif
                                    <button wire:click="delete({{ $inquiry->id }})" wire:confirm="Delete this inquiry?" class="text-xs text-red-600 hover:text-red-900">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                No inquiries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    <div class="flex justify-end">
        {{ $inquiries->links() }}
    </div>
</section>
