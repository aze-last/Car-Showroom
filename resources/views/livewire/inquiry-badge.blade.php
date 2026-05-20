<div>
    @if($count > 0)
        <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[8px] font-black text-white ring-2 ring-white">
            {{ $count > 9 ? '9+' : $count }}
        </span>
    @endif
</div>
