<div class="flash-container">
    @foreach (session('flash_notification', collect())->toArray() as $key => $message)
        @if ($message['overlay'])
            @include('vendor.flash.modal', [
                'modalClass' => 'flash-modal',
                'title'      => $message['title'],
                'body'       => $message['message']
            ])
        @else
            <div class="flash alert alert-{{ $message['level'] }} {{ $message['important'] ? 'alert-important' : '' }}"
                 role="alert"
                 data-flash-key="f-{{ $key }}">
                {!! $message['message'] !!}

                <button type="button" class="close" onclick="closeFlash({{ $key }})">&times;</button>
            </div>
        @endif
    @endforeach

    {{ session()->forget('flash_notification') }}
</div>
