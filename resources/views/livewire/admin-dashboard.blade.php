@php
    use App\Models\UnitStatusLog;
    use App\Models\Inquiry;
@endphp

<div class="space-y-12 animate-showroom-fade-up">
    <!-- Top Header Area -->  
    <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-stack-lg">
        <div>
            <h2 class="text-5xl font-bold tracking-tighter text-black mb-2">Admin Overview</h2>   
            <p class="text-sm font-medium text-zinc-400 leading-snug">Elite operational metrics for {{ now()->format('M d, Y') }}</p>        
        </div>
        <div class="hidden md:flex gap-3">
            <button class="px-6 py-3 border border-gallery-outline/30 rounded-2xl font-bold text-[11px] uppercase tracking-widest text-black hover:bg-gallery-surface-low transition-colors duration-300">Generate Audit Report</button>
        </div>
    </header>

    <!-- KPI Cards Grid -->   
    <section class="grid grid-cols-1 md:grid-cols-4 gap-8">      
        <!-- KPI 1: Portfolio Value -->
        <div class="bg-white rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift relative overflow-hidden group">  
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                <svg viewBox="0 0 24 24" fill="none" class="h-20 w-20 text-black" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M12 8v8M8 12h8"/></svg>
            </div>
            <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-4">Portfolio Value</h3>
            <div class="text-5xl font-bold text-black tracking-tighter mb-2">₱{{ number_format($portfolioValue / 1000000, 1) }}M</div>    
            <div class="flex items-center gap-2">
                <span class="bg-emerald-50 text-emerald-600 font-bold text-[9px] px-2 py-1 rounded-full flex items-center gap-1 uppercase tracking-widest">
                    Available
                </span>
                @if($salesTrend != 0)
                    <span class="text-[9px] font-bold {{ $salesTrend > 0 ? 'text-emerald-500' : 'text-red-500' }} uppercase tracking-widest">
                        {{ $salesTrend > 0 ? '↑' : '↓' }} {{ abs(round($salesTrend)) }}% vs last month
                    </span>
                @endif
            </div>
        </div>

        <!-- KPI 2: Active Pipeline -->
        <div class="bg-white rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift relative overflow-hidden group">  
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                <svg viewBox="0 0 24 24" fill="none" class="h-20 w-20 text-black" stroke="currentColor" stroke-width="2.5"><path d="M21 15V19A2 2 0 0 1 19 21H5A2 2 0 0 1 3 19V15M7 10L12 15L17 10M12 15V3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-4">Unread Chats</h3>
            <div class="text-5xl font-bold text-black tracking-tighter mb-2">{{ $activeInquiriesCount }}</div>      
            <div class="flex items-center gap-2">
                <span class="bg-black text-white font-bold text-[9px] px-2 py-1 rounded-full flex items-center gap-1 uppercase tracking-widest">
                    Messaging
                </span>
            </div>
        </div>

        <!-- KPI 3: Auction Health -->
        <div class="bg-white rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift relative overflow-hidden group">  
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                <svg viewBox="0 0 24 24" fill="none" class="h-20 w-20 text-black" stroke="currentColor" stroke-width="2.5"><path d="M12 8V12L15 15M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-4">Auction Room</h3>
            <div class="text-5xl font-bold text-black tracking-tighter mb-2">{{ $activeAuctionsCount }}</div>       
            <div class="flex items-center gap-2">
                <span class="bg-red-50 text-red-600 font-bold text-[9px] px-2 py-1 rounded-full flex items-center gap-1 uppercase tracking-widest">
                    Live Now
                </span>
            </div>
        </div>

        <!-- KPI 4: Fleet Size -->
        <div class="bg-white rounded-[32px] p-8 border border-gallery-outline/20 ambient-shadow hover-lift relative overflow-hidden group">  
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                <svg viewBox="0 0 24 24" fill="none" class="h-20 w-20 text-black" stroke="currentColor" stroke-width="2.5"><path d="M4 8.5L12 4L20 8.5V15.5L12 20L4 15.5V8.5Z" stroke-linejoin="round"/></svg>
            </div>
            <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.4em] mb-4">Fleet Size</h3>
            <div class="text-5xl font-bold text-black tracking-tighter mb-2">{{ $totalUnits }}</div>       
            <div class="flex items-center gap-2">
                <span class="bg-zinc-100 text-zinc-900 font-bold text-[9px] px-2 py-1 rounded-full flex items-center gap-1 uppercase tracking-widest">
                    Total Units
                </span>
                @if($unitTrend != 0)
                    <span class="text-[9px] font-bold {{ $unitTrend > 0 ? 'text-emerald-500' : 'text-red-500' }} uppercase tracking-widest">
                        {{ $unitTrend > 0 ? '↑' : '↓' }} {{ abs(round($unitTrend)) }}% acquisition
                    </span>
                @endif
            </div>
        </div>
    </section>

    <!-- Main Data Grid (Chart & Timeline) -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-8">     
        <!-- Chart Area: Acquisition Strategy -->
        <div class="lg:col-span-8 bg-white rounded-[40px] p-10 border border-gallery-outline/20 ambient-shadow flex flex-col min-h-[500px]">
            <div class="flex justify-between items-center mb-12">
                <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em]">Portfolio Velocity</h3>
                <div class="flex gap-2">  
                    <span class="text-[10px] font-bold text-zinc-400 px-4 py-1.5 bg-gallery-surface-low rounded-full cursor-pointer hover:text-black transition-colors uppercase tracking-widest">Weekly</span>
                    <span class="text-[10px] font-bold text-black px-4 py-1.5 bg-gallery-surface-high rounded-full cursor-pointer uppercase tracking-widest">Monthly</span>
                </div>
            </div>
            
            <div class="flex-grow relative w-full flex items-end pt-10 px-6">
                <!-- SVG Line/Area (Dynamic representation of Sales Velocity) -->    
                <div class="relative w-full h-[300px] z-10">      
                    <svg class="w-full h-full overflow-visible" preserveAspectRatio="none" viewBox="0 0 1000 300">
                        <defs>
                            <linearGradient id="areaGradient" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#000000" stop-opacity="0.05"></stop>
                                <stop offset="100%" stop-color="#000000" stop-opacity="0"></stop>
                            </linearGradient>
                        </defs>
                        <!-- Area -->
                        <path d="{{ $chartPath }} L 1000,300 L 0,300 Z" fill="url(#areaGradient)"></path>
                        <!-- Line -->
                        <path d="{{ $chartPath }}" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"></path>     
                        
                        <!-- Dynamic Pulse Points -->
                        @foreach($velocityData as $index => $data)
                            @php
                                $maxCount = collect($velocityData)->max('count') ?: 1;
                                $x = $index * (1000 / 5);
                                $y = 250 - ($data['count'] / $maxCount * 200);
                            @endphp
                            <g class="group/point">
                                <circle cx="{{ $x }}" cy="{{ $y }}" r="6" fill="#ffffff" stroke="#000000" stroke-width="3" class="transition-all duration-300 group-hover/point:r-8"></circle>
                                <text x="{{ $x }}" y="{{ $y - 15 }}" text-anchor="middle" class="opacity-0 group-hover/point:opacity-100 transition-opacity duration-300 text-[10px] font-bold fill-black">{{ $data['count'] }}</text>
                            </g>
                        @endforeach
                    </svg>
                </div>
                
                <!-- X-Axis Labels (Dynamic) -->
                <div class="absolute bottom-[-20px] left-6 right-6 flex justify-between text-[10px] font-bold text-zinc-300 uppercase tracking-widest">
                    @foreach($velocityData as $data)
                        <span>{{ $data['label'] }}</span>
                    @endforeach
                </div>
            </div>
            <div class="mt-16 pt-8 border-t border-gallery-outline/5 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full bg-black"></div>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Catalog Expansion</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full bg-zinc-200"></div>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Private Acquisition</span>
                    </div>
                </div>
                <div class="text-[10px] font-bold text-black uppercase tracking-widest italic opacity-60">Verified Analytics Hub</div>
            </div>
        </div>

        <!-- Audit Trail (Spans 4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-[40px] p-10 border border-gallery-outline/20 ambient-shadow flex flex-col h-[500px] lg:h-auto">      
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em]">Curator Audit</h3>
                <a href="{{ route('admin.logs.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-zinc-300 hover:text-black transition-colors" stroke="currentColor" stroke-width="2.5"><path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm0 7a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" stroke-linecap="round"/></svg>
                </a>
            </div>
            <div class="flex-grow overflow-y-auto pr-4 relative">
                <!-- Vertical Line -->    
                <div class="absolute left-[11px] top-2 bottom-2 w-[2px] bg-gallery-surface-low"></div>
                <ul class="flex flex-col gap-10 relative z-10">      
                    @foreach($recentLogs as $log)
                        <li class="flex gap-6 group">
                            <div class="w-6 h-6 rounded-full {{ $log->action === UnitStatusLog::ACTION_SET_AVAILABLE ? 'bg-emerald-500' : 'bg-black' }} flex items-center justify-center shrink-0 border-4 border-white mt-1 shadow-sm">
                                <svg viewBox="0 0 24 24" fill="none" class="h-2.5 w-2.5 text-white" stroke="currentColor" stroke-width="4">
                                    <path d="{{ $log->action === UnitStatusLog::ACTION_SET_AVAILABLE ? 'M20 6L9 17L4 12' : 'M6 18L18 6M6 6l12 12' }}" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[13px] font-bold text-black mb-1 tracking-tight">{{ $log->unit?->name ?? 'System Event' }}</p>
                                <p class="text-[11px] font-medium text-zinc-500 leading-snug">Status transitioned to <strong class="text-black">{{ $log->action }}</strong></p>
                                <p class="text-[9px] font-bold text-zinc-300 uppercase tracking-widest mt-2">{{ $log->created_at?->diffForHumans() }} â€¢ {{ $log->user?->name ?? 'Curator' }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <a href="{{ route('admin.logs.index') }}" class="mt-12 w-full h-12 rounded-full border border-gallery-outline/30 flex items-center justify-center text-[10px] font-bold text-black uppercase tracking-widest hover:bg-gallery-surface-low transition-all">
                Full Integrity Log
            </a>
        </div>
    </section>

    <!-- Recent Messaging Pipeline -->
    <section class="bg-white rounded-[40px] p-10 border border-gallery-outline/20 ambient-shadow overflow-hidden">        
        <div class="flex justify-between items-center mb-10">
            <h3 class="text-[12px] font-bold text-black uppercase tracking-[0.4em]">Live Chat Pipeline</h3>
            <a href="{{ route('admin.messages') }}" class="text-[10px] font-bold text-black border-b-2 border-black pb-1 hover:opacity-60 transition-all uppercase tracking-widest">Open Message Center</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">    
                <thead>
                    <tr class="border-b border-gallery-outline/10 text-zinc-400">        
                        <th class="py-6 px-4 font-bold text-[10px] uppercase tracking-widest">Collector</th>
                        <th class="py-6 px-4 font-bold text-[10px] uppercase tracking-widest">Unit Context</th>
                        <th class="py-6 px-4 font-bold text-[10px] uppercase tracking-widest">Last Message</th>
                        <th class="py-6 px-4 font-bold text-[10px] uppercase tracking-widest">Received</th>
                        <th class="py-6 px-4 font-bold text-[10px] uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-[14px] text-black">
                    @foreach($recentInquiries as $msg)
                        <tr class="border-b border-gallery-outline/5 hover:bg-gallery-surface-low transition-colors duration-200 group">      
                            <td class="py-6 px-4 font-bold">{{ $msg->user->name }}</td>   
                            <td class="py-6 px-4 text-zinc-500 font-medium">{{ $msg->unit?->name ?? 'General' }}</td>   
                            <td class="py-6 px-4">    
                                <p class="text-xs text-zinc-400 truncate max-w-xs">{{ $msg->body }}</p>
                            </td>
                            <td class="py-6 px-4 text-zinc-400 font-medium">{{ $msg->created_at?->diffForHumans() }}</td>
                            <td class="py-6 px-4 text-right">
                                <a href="{{ route('admin.messages', ['user_id' => $msg->user_id, 'unit_id' => $msg->unit_id]) }}" class="text-zinc-300 group-hover:text-black transition-colors">
                                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 inline" stroke="currentColor" stroke-width="2.5"><path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
