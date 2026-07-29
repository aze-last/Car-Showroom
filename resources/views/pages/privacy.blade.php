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
                <p>We collect personal information you provide directly when you create a collector account, place bids, submit bid deposits, complete a guest checkout, or send an inquiry about a vehicle. This may include your name, email address, phone number, proof-of-payment/deposit details, and — for guest and handover transactions — a photo taken at the point of vehicle handover.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">2. How We Use Your Information</h2>
                <p>We use your information to:</p>
                <ul class="list-disc list-inside space-y-1 text-zinc-700 pl-2 text-black">
                    <li>Verify bidding eligibility and enable bidding access for a specific auction lot</li>
                    <li>Coordinate vehicle handovers, including guest walk-in checkout records</li>
                    <li>Process and verify bid deposits</li>
                    <li>Send transaction notifications (bid confirmations, auction outcomes, payment reminders)</li>
                    <li>Communicate with you about your account and active bids</li>
                    <li>Comply with legal or regulatory obligations, where applicable</li>
                </ul>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">3. Legal Basis for Processing</h2>
                <p>We process your information based on your consent (given when you register or submit a transaction) and, where applicable, because processing is necessary to perform the transaction you've requested (e.g., verifying a deposit to enable bidding).</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">4. Data Security & Storage</h2>
                <p>We apply reasonable technical and organizational measures to protect your information, including restricting access to registration details and transaction documents to authorized Platform staff. Handover photos and payment proof documents are stored using secure relative storage paths and are accessible only to verified administrators of the command center.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">5. Data Retention</h2>
                <p>We retain your registration details and transaction records for as long as your account is active, and as needed for dispute resolution, fraud prevention, or legal compliance. Handover and deposit-proof photos are retained following a completed transaction.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">6. Third-Party Service Providers</h2>
                <p>We may share limited data with third-party service providers who help us operate the Platform, including hosting/infrastructure providers and network security services, solely to the extent needed to provide those services. These providers are not authorized to use your data for their own purposes.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">7. Your Rights</h2>
                <p>Under RA 10173 (Data Privacy Act of 2012), you have the right to:</p>
                <ul class="list-disc list-inside space-y-1 text-zinc-700 pl-2 text-black">
                    <li>Be informed that your data is being collected and processed</li>
                    <li>Access and obtain a copy of your personal data</li>
                    <li>Correct inaccurate or outdated information</li>
                    <li>Request deletion or withdrawal of your data, subject to our legal obligation to retain certain transaction records</li>
                    <li>Object to processing, where applicable</li>
                    <li>File a complaint with the National Privacy Commission if you believe your rights have been violated</li>
                </ul>
                <p class="pt-2">To exercise any of these rights, contact us at <a href="mailto:{{ Setting::get('shop_email', 'contact@thegallery.com') }}" class="underline hover:text-black">{{ Setting::get('shop_email', 'contact@thegallery.com') }}</a>.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">8. Data Breach Notification</h2>
                <p>In the event of a data breach that poses a real risk of harm to you, we will notify affected users and, where required, the National Privacy Commission, in accordance with RA 10173 and its implementing rules.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">9. Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. Continued use of the Platform after changes are posted constitutes acknowledgment of the revised policy.</p>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-bold tracking-tight text-black">10. Contact</h2>
                <p>Privacy-related requests or questions: <a href="mailto:{{ Setting::get('shop_email', 'contact@thegallery.com') }}" class="underline hover:text-black">{{ Setting::get('shop_email', 'contact@thegallery.com') }}</a></p>
            </section>
        </main>
    </div>
</x-layouts.public-showroom>

