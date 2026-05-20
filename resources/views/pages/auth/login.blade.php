<x-layouts::auth.admin>
    <div class="space-y-8">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-bold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="email" class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Email Address</label>
                <input 
                    id="email"
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    placeholder="name@example.com"
                    class="h-12 w-full rounded-xl border border-zinc-100 bg-zinc-50 px-4 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none transition-all @error('email') border-red-500 @enderror"
                >
                @error('email')
                    <p class="text-[10px] font-bold text-red-600 uppercase">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label for="password" class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[10px] font-black uppercase tracking-[0.1em] text-zinc-300 hover:text-zinc-900 transition-colors">
                            Forgot?
                        </a>
                    @endif
                </div>
                <input 
                    id="password"
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••"
                    class="h-12 w-full rounded-xl border border-zinc-100 bg-zinc-50 px-4 text-sm text-zinc-900 focus:border-zinc-900 focus:bg-white focus:outline-none transition-all @error('password') border-red-500 @enderror"
                >
                @error('password')
                    <p class="text-[10px] font-bold text-red-600 uppercase">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <label class="inline-flex cursor-pointer items-center group">
                    <input type="checkbox" name="remember" class="rounded-md border-zinc-200 text-zinc-900 focus:ring-0">
                    <span class="ms-3 text-xs font-bold text-zinc-400 group-hover:text-zinc-600 transition-colors">Keep me signed in</span>
                </label>
            </div>

            <button type="submit" class="w-full rounded-xl bg-zinc-900 py-4 text-xs font-black uppercase tracking-[0.2em] text-white shadow-xl transition-all hover:bg-zinc-800 active:scale-95">
                Authorize Access
            </button>
        </form>

        @if (Route::has('register'))
            <div class="border-t border-zinc-50 pt-6 text-center">
                <p class="text-[10px] font-black uppercase tracking-[0.1em] text-zinc-300">
                    No account? <a href="{{ route('register') }}" class="text-zinc-400 hover:text-zinc-900 transition-colors">Contact management</a>
                </p>
            </div>
        @endif
    </div>
</x-layouts::auth.admin>
