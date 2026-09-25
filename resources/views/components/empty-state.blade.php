@props(['title', 'action' => null, 'actionLabel' => null])
<div class="empty-state">
    <p>{{ $title }}</p>
    @if($action && $actionLabel)
        <a href="{{ $action }}" class="btn-ghost">{{ $actionLabel }}</a>
    @endif
</div>