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
                <p>To participate in timed auctions, you must register a collector account and hold a verified bid deposit for the specific lot you intend to bid on. The Platform reserves the right to cancel any bid placed without verification or sufficient deposit proof, and to suspend accounts that repeatedly fail to complete payment after winning an auction. Suspension terms (including duration and reinstatement conditions) are disclosed to the affected user at the time of suspension.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">2. Deposit & Verification Workflow</h2>
                <p>All bid deposits are subject to review by Platform administrators before bidding access is granted for the corresponding lot. You must upload clear, unaltered proof of payment.</p>
                <div class="space-y-2 pt-2">
                    <p class="font-semibold text-black">Refund terms:</p>
                    <ul class="list-disc list-inside space-y-1 text-zinc-700 pl-2 text-black">
                        <li>Deposits from unsuccessful bidders are released within 5 business days of an auction closing, to the original payment method or account used.</li>
                        <li>Deposits from a winning bidder who fails to complete payment within the stated payment window are forfeited in full and are non-refundable, in addition to any account strikes applied under Section 1.</li>
                        <li>Deposits are held, not treated as partial payment, unless and until a bid is won and payment is completed.</li>
                    </ul>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">3. Handover & Guest Checkout</h2>
                <p>For walk-in sales and completed auctions, accurate registration (full name and contact details for guest buyers) and photo proof of vehicle handover are required before a transaction is considered complete. This information is collected and stored in accordance with our Privacy Policy.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">4. Vehicle Listings & Disclaimer</h2>
                <p>Vehicles are listed with data sheets verified by Platform staff at the time of listing. All vehicles are sold on an "as-is" basis. Bidders and buyers are strongly encouraged to physically inspect a vehicle at our showroom before a bid closes or a purchase is finalized. The Platform does not guarantee the ongoing accuracy of a listing after physical inspection has been offered and declined.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">5. Limitation of Liability</h2>
                <p>To the maximum extent permitted by Philippine law, the Platform and its operator(s) are not liable for indirect, incidental, or consequential damages arising from use of the Platform, including but not limited to system downtime, listing errors, or bidding system malfunctions, except where such damages result from gross negligence or willful misconduct. This limitation does not affect any rights you may have under the Consumer Act of the Philippines (RA 7394) that cannot be waived by agreement.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">6. Dispute Resolution</h2>
                <p>Before pursuing formal legal action, both parties agree to first attempt to resolve any dispute directly by contacting <a href="mailto:{{ Setting::get('shop_email', 'contact@thegallery.com') }}" class="underline hover:text-black">{{ Setting::get('shop_email', 'contact@thegallery.com') }}</a> within 30 days of the issue arising. If unresolved after 30 days, either party may pursue the matter through the appropriate courts.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">7. Governing Law</h2>
                <p>These Terms are governed by the laws of the Republic of the Philippines. Any dispute not resolved under Section 6 shall be subject to the exclusive jurisdiction of the courts of Davao del Sur.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">8. Changes to These Terms</h2>
                <p>We may update these Terms from time to time. Continued use of the Platform after changes are posted constitutes acceptance of the revised Terms.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">9. Contact</h2>
                <p>Questions about these Terms: <a href="mailto:{{ Setting::get('shop_email', 'contact@thegallery.com') }}" class="underline hover:text-black">{{ Setting::get('shop_email', 'contact@thegallery.com') }}</a></p>
            </section>
        </main>
    </div>
</x-layouts.public-showroom>

