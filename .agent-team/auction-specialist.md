# Auction House Specialist

## Objective
Orchestrate the premium auction lifecycle, ensuring real-time bid integrity, financial verification (deposits), and automated event transitions from scheduling to completion.

## Core Logic (Source of Truth)

### 1. Status Lifecycle
- **`scheduled`**: Initial state. Auction is visible in the registry but not open for bids.
- **`live`**: Active state. Users with approved deposits can enter the Auction Room and place bids.
- **`completed`**: Auction has reached its `end_at` time. A winner is identified, and the payment deadline (48h) starts.
- **`cancelled`**: Auction was closed manually by admin or ended with zero bids.

### 2. Automation (CheckAuctionDeadlines)
The `auction:check-deadlines` command MUST be scheduled to run every minute to:
- **Auto-Activate:** Transition `scheduled` -> `live` when `start_at <= now()`.
- **Auto-Finalize:** Transition `live` -> `completed` when `end_at <= now()`.
- **Payment Enforcement:** Track the 48-hour `payment_deadline`. If unpaid, forfeit deposit and increment user strike.

### 3. Participant Workflow
1. **Discovery:** User views Registry in `AuctionHall`.
2. **Entry:** User submits `BidDeposit` (Proof of payment).
3. **Verification:** Admin approves deposit (status `approved`).
4. **Bidding:** User gains access to `AuctionRoom`.
5. **Anti-Sniping:** Any bid placed in the final 2 minutes extends the `end_at` time by an additional 2 minutes.

## UI/UX Standards

- **Featured Spotlight:** Only 1 `live` or `active` auction is featured at the top. Expired auctions MUST be filtered out immediately.
- **Registry Visibility:** Use the `animate-showroom-fade-up` animation for all lot cards. NEVER use `opacity-0` without a JS trigger.
- **Live Badges:** Use `emerald-500` for "ACTIVE" status and `red-600` for "LIVE NOW" hero badges.
- **Real-Time Timers:** Use Alpine.js in the frontend to handle countdowns, synchronized with the server's `end_at` timestamp.

## Security Mandates
- **Concurrency:** Always use `lockForUpdate()` during bid placement to prevent race conditions on `current_bid_php`.
- **Access Control:** `AuctionRoom` must verify that the auction `isLive()` and the user has an `approved` deposit for that specific auction.
