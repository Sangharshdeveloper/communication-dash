@php
  $sideClass = $m['type'] === 'system' ? 'system' : ($m['mine'] ? 'mine' : 'theirs');
  $isAgent   = isset($m['sender_role']) && $m['sender_role'] === 'agent';
@endphp
<div class="msg {{ $sideClass }}" id="m-{{ $m['id'] }}" data-id="{{ $m['id'] }}" data-date="{{ $m['date'] }}">

  {{-- Sender name label (only on incoming messages, not system) --}}
  
  <div class="bubble">
    @if(!empty($m['body']))
      {!! nl2br(e($m['body'])) !!}
    @endif
    @foreach($m['attachments'] ?? [] as $att)
      @if($att['is_image'])
        <a href="{{ $att['url'] }}" target="_blank" rel="noopener">
          <img class="att-img" src="{{ $att['url'] }}" alt="{{ $att['name'] }}">
        </a>
      @else
        <a class="att" href="{{ $att['url'] }}" target="_blank" rel="noopener">
          <span class="ico">📄</span>
          <span class="name">{{ $att['name'] }}</span>
          <span class="sz">{{ $att['size'] }}</span>
        </a>
      @endif
    @endforeach
  </div>
  <div class="msg-meta">
    {{ $m['time'] }}
    @if($m['mine'])
      · <span class="tick {{ $m['is_read'] ? 'read' : '' }}">{{ $m['is_read'] ? '✓✓' : '✓' }}</span>
    @endif
  </div>
</div>