<div class="min-h-[80vh] flex items-center justify-center px-4 py-12 animate-showroom-fade-up">
    <div class="w-full max-w-md space-y-12">
        <div class="text-center">
            <h2 class="text-[12px] font-bold uppercase tracking-[0.4em] text-zinc-400">Join The Gallery</h2>
            <h1 class="text-5xl font-bold tracking-tighter text-black mt-2 leading-none">Start Your Collection</h1>
            <p class="mt-6 text-zinc-500 font-medium">Register to save vehicles and manage your private curator inquiries.</p>
        </div>

        <form wire:submit="register" class="bg-white rounded-[32px] border border-gallery-outline/20 p-10 ambient-shadow space-y-6">
            <div class="space-y-2">
                <label for="name" class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Full Name</label>
                <input wire:model="name" id="name" type="text" required class="w-full h-12 bg-gallery-surface-low border-none rounded-2xl px-6 text-sm font-medium focus:ring-2 focus:ring-black/5 transition-all">
                @error('name') <span class="text-red-500 text-[10px] font-bold uppercase tracking-widest">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label for="email" class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Email Address</label>
                <input wire:model="email" id="email" type="email" required class="w-full h-12 bg-gallery-surface-low border-none rounded-2xl px-6 text-sm font-medium focus:ring-2 focus:ring-black/5 transition-all">
                @error('email') <span class="text-red-500 text-[10px] font-bold uppercase tracking-widest">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label for="password" class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Security Key</label>
                <input wire:model="password" id="password" type="password" required class="w-full h-12 bg-gallery-surface-low border-none rounded-2xl px-6 text-sm font-medium focus:ring-2 focus:ring-black/5 transition-all">
                @error('password') <span class="text-red-500 text-[10px] font-bold uppercase tracking-widest">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label for="password_confirmation" class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Confirm Security Key</label>
                <input wire:model="password_confirmation" id="password_confirmation" type="password" required class="w-full h-12 bg-gallery-surface-low border-none rounded-2xl px-6 text-sm font-medium focus:ring-2 focus:ring-black/5 transition-all">
            </div>

            <button type="submit" class="w-full h-14 bg-black text-white font-bold uppercase tracking-widest text-[11px] rounded-full hover:opacity-90 transition-all duration-300 shadow-xl hover:shadow-2xl mt-4">
                Enter The Gallery
            </button>

            <div class="text-center pt-6">
                <a href="{{ route('login') }}" class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 hover:text-black transition-colors underline underline-offset-4 decoration-zinc-200 hover:decoration-black">Already registered?</a>
            </div>
        </form>
    </div>
</div>
