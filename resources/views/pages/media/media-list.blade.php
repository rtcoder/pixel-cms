@extends('layout.app')
@section('title', __('pages.documents'))

@section('content')

    <label for="files">
        <span class="add-btn">
            <span class="material-icons">add</span>
            @lang('common.add')
        </span>
        <input type="file" id="files" multiple>
    </label>

    <div class="media">
        @foreach($media as $item)
            <div class="item">
                <div class="img-container">
                    @switch(explode('/', $item->type)[0])
                        @case('image')
                        <img src="{{ $item->thumbnails_urls[0] }}" alt="{{ $item->filename }}">
                        @break
                        @case('video')
                        <img src="{{ $item->thumbnails_urls[0] }}" alt="{{ $item->filename }}">
                        @break
                        @case('application')
                        <span class="material-icons">insert_drive_file</span>
                        @break
                        @case('audio')
                        <img src="{{ $item->thumbnails_urls[0] }}" alt="{{ $item->filename }}">
                        @break

                    @endswitch
                </div>
                <p class="ellipsis" title="{{ $item->original_name }}">{{ $item->original_name }}</p>
                <p>{{ $item->readable_type }}</p>
            </div>
        @endforeach
    </div>

    @if(!count($media))
        @include('layout.table.no-data')
    @endif

    <style>
        .media {
            width: 100%;
            display: grid;
            grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .item {
            position: relative;
            width: 100%;
            border: 1px solid #ddd;
            background: #ededed;
            padding: 5px;
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: space-between;
        }

        .item .img-container {
            width: 100%;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .img-container .material-icons {
            font-size: 120px;
        }

        .item img {
            width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: brightness(0.9);
            transition: filter 0.2s ease-in;
        }

        .item img:hover {
            filter: brightness(1);
        }

        .item .layer {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            padding: 10px;
            flex-direction: column;
            justify-content: center;
        }

        .layer .progress {
            position: relative;
            width: 100%;
            height: 20px;
            border-radius: 20px;
            overflow: hidden;
            background: rgba(150, 150, 150, 0.4);
            border: 1px solid rgba(0, 131, 255, 0.69);
        }

        .layer .progress .bar {
            width: 0;
            height: 20px;
            background: #09c;
        }

        .layer .progress .val {
            position: absolute;
            color: #fff;
            z-index: 2;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            font-size: 13px;
            line-height: 20px;
        }

        .layer .abort {
            color: #fff;
            background: #dc0000;
            padding: 2px 15px;
            margin-top: 5px;
            border-radius: 20px;
            cursor: pointer;
        }

        label input {
            display: none;
        }

        .add-btn {
            padding: 3px;
            width: 120px;
        }
    </style>
    <script>
        const media = document.querySelector('.media');
        const input = document.querySelector('input#files');
        const requests = {};
        input.addEventListener('change', event => {
            console.log(input.files);
            [...input.files].forEach(file => {
                const index = `s${getRandomString()}`;
                const reader = new FileReader();
                reader.onload = function (e) {

                    let placeholder = '';
                    const type = file.type.split('/')[0];
                    switch (type) {
                        case 'image':
                            placeholder = `<img src="${e.target.result}">`;
                            break;
                        case 'video':
                            placeholder = `<span class="material-icons">movie</span>`;
                            break;
                        case 'application':
                            placeholder = `<span class="material-icons">insert_drive_file</span>`;
                            break;
                        case 'audio':
                            placeholder = `<span class="material-icons">music_note</span>`;
                            break;
                    }

                    const div = `
                    <div class="item" id="${index}">
                        <div class="img-container">${placeholder}</div>
                        <div class="layer">
                            <div class="progress">
                                <div class="bar"></div>
                                <div class="val">0%</div>
                            </div>
                            <div class="abort" onclick="requests['${index}'].abort()">anuluj</div>
                        </div>
                    </div>
                    `;
                    media.innerHTML = div + media.innerHTML
                    requests[index] = upload(file, {
                        onProgress: ev => {
                            const {loaded, total} = ev;
                            const value = parseFloat(String(((loaded * 100) / total))).toFixed(1);
                            const percent = `${value}%`;
                            document.querySelector(`.item#${index} .bar`).style.width = percent;
                            document.querySelector(`.item#${index} .val`).innerHTML = percent;

                            if (loaded === total) {
                                document.querySelector(`.item#${index} .val`).innerHTML = 'przetwarzanie';
                            }
                        },
                        onAbort: () => {
                            document.querySelector(`.item#${index}`).remove();
                        },
                        onLoad: () => {
                            document.querySelector(`.item#${index} .layer`).remove();
                            const result = requests[index].response;
                            const {
                                readable_type,
                                thumbnails_urls,
                                original_name
                            } = result;
                            let placeholder = '';
                            const type = result.type.split('/')[0];
                            switch (type) {
                                case 'image':
                                case 'video':
                                case 'audio':
                                    placeholder = `<img src="${thumbnails_urls[0]}">`;
                                    break;
                                case 'application':
                                    placeholder = `<span class="material-icons">insert_drive_file</span>`;
                                    break;
                            }
                            document.querySelector(`.item#${index} .img-container`).innerHTML = placeholder;
                            const itemDiv = document.querySelector(`.item#${index}`);
                            itemDiv.innerHTML = itemDiv.innerHTML + `
                            <p class="ellipsis" title="${original_name}">${original_name}</p>
                            <p>${readable_type}</p>
                            `;
                        }
                    })
                };

                reader.readAsDataURL(file);
            })
        });
    </script>






@endsection
