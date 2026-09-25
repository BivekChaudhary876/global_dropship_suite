{{-- Usage: <x-status-badge :status="$order->status" /> --}}
<span class="status-badge" style="color: {{ $status->color() }}; background: {{ $status->backgroundColor() }};">
    {{ $status->label() }}
</span>