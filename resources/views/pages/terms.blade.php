@php
    use App\Models\Setting;
@endphp

<x-layouts.public-showroom :title="Setting::get('shop_name', 'The Gallery') . ' | Terms of Service'">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12 animate-showroom-fade-up">
        <header class="text-center space-y-4">
            <h1 class="text-5xl font-bold tracking-tighter text-black uppercase">Terms of Service</h1>
            <p class="text-sm uppercase tracking-widest text-zinc-400">Effective Date: June 8, 2026</p>
            <div class="flex justify-center pt-4">
                <div class="h-[2px] w-16 bg-black"></div>
            </div>
        </header>

        <main class="bg-white rounded-[40px] border border-zinc-100 p-8 sm:p-12 shadow-sm space-y-8 text-zinc-900 leading-relaxed">
            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">1. Bidding Eligibility</h2>
                <p>To participate in our premium timed auctions, you must register a collector account and hold a verified bid deposit for active lots. Bids placed without verification or sufficient deposit proof may be cancelled at the sole discretion of the administrator.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">2. Deposit & Verification Workflow</h2>
                <p>All bid deposits are subject to verification by the admin command center. Users must upload clear proof of payment. Once verified, bidding permissions will be enabled for the corresponding auction lot. Unsuccessful bidders' deposits will be handled according to our refund terms.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">3. Handover & Guest Checkout</h2>
                <p>For walk-in sales or auction completions, accurate guest registration (including full name and contact details) and photo proof of vehicle handover are required at checkout. All relative files are stored securely and in compliance with state regulations.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">4. Disclaimer & Liability</h2>
                <p>Vehicles listed are described with verified data sheets. However, all listings are sold on an "as-is" certified basis. Bidders are highly encouraged to inspect vehicles at our landmark showroom before bidding closes.</p>
            </section>
        </main>
    </div>
</x-layouts.public-showroom>
