<?php

$fieldName = $fieldName ?? 'content';

?>


<div class="centered" id="ckeditor-container">
    <div class="row" hidden>
        <button type="button" onclick="openImageFromSystemDialog()" id="imageFromSystemButton" title="Wstaw zdjęcie z systemu">
            <span class="material-icons">collections</span>
        </button>
    </div>
    <textarea name="description" id="content" hidden></textarea>
    <div class="row">
        <div class="document-editor__toolbar"></div>
    </div>
    <div class="row row-editor">
        <div class="editor"></div>
    </div>
</div>
@include('layout.modals.insert-image-from-system')
<link rel="stylesheet" href="{{ url('ckeditor/styles.css') }}">
<script src="{{ url('ckeditor/ckeditor.js') }}"></script>
<script>
    function openImageFromSystemDialog() {
        const modal = document.getElementById("ImageFromSystemDialog");
        modal.style.display = "block";
    }

    class CkeditorCustomUploadAdapter {
        constructor(loader) {
            this.loader = loader;
            this.url = '/api/media/';
        }

        upload() {
            return this.loader.file
                .then((file) => new Promise((resolve, reject) => {
                    this._initRequest();
                    this._initListeners(resolve, reject);
                    this._sendRequest(file);
                }));
        }

        abort() {
            if (this.xhr) {
                this.xhr.abort();
            }
        }

        _initRequest() {
            const xhr = this.xhr = new XMLHttpRequest();

            xhr.open('POST', this.url, true);
            xhr.setRequestHeader('Authorization', 'Bearer {{ auth()->user()->api_token }}')
            xhr.setRequestHeader('Accept', 'application/json')
            xhr.responseType = 'json';
        }

        _initListeners(resolve, reject) {
            const xhr = this.xhr;
            const loader = this.loader;
            const genericErrorText = 'Couldn\'t upload file:' + ` ${loader.file.name}.`;

            xhr.addEventListener('error', () => reject(genericErrorText));
            xhr.addEventListener('abort', () => reject());
            xhr.addEventListener('load', () => {
                const response = xhr.response;

                if (!response || response.error) {
                    return reject(response && response.error ? response.error.message : genericErrorText);
                }
                resolve({default: response.url});
            });

            if (xhr.upload) {
                xhr.upload.addEventListener('progress', evt => {
                    if (evt.lengthComputable) {
                        loader.uploadTotal = evt.total;
                        loader.uploaded = evt.loaded;
                    }
                });
            }
        }

        // Prepares the data and sends the request.
        _sendRequest(file) {
            const data = new FormData();
            data.append('file', file);
            this.xhr.send(data);
        }
    }

    function uploadPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new CkeditorCustomUploadAdapter(loader);
        };
    }

    DecoupledDocumentEditor.create(document.querySelector('.editor'), {
        extraPlugins: [uploadPlugin],
        toolbar: {
            items: [
                'heading', '|',
                'fontSize', 'fontFamily', '|',
                'bold', 'italic', 'underline', 'strikethrough', 'removeFormat', 'highlight', '|',
                'fontColor', 'fontBackgroundColor', '|',
                'alignment', '|',
                'todoList', 'numberedList', 'bulletedList', '|',
                'indent', 'outdent', '|',
                'link', 'blockQuote', 'imageInsert', 'imageUpload', 'insertTable', 'mediaEmbed', '|',
                'codeBlock', 'htmlEmbed', 'code', '|',
                'undo', 'redo', '|',
                'specialCharacters', 'subscript', 'superscript', 'pageBreak'
            ],
            shouldNotGroupWhenFull: true
        },
        language: 'pl',
        image: {
            toolbar: [
                'imageTextAlternative',
                'imageStyle:full',
                'imageStyle:side',
                'linkImage'
            ]
        },
        table: {
            contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells',
                'tableCellProperties',
                'tableProperties'
            ]
        },
        licenseKey: '',

    }).then(editor => {
        editor.model.document.on('change:data', () => {
            document.querySelector('textarea#content').value = editor.getData();
        });
        window.editor = editor;

        // Set a custom container for the toolbar.
        document.querySelector('.document-editor__toolbar').appendChild(editor.ui.view.toolbar.element);
        document.querySelector('.ck-toolbar').classList.add('ck-reset_all');


        const imageFromSystemButton = document.querySelector('#imageFromSystemButton');
        const toolbar = document.querySelector('.ck.ck-toolbar__items');
        toolbar.appendChild(imageFromSystemButton)
    }).catch(error => {
        console.error('Oops, something went wrong!');
        console.error('Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:');
        console.warn('Build id: 6pz4a31oll3-cr3lxj20pv8v');
        console.error(error);
    });
</script>
