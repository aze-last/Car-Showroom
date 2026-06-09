@php
    use App\Models\Setting;
@endphp

<x-layouts.public-showroom :title="Setting::get('shop_name', 'The Gallery') . ' | Privacy Policy'">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12 animate-showroom-fade-up">
        <header class="text-center space-y-4">
            <h1 class="text-5xl font-bold tracking-tighter text-black uppercase">Privacy Policy</h1>
            <p class="text-sm uppercase tracking-widest text-zinc-400">Effective Date: June 8, 2026</p>
            <div class="flex justify-center pt-4">
                <div class="h-[2px] w-16 bg-black"></div>
            </div>
        </header>

        <main class="bg-white rounded-[40px] border border-zinc-100 p-8 sm:p-12 shadow-sm space-y-8 text-zinc-900 leading-relaxed">
            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">1. Information We Collect</h2>
                <p>We collect personal information that you provide directly to us when creating a collector account, placing bids in auctions, posting bid deposits, or sending inquiries about our vehicles. This information may include your name, email address, phone number, and proof of deposit details.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">2. How We Use Your Information</h2>
                <p>We use the collected data to verify bidding eligibility, coordinate vehicle handovers (including guest walk-in checkout registrations), process bid deposits securely, send transaction notifications, and communicate directly with you about your portfolio and bids.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">3. Data Security & Storage</h2>
                <p>We protect all files, registration details, and documents using secure server protocols. Handover photos and transaction proof documents are stored locally using encrypted relative paths and are accessible only to verified administrators of the command center.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">4. Your Rights</h2>
                <p>As a registered collector, you have the right to inspect, update, or request the deletion of your personal garage profile and personal details. Contact our team at {{ Setting::get('shop_email', 'contact@thegallery.com') }} for any privacy-related requests.</p>
            </section>
        </main>
    </div>
</x-layouts.public-showroom>
