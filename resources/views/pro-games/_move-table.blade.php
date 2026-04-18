<div class="grid grid-cols-3 text-xs font-bold text-gray-400 uppercase bg-white/5 border border-white/10 rounded-t-xl px-2 py-1.5">
    <div>#</div>
    <div>White</div>
    <div>Black</div>
</div>

<div style="max-height:500px; overflow-y:auto;" class="border border-t-0 border-white/10 rounded-b-xl bg-white/[0.02]">

    @php $moves = $proGame->moves_data; @endphp

    @for($r = 0; $r < ceil(count($moves) / 2); $r++)
        @php
            $wi     = $r * 2;
            $bi     = $r * 2 + 1;
            $wEntry = $moves[$wi] ?? null;
            $bEntry = $moves[$bi] ?? null;
            $rowBg  = $r % 2 === 0 ? 'bg-transparent' : 'bg-white/[0.02]';
        @endphp
        <div class="grid grid-cols-3 border-b border-white/5 {{ $rowBg }} text-sm">

            <div class="px-2 py-1 text-gray-500 font-mono text-xs flex items-start pt-2">{{ $r + 1 }}.</div>

            <div class="py-1 pr-1">
                @if($wEntry)
                    @php
                        $wName    = $wEntry['notation'] ?? $wEntry['move'] ?? '?';
                        $wScore   = $wEntry['score'] ?? null;
                        $wBrill   = $wEntry['is_brilliant'] ?? false;
                        $wBlunder = $wEntry['is_blunder'] ?? ($wEntry['isImportant'] ?? false);
                        $wClass   = $wBrill ? 'text-blue-400' : ($wBlunder ? 'text-red-400' : 'text-gray-200');
                    @endphp
                    <span class="move-btn {{ $wClass }}" data-ply="{{ $wi + 1 }}">{{ $wName }}</span>
                    @if($wScore !== null)
                        <span class="eval-badge">{{ $wScore > 0 ? '+' : '' }}{{ number_format($wScore, 1) }}</span>
                    @endif
                @endif
            </div>

            <div class="py-1 pr-1">
                @if($bEntry)
                    @php
                        $bName    = $bEntry['notation'] ?? $bEntry['move'] ?? '?';
                        $bScore   = $bEntry['score'] ?? null;
                        $bBrill   = $bEntry['is_brilliant'] ?? false;
                        $bBlunder = $bEntry['is_blunder'] ?? ($bEntry['isImportant'] ?? false);
                        $bClass   = $bBrill ? 'text-blue-400' : ($bBlunder ? 'text-red-400' : 'text-gray-200');
                    @endphp
                    <span class="move-btn {{ $bClass }}" data-ply="{{ $bi + 1 }}">{{ $bName }}</span>
                    @if($bScore !== null)
                        <span class="eval-badge">{{ $bScore > 0 ? '+' : '' }}{{ number_format($bScore, 1) }}</span>
                    @endif
                @endif
            </div>

        </div>
    @endfor

</div>
