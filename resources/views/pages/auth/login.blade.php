<x-layouts::auth.admin>
    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf

            <div class="space-y-2">
                <label for="email" class="text-sm font-medium text-slate-700">Email Address</label>
                <input 
                    id="email"
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    placeholder="name@example.com"
                    class="admin-input @error('email') border-red-500 @enderror"
                >
                @error('email')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label for="password" class="text-sm font-medium text-slate-700">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-slate-600 hover:text-slate-900">
                            Forgot password?
                        </a>
                    @endif
                </div>
                <input 
                    id="password"
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••"
                    class="admin-input @error('password') border-red-500 @enderror"
                >
                @error('password')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-slate-900 shadow-sm focus:ring-slate-500">
                    <span class="ms-2 text-sm text-slate-600">Keep me logged in</span>
                </label>
            </div>

            <button type="submit" class="admin-btn-primary w-full py-3 text-base">
                Sign In
            </button>
        </form>

        @if (Route::has('register'))
            <div class="pt-4 text-center text-sm text-slate-500">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-semibold text-slate-900 hover:underline">Contact Admin</a>
            </div>
        @endif
    </div>
</x-layouts::auth.admin>
