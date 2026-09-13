@php
    // $rating can be an int (1-5) or a decimal average (e.g. 3.7)
    $full = floor($rating);
    $hasHalf = ($rating - $full) >= 0.25 && ($rating - $full) < 0.75;
    $roundedUp = ($rating - $full) >= 0.75;
    if ($roundedUp) { $full++; }
    $empty = 5 - $full - ($hasHalf ? 1 : 0);
@endphp

<span class="star-rating" title="{{ number_format($rating, 1) }} / 5">
    @for ($i = 0; $i < $full; $i++)
        <span class="star star-full">&#9733;</span>
    @endfor
    @if ($hasHalf)
        <span class="star star-half">
            <span class="star star-empty">&#9733;</span>
            <span class="star star-full star-half-inner">&#9733;</span>
        </span>
    @endif
    @for ($i = 0; $i < $empty; $i++)
        <span class="star star-empty">&#9733;</span>
    @endfor
</span>