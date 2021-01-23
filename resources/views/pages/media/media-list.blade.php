@extends('layout.app')
@section('title', __('pages.documents'))


@section('content')
    <div id="media-app">
        <label for="files">
        <span class="add-btn">
            <span class="material-icons">add</span>
            @lang('common.add')
        </span>
            <input type="file" id="files" multiple
                   @change="uploadFiles" ref="fileInput">
        </label>

        <div class="upload" v-if="uploadItems.length">
            <table>
                <thead>
                <tr>
                    <th></th>
                    <th><span class="material-icons">backup</span></th>
                    <th><span class="material-icons">speed</span></th>
                    <th><span class="material-icons">query_builder</span></th>
                    <th>%</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="uploadItem in uploadItems" v-bind:id="uploadItem.index">
                    <td class="name">
                        <p class="ellipsis"
                           v-bind:title="uploadItem.name"
                        >@{{ uploadItem.name }}</p>
                    </td>
                    <td class="size">@{{ uploadItem.uploaded }} / @{{ uploadItem.size }}</td>
                    <td class="speed">@{{ uploadItem.speed }}</td>
                    <td class="eta">@{{ uploadItem.eta }}</td>
                    <td class="progress">@{{ uploadItem.progress }}</td>
                    <td class="actions">
                        <div class="abort" @click="abort(uploadItem.index)">
                            <span class="material-icons">clear</span>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="media">
            <div class="item" v-for="item in items">
                <div class="img-container" v-html="getItemIcon(item)"></div>
                <p class="ellipsis" v-bind:title="item.original_name">@{{ item.original_name }}</p>
                <p class="ellipsis">@{{ item.readable_type }}</p>

                <a v-bind:href="'media/' + item.id + '/delete'">
                    <span class="material-icons delete">delete</span>
                </a>
            </div>
        </div>

        <template v-if="!items.length">
            @include('layout.table.no-data')
        </template>
    </div>

@endsection
@section('scripts')
    <script>
        const Counter = {
            data() {
                return {
                    items: [],
                    uploadItems: [],
                    requests: {},
                    lastNow: {},
                    startedAt: {},
                    lastBytes: {},
                    progressIteration: {}
                }
            },
            mounted() {
                fetchFromApi(API_LINKS.mediaList)
                    .then(response => this.items = [...this.items, ...response])
            },
            methods: {
                getItemIcon(item) {
                    const type = item.type.split('/')[0];
                    const {thumbnails_urls, original_name} = item;
                    return type === 'application'
                        ? `<span class="material-icons">insert_drive_file</span>`
                        : `<img src="${thumbnails_urls[0]}" alt="${original_name}">`
                },

                abort(index) {
                    this.requests[index].abort();
                },

                uploadFiles() {
                    const files = this.$refs.fileInput.files;
                    console.log(files);

                    [...files].forEach(file => {
                        const index = `s${getRandomString()}`;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.startedAt[index] = new Date().getTime();
                            this.lastNow[index] = new Date().getTime();
                            this.lastBytes[index] = 0;
                            this.progressIteration[index] = 0;
                            this.addUploadRow(file, e, index);


                            this.requests[index] = upload(file, {
                                onProgress: ev => this.updateUploadRow(ev, index),
                                onAbort: () => this.removeUploadRow(index),
                                onLoad: () => {
                                    // removeUploadRow(index);
                                    const result = this.requests[index].response;
                                    if (result?.id) {
                                        this.items = [result, ...this.items];
                                    }
                                }
                            })
                        };

                        reader.readAsDataURL(file);
                    })
                },

                addUploadRow(file, event, index) {
                    this.uploadItems.push({
                        index,
                        name: file.name,
                        size: humanFileSize(file.size, true, 2),
                        uploaded: '',
                        speed: '',
                        eta: '',
                        progress: '',
                    });
                },

                updateUploadRow(event, index) {
                    const uploadItemIndex = this.uploadItems.findIndex(item => item.index === index);
                    if (uploadItemIndex === -1) {
                        return;
                    }
                    this.progressIteration[index]++;
                    const uploadItem = this.uploadItems[uploadItemIndex];
                    const {loaded, total} = event;

                    const value = ((loaded * 100) / total).toFixed(1);
                    const progress = `${value}%`;

                    const now = new Date().getTime();
                    const uploadedBytes = loaded - this.lastBytes[index];
                    const elapsed = (now - this.lastNow[index]) / 1000;
                    const bytesPerSecond = elapsed ? (uploadedBytes / elapsed) : 0;
                    const remainingBytes = total - loaded;
                    const secondsRemaining = elapsed ? remainingBytes / bytesPerSecond : 0;

                    if (this.progressIteration[index] % 5 === 0) {
                        this.lastBytes[index] = loaded;
                        this.lastNow[index] = now;
                    }

                    const data = {
                        uploaded: humanFileSize(loaded, true, 2),
                        speed: humanFileSize(bytesPerSecond, true) + '/s',
                        eta: secondsToHms(secondsRemaining),
                        progress,
                    }
                    if (loaded === total) {
                        data.speed = '';
                        data.eta = '';
                    }

                    this.uploadItems[uploadItemIndex] = Object.assign(uploadItem, data);
                },

                removeUploadRow(index) {
                    const uploadItemIndex = this.uploadItems.findIndex(item => item.index === index);
                    if (uploadItemIndex === -1) {
                        return;
                    }
                    this.uploadItems.splice(uploadItemIndex, 1);

                    delete this.lastBytes[index];
                    delete this.lastNow[index];
                    delete this.requests[index];
                }
            }
        }

        Vue.createApp(Counter).mount('#media-app');
    </script>
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ url('css/media-page.css') }}">
@endsection
