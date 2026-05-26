<section class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
        <div>
            <p class="text-[12px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Human Resources</p>
            <h2 class="text-3xl font-bold text-black">Staff Management</h2>
        </div>
    </div>

    @if (session('status'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl text-sm font-bold flex items-center gap-3 animate-showroom-fade-up">
            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17L4 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Create Form -->
        <article class="lg:col-span-1 bg-white rounded-[32px] border border-zinc-100 shadow-sm p-8 space-y-6 sticky top-28">
            <h3 class="text-sm font-bold text-black uppercase tracking-widest">New Staff Account</h3>
            
            <form wire:submit="create" class="space-y-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-2">Full Name</label>
                    <input type="text" wire:model="name" class="w-full bg-zinc-50 border-none rounded-2xl py-3 px-5 font-bold text-sm focus:ring-2 focus:ring-black transition-all" placeholder="John Doe">
                    @error('name') <p class="text-[10px] text-red-600 font-bold px-2">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-2">Email Address</label>
                    <input type="email" wire:model="email" class="w-full bg-zinc-50 border-none rounded-2xl py-3 px-5 font-bold text-sm focus:ring-2 focus:ring-black transition-all" placeholder="john@thegallery.com">
                    @error('email') <p class="text-[10px] text-red-600 font-bold px-2">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-2">Job Title</label>
                    <input type="text" wire:model="job_title" class="w-full bg-zinc-50 border-none rounded-2xl py-3 px-5 font-bold text-sm focus:ring-2 focus:ring-black transition-all" placeholder="Senior Curator">
                    @error('job_title') <p class="text-[10px] text-red-600 font-bold px-2">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-2">Password</label>
                    <input type="password" wire:model="password" class="w-full bg-zinc-50 border-none rounded-2xl py-3 px-5 font-bold text-sm focus:ring-2 focus:ring-black transition-all" placeholder="••••••••">
                    @error('password') <p class="text-[10px] text-red-600 font-bold px-2">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest px-2">Confirm Password</label>
                    <input type="password" wire:model="password_confirmation" class="w-full bg-zinc-50 border-none rounded-2xl py-3 px-5 font-bold text-sm focus:ring-2 focus:ring-black transition-all" placeholder="••••••••">
                </div>

                <button type="submit" class="w-full bg-black text-white font-bold text-[11px] uppercase tracking-widest py-4 rounded-2xl hover:opacity-90 transition-all ambient-shadow mt-4">
                    Register Staff
                </button>
            </form>
        </article>

        <!-- Staff List -->
        <article class="lg:col-span-2 bg-white rounded-[32px] border border-zinc-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-zinc-50 flex justify-between items-center">
                <h3 class="text-sm font-bold text-black uppercase tracking-widest">Active Curators</h3>
                <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">{{ $employees->total() }} Total Accounts</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-zinc-50/50">
                            <th class="px-8 py-4 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Curator</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Job Title</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Registry Date</th>
                            <th class="px-8 py-4 text-[10px] font-bold text-zinc-400 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50">
                        @forelse ($employees as $employee)
                            <tr class="hover:bg-zinc-50/30 transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-black text-white flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-black">{{ $employee->name }}</p>
                                            <p class="text-[10px] text-zinc-400 font-bold">{{ $employee->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-xs font-bold text-black bg-zinc-100 px-3 py-1 rounded-full uppercase tracking-wider">{{ $employee->job_title }}</span>
                                </td>
                                <td class="px-8 py-5 text-[11px] font-bold text-zinc-400">
                                    {{ $employee->created_at?->format('M d, Y') }}
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <button 
                                        wire:click="delete({{ $employee->id }})" 
                                        wire:confirm="Revoke all access for this staff member? This action is permanent."
                                        class="h-9 w-9 rounded-xl bg-red-50 text-red-400 hover:text-red-600 hover:bg-white hover:ambient-shadow transition-all border border-red-100 inline-flex items-center justify-center"
                                    >
                                        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <p class="text-[10px] text-zinc-300 font-bold uppercase tracking-widest">No curatorial staff registered</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-8 py-6 border-t border-zinc-50">
                {{ $employees->links() }}
            </div>
        </article>
    </div>
</section>
