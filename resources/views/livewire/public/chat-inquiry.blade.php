<div class="chat-inquiry-root">
    @teleport('body')
        <div 
            class="fixed bottom-8 right-8 z-[100]" 
            x-data="{ 
                scrollToBottom() {
                    $nextTick(() => {
                        const container = $refs.messageContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                }
            }" 
            x-init="scrollToBottom()" 
            @chat-opened.window="scrollToBottom()" 
            @message-sent.window="scrollToBottom()" 
            @message-received.window="scrollToBottom()"
        >
            @if($isOpen)
                @if($isMinimized)
                    <!-- Chathead Bubble -->
                    <button 
                        wire:click="expand"
                        wire:loading.attr="disabled"
                        class="group relative h-16 w-16 rounded-full bg-zinc-950 text-white shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-300 animate-showroom-fade-up cursor-pointer"
                    >
                        @if($logo)
                            <img src="{{ Storage::url($logo) }}" class="h-8 w-8 object-contain" alt="">
                        @else
                            <span class="text-xs font-black uppercase tracking-widest">{{ substr($shopName, 0, 1) }}</span>
                        @endif
                        
                        <!-- Red Dot Notification -->
                        @php
                            $unreadCount = $messages->where('is_from_admin', true)->whereNull('read_at')->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 h-5 w-5 bg-red-600 border-2 border-white rounded-full flex items-center justify-center text-[8px] font-bold">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>
                @else
                    <!-- Expanded Chat Box -->
                    <div 
                        class="w-[380px] h-[550px] bg-white rounded-[32px] shadow-[0_40px_100px_-15px_rgba(0,0,0,0.2)] border border-zinc-100 flex flex-col overflow-hidden animate-showroom-fade-up"
                        wire:poll.5s="checkAutoReply"
                    >
                        <!-- Header -->
                        <div class="bg-zinc-950 p-6 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-white/10 flex items-center justify-center text-white border border-white/10 backdrop-blur-md">
                                    @if($logo)
                                        <img src="{{ Storage::url($logo) }}" class="h-6 w-6 object-contain" alt="">
                                    @else
                                        <span class="text-[10px] font-black">{{ substr($shopName, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-white uppercase tracking-[0.2em] leading-none">{{ $shopName }}</p>
                                    <p class="text-[9px] text-zinc-500 font-bold mt-1.5 flex items-center gap-1.5">
                                        <span class="h-1 w-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Curators Online
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    wire:click="minimize" 
                                    class="p-2 text-zinc-400 hover:text-white transition-colors cursor-pointer"
                                    title="Minimize"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14" stroke-linecap="round"/></svg>
                                </button>
                                <button 
                                    wire:click="close" 
                                    class="p-2 text-zinc-400 hover:text-white transition-colors cursor-pointer"
                                    title="Close"
                                >
                                    <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12" stroke-linecap="round"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Unit Context Banner -->
                        <div class="px-6 py-4 bg-zinc-50 border-b border-zinc-100 flex items-center gap-4">
                            @if($unit->mainImage)
                                <img src="{{ Storage::url($unit->mainImage->url) }}" class="h-10 w-14 rounded-lg object-cover shadow-sm" alt="">
                            @else
                                <div class="h-10 w-14 rounded-lg bg-zinc-200 animate-pulse"></div>
                            @endif
                            <div class="min-w-0">
                                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Inquiry for</p>
                                <p class="text-xs font-bold text-black truncate">{{ $unit->name }}</p>
                            </div>
                        </div>

                        <!-- Messages Area -->
                        <div 
                            x-ref="messageContainer"
                            class="flex-1 overflow-y-auto p-6 flex flex-col gap-4 bg-[radial-gradient(circle_at_1px_1px,rgba(0,0,0,0.01)_1px,transparent_0)] [background-size:24px_24px]"
                        >
                            <div class="text-center py-4">
                                <span class="text-[8px] font-black uppercase tracking-[0.3em] text-zinc-300">Conversation Secure</span>
                            </div>

                            @foreach($messages as $message)
                                <div class="flex {{ $message->is_from_admin ? 'justify-start' : 'justify-end' }}" wire:key="msg-{{ $message->id }}">
                                    <div class="max-w-[85%] group">
                                        <div class="px-4 py-3 rounded-2xl text-[13px] leading-relaxed {{ $message->is_from_admin ? 'bg-zinc-100 text-black rounded-bl-none' : 'bg-zinc-900 text-white rounded-br-none shadow-lg' }}">
                                            {{ $message->body }}
                                        </div>
                                        <p class="text-[8px] font-bold text-zinc-300 uppercase tracking-widest mt-1.5 {{ $message->is_from_admin ? 'text-left' : 'text-right' }}">
                                            {{ $message->created_at->format('H:i') }}
                                            @if(!$message->is_from_admin && $message->read_at)
                                                • Seen
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Input Area -->
                        <div class="p-6 bg-white border-t border-zinc-100">
                            <form wire:submit="sendMessage" class="relative group">
                                <textarea 
                                    wire:model="body"
                                    @keydown.enter.prevent="$wire.sendMessage()"
                                    placeholder="Ask about this unit..."
                                    rows="1"
                                    class="w-full bg-zinc-50 border-none rounded-2xl pl-6 pr-14 py-4 text-xs font-medium placeholder:text-zinc-400 focus:ring-2 focus:ring-black/5 focus:bg-white transition-all resize-none"
                                ></textarea>
                                <button 
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 h-10 w-10 rounded-xl bg-zinc-950 text-white flex items-center justify-center hover:scale-105 active:scale-95 transition-all shadow-lg cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <div wire:loading wire:target="sendMessage" class="h-4 w-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                                    <svg wire:loading.remove wire:target="sendMessage" viewBox="0 0 24 24" fill="none" class="h-4 w-4" stroke="currentColor" stroke-width="3"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </form>
                            <p class="text-[9px] text-zinc-300 font-bold mt-4 text-center uppercase tracking-widest">Premium Showroom Service</p>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @endteleport
</div>
