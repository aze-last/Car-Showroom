<!-- Global Toast Notification Center -->
<div
    x-data="{
        toasts: [],
        addToast(message, type = 'success') {
            const id = Date.now();
            this.toasts.push({ id, message, type });
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 4000);
        }
    }"
    x-init="
        @if(session()->has('toast'))
            $nextTick(() => addToast(@js(session('toast')['message']), @js(session('toast')['type'])));
        @endif
        @if(session()->has('error'))
            $nextTick(() => addToast(@js(session('error')), 'error'));
        @endif
    "
    @toast.window="addToast($event.detail.message, $event.detail.type)"
    class="fixed top-24 right-6 z-[100] flex flex-col gap-3 pointer-events-none w-full max-w-sm"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-[-20px] opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0 opacity-100"
            x-transition:leave-end="translate-y-[-20px] opacity-0"
            class="pointer-events-auto bg-black/95 backdrop-blur-md text-white rounded-2xl px-6 py-4 shadow-[0_20px_40px_-5px_rgba(0,0,0,0.4)] border border-white/10 flex items-center justify-between gap-4"
        >
            <div class="flex items-center gap-3">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="toast.type === 'error' ? 'bg-red-400' : (toast.type === 'info' ? 'bg-zinc-400' : 'bg-emerald-400')"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2" :class="toast.type === 'error' ? 'bg-red-500' : (toast.type === 'info' ? 'bg-zinc-500' : 'bg-emerald-500')"></span>
                </span>
                <p class="text-[10px] font-bold uppercase tracking-widest leading-normal" x-text="toast.message"></p>
            </div>
            <button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-white/45 hover:text-white transition-colors shrink-0">
                <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>
