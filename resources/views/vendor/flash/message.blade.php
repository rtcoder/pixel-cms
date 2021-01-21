<div class="flash-container">
    @foreach (session('flash_notification', collect())->toArray() as $key => $message)
            <div class="flash alert alert-{{ $message['level'] }}"
                 role="alert"
                 data-flash-key="f-{{ $key }}">
                {!! $message['message'] !!}

                <button type="button" class="close" onclick="closeFlash({{ $key }})">
                    <span class="material-icons">close</span>
                </button>
            </div>
    @endforeach

    {{ session()->forget('flash_notification') }}
</div>
