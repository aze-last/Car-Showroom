<div class="space-y-8">
    <div class="flex justify-between items-end">
        <div>
            <p class="text-[12px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Financial Oversight</p>
            <h2 class="text-4xl font-bold text-black tracking-tighter">Deposit Verification</h2>
        </div>
    </div>

    <div class="bg-white rounded-[40px] border border-zinc-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-zinc-50">
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-400">Collector / Bidder</th>
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-400">Target Auction</th>
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-400">Amount</th>
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-400">Proof of Payment</th>
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-zinc-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50">
                    @forelse($deposits as $deposit)
                        <tr class="hover:bg-zinc-50/50 transition-colors">
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-full bg-zinc-900 text-white flex items-center justify-center text-xs font-black">
                                        {{ strtoupper(substr($deposit->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-black">{{ $deposit->user->name }}</p>
                                        <p class="text-[10px] text-zinc-400 font-medium">{{ $deposit->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <p class="text-sm font-bold text-black">{{ $deposit->auction->unit->name }}</p>
                                <p class="text-[10px] text-zinc-400 font-medium uppercase tracking-widest">Lot #{{ $deposit->auction->lot_number }}</p>
                            </td>
                            <td class="px-10 py-8">
                                <span class="text-sm font-black text-black">₱{{ number_format($deposit->amount) }}</span>
                            </td>
                            <td class="px-10 py-8">
                                <div class="relative w-24 h-16 rounded-xl overflow-hidden bg-zinc-100 group cursor-zoom-in" x-on:click="$flux.modal('view-proof-{{ $deposit->id }}').show()">
                                    <img src="{{ Storage::url($deposit->proof_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                        <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-white" stroke="currentColor" stroke-width="3"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
                                    </div>
                                </div>

                                {{-- Image Modal --}}
                                <flux:modal name="view-proof-{{ $deposit->id }}" class="max-w-4xl rounded-[40px]">
                                    <div class="p-4">
                                        <img src="{{ Storage::url($deposit->proof_image) }}" class="w-full h-auto rounded-3xl shadow-2xl">
                                    </div>
                                </flux:modal>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex gap-3">
                                    <button 
                                        wire:click="approve({{ $deposit->id }})" 
                                        wire:loading.attr="disabled"
                                        class="bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest px-6 py-3 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20"
                                    >
                                        Approve
                                    </button>
                                    <button 
                                        wire:click="openRejectModal({{ $deposit->id }})"
                                        x-on:click="$flux.modal('reject-deposit-modal').show()"
                                        class="bg-white border border-zinc-100 text-red-600 text-[10px] font-black uppercase tracking-widest px-6 py-3 rounded-xl hover:bg-red-50 hover:border-red-100 transition-all"
                                    >
                                        Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-10 py-32 text-center">
                                <div class="opacity-20 flex flex-col items-center gap-4">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-16 w-16" stroke="currentColor" stroke-width="1.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="text-sm font-black uppercase tracking-[0.4em]">No pending deposits</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-10 border-t border-zinc-50 bg-zinc-50/50">
            {{ $deposits->links() }}
        </div>
    </div>

    {{-- Reject Modal --}}
    <flux:modal name="reject-deposit-modal" class="max-w-lg rounded-[40px]">
        <form wire:submit.prevent="reject" class="p-8 space-y-6">
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest px-2">Reason for Rejection</label>
                <textarea 
                    wire:model="adminNote" 
                    placeholder="e.g. Screenshot blurry, Amount doesn't match..." 
                    class="w-full bg-zinc-50 border-none rounded-3xl py-5 px-6 font-bold text-sm focus:ring-2 focus:ring-red-600 transition-all min-h-[150px]"
                ></textarea>
                @error('adminNote') <span class="text-red-600 text-[10px] font-bold uppercase tracking-widest px-2">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-4">
                <flux:modal.close>
                    <button type="button" class="flex-1 bg-zinc-50 text-zinc-500 font-black uppercase tracking-widest text-[10px] py-5 rounded-2xl">Cancel</button>
                </flux:modal.close>
                <button type="submit" class="flex-1 bg-red-600 text-white font-black uppercase tracking-widest text-[10px] py-5 rounded-2xl shadow-xl shadow-red-600/20 active:scale-95">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </flux:modal>
</div>
