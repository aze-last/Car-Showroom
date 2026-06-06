<div class="flex h-[calc(100vh-160px)] gap-8 animate-showroom-fade-up">
    <!-- Threads Sidebar -->
    <div class="w-96 flex flex-col bg-white rounded-[40px] border border-zinc-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-zinc-50">
            <h2 class="text-xs font-black uppercase tracking-[0.4em] text-zinc-900">Conversations</h2>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            @forelse($threads as $thread)
                <button 
                    wire:click="selectThread({{ $thread['user']->id }}, {{ $thread['unit']->id }})"
                    class="w-full p-8 flex gap-4 transition-all border-b border-zinc-50 hover:bg-zinc-50 text-left {{ $selectedUserId == $thread['user']->id && $selectedUnitId == $thread['unit']->id ? 'bg-zinc-50' : '' }}"
                >
                    <div class="relative">
                        <div class="h-12 w-12 rounded-2xl bg-zinc-900 text-white flex items-center justify-center font-bold">
                            {{ substr($thread['user']->name, 0, 1) }}
                        </div>
                        @if($thread['unread_count'] > 0)
                            <span class="absolute -top-1 -right-1 h-5 w-5 bg-red-600 border-2 border-white rounded-full flex items-center justify-center text-[8px] font-bold text-white">
                                {{ $thread['unread_count'] }}
                            </span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-1">
                            <h3 class="text-sm font-bold text-black truncate">{{ $thread['user']->name }}</h3>
                            <span class="text-[9px] font-bold text-zinc-300 uppercase tracking-widest">{{ $thread['last_message']->created_at->diffForHumans(null, true) }}</span>
                        </div>
                        <p class="text-[10px] font-bold text-brand-primary uppercase tracking-widest truncate mb-2">{{ $thread['unit']->name }}</p>
                        <p class="text-xs text-zinc-400 truncate">{{ $thread['last_message']->body }}</p>
                    </div>
                </button>
            @empty
                <div class="p-12 text-center">
                    <p class="text-xs font-bold text-zinc-300 uppercase tracking-widest">No inquiries yet</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col bg-white rounded-[40px] border border-zinc-100 shadow-sm overflow-hidden relative">
        @if($selectedUserId && $selectedUnitId)
            @php
                $activeThread = $threads->where('user.id', $selectedUserId)->where('unit.id', $selectedUnitId)->first();
            @endphp
            <!-- Chat Header -->
            <div class="p-8 border-b border-zinc-50 flex justify-between items-center bg-zinc-50/50">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-zinc-900 text-white flex items-center justify-center font-bold">
                        {{ substr($activeThread['user']->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-black">{{ $activeThread['user']->name }}</h2>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Inquiry regarding: <span class="text-brand-primary">{{ $activeThread['unit']->name }}</span></p>
                    </div>
                </div>
                <a href="{{ route('units.show', $activeThread['unit']) }}" target="_blank" class="px-6 py-2 rounded-xl border border-zinc-100 text-[10px] font-bold uppercase tracking-widest hover:bg-zinc-900 hover:text-white transition-all">View Asset</a>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 overflow-y-auto p-10 flex flex-col gap-6" wire:poll.5s>
                @foreach($messages as $message)
                    <div class="flex {{ $message->is_from_admin ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[70%] group">
                            <div class="px-6 py-4 rounded-3xl text-sm leading-relaxed {{ $message->is_from_admin ? 'bg-zinc-900 text-white rounded-br-none' : 'bg-zinc-100 text-black rounded-bl-none' }}">
                                {{ $message->body }}
                                @if($message->is_automated)
                                    <span class="block mt-2 pt-2 border-t border-white/10 text-[9px] font-bold uppercase tracking-widest opacity-40">Auto-Response</span>
                                @endif
                            </div>
                            <p class="text-[10px] font-bold text-zinc-300 uppercase tracking-widest mt-2 {{ $message->is_from_admin ? 'text-right' : 'text-left' }}">
                                {{ $message->created_at->format('M d, H:i') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Reply Area -->
            <div class="p-8 border-t border-zinc-50 bg-white">
                <form wire:submit="sendReply" class="flex items-center gap-4 group">
                    <textarea 
                        wire:model="replyBody"
                        @keydown.ctrl.enter.prevent="$wire.sendReply()"
                        placeholder="Type your formal response... (Ctrl+Enter to send)"
                        rows="1"
                        class="flex-1 bg-zinc-50 border-none rounded-2xl px-8 py-4 text-sm font-medium focus:ring-2 focus:ring-black/5 focus:bg-white transition-all resize-none min-h-[56px] flex items-center"
                    ></textarea>
                    <button 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="h-[56px] w-[56px] bg-zinc-950 text-white flex items-center justify-center rounded-2xl hover:scale-105 active:scale-95 transition-all shadow-xl disabled:opacity-50 cursor-pointer"
                        title="Send Reply"
                    >
                        <div wire:loading wire:target="sendReply" class="h-5 w-5 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                        <svg wire:loading.remove wire:target="sendReply" viewBox="0 0 24 24" fill="none" class="h-5 w-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                </form>
            </div>
        @else
            <div class="flex-1 flex flex-col items-center justify-center opacity-20">
                <svg viewBox="0 0 24 24" fill="none" class="h-24 w-24 mb-6" stroke="currentColor" stroke-width="1"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <p class="text-xl font-black uppercase tracking-[0.5em]">Select a Thread</p>
            </div>
        @endif
    </div>
</div>
