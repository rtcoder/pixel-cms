@extends('layout.app')
@section('title', __('pages.documents'))


@section('content')
    <div id="media-app">
        <div class="content-wrapper" ref="mediaArea">
            <div class="drag-on-layer"
                 data-text="@lang('upload.drop_here')"
                 ref="dropArea"
            ></div>


            <div class="top">
                <div>
                    <div class="group-actions" v-if="isAnySelected()">
                        <span class="material-icons" @click="deleteMany">delete</span>
                        <button type="button" @click="selectAll">@lang('common.select_all')</button>
                    </div>
                </div>

                <label for="files">
                <span class="add-btn">
                    <span class="material-icons">add</span>
                    @lang('common.add')
                </span>
                    <input type="file" id="files" multiple
                           @change="handleFilesFromInput" ref="fileInput">
                </label>
            </div>


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
                    <tr v-for="(uploadItem, index) in uploadItems" :id="uploadItem.index">
                        <td class="name">
                            <p class="ellipsis"
                               :title="uploadItem.name"
                            >@{{ index + 1 }}. @{{ uploadItem.name }}</p>
                        </td>
                        <td class="size">@{{ uploadItem.uploaded }} / @{{ uploadItem.size }}</td>
                        <td class="speed">@{{ uploadItem.speed }}</td>
                        <td class="eta">@{{ uploadItem.eta }}</td>
                        <td class="progress">
                            <template v-if="uploadItem.status === 0">
                                @lang('upload.pending')
                            </template>
                            <template v-if="uploadItem.status === 1">
                                @{{ uploadItem.progress }}
                            </template>
                            <template v-if="uploadItem.status === 2">
                                @lang('upload.processing')
                            </template>
                            <template v-if="uploadItem.status === 3">
                                @lang('upload.done')
                            </template>
                            <template v-if="uploadItem.status === 4">
                                @lang('upload.error')
                            </template>

                        </td>
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
                <div class="item" v-for="item in items"
                     :class="{selected: item.selected}"
                >
                    <div class="img-container" v-html="getItemIcon(item)"></div>
                    <p class="ellipsis" :title="item.original_name">@{{ item.original_name }}</p>
                    <p class="ellipsis">@{{ item.readable_type }}</p>

                    <input type="checkbox" :checked="item.selected"
                           v-model="item.selected">
                    <span class="material-icons delete" @click="deleteOne(item.id)">delete</span>
                </div>
            </div>

            <template v-if="!items.length">
                @include('layout.table.no-data')
            </template>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        const MediaListPage = {
            data() {
                return {
                    isLoading: false,
                    mouseOverMediaContainer: false,
                    uploadStatuses: {
                        pending: 0,
                        uploading: 1,
                        processing: 2,
                        done: 3,
                        error: 4
                    },
                    maxFilesUploadAtOnce: 2,
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
                    .then(response => this.items = [...this.items, ...response]);


                ['dragenter', 'dragover'].forEach(eventName => {
                    window.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        let mouseOverMedia = false;
                        (e.path || []).forEach(node => {
                            if (node.id === 'media-app') {
                                mouseOverMedia = true;
                            }
                        });
                        if (mouseOverMedia) {
                            this.$refs.mediaArea.classList.add('drag-on');
                        } else {
                            this.$refs.mediaArea.classList.remove('drag-on');
                        }
                    }, false);
                });

                window.addEventListener('drop', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.$refs.mediaArea.classList.remove('drag-on');
                }, false);
                this.$refs.mediaArea.addEventListener('drop', (e) => {
                    this.$refs.mediaArea.classList.remove('drag-on');
                    getDroppedOrSelectedFiles(e).then(files => {
                        this.uploadFiles(files.map(file => file.fileObject));
                    });
                }, false);
            },
            methods: {
                deleteMany() {
                    if (this.isLoading) {
                        return;
                    }
                    const ids = this.items.filter(item => item.selected).map(item => item.id);
                    const url = `${API_LINKS.mediaDeleteMany}?ids=${ids.join(',')}`;
                    this.isLoading = true;
                    fetchFromApi(url, {method: 'DELETE'})
                        .then(() => {
                            this.items = this.items.filter(item => !ids.includes(item.id));
                            this.isLoading = false;
                        });
                },
                deleteOne(id) {
                    console.log('delete', this.isLoading);
                    if (this.isLoading) {
                        return;
                    }
                    const url = `${API_LINKS.mediaList}/${id}`;
                    this.isLoading = true;
                    fetchFromApi(url, {method: 'DELETE'})
                        .then(() => {
                            this.items = this.items.filter(item => id !== item.id);
                            this.isLoading = false;
                        });
                },

                isAnySelected() {
                    return this.items.some(item => item.selected);
                },

                selectAll() {
                    if (this.items.every(item => item.selected)) {
                        this.items.forEach(item => item.selected = false);
                    } else
                        this.items.forEach(item => item.selected = true)
                },

                getItemIcon(item) {
                    const type = item.type.split('/')[0];
                    const {thumbnails_urls, original_name} = item;
                    return type === 'application'
                        ? `<span class="material-icons">insert_drive_file</span>`
                        : `<img src="${thumbnails_urls[0]}" alt="${original_name}">`
                },

                abort(index) {
                    this.requests[index]?.abort();
                    this.removeUploadRow(index)
                },

                handleFilesFromInput() {
                    const files = this.$refs.fileInput.files;
                    this.uploadFiles(files);
                },

                uploadFiles(files) {
                    [...files].forEach(file => {
                        const index = `s${getRandomString()}`;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.startedAt[index] = new Date().getTime();
                            this.lastNow[index] = new Date().getTime();
                            this.lastBytes[index] = 0;
                            this.progressIteration[index] = 0;
                            this.addUploadRow(file, e, index);
                        };

                        reader.readAsDataURL(file);
                    });
                },

                runUpload(index) {
                    const uploadItemIndex = this.uploadItems.findIndex(item => item.index === index);
                    if (uploadItemIndex === -1) {
                        return;
                    }

                    this.uploadItems[uploadItemIndex].status = this.uploadStatuses.uploading;
                    const uploadItem = this.uploadItems[uploadItemIndex];

                    this.requests[index] = upload(uploadItem.file, {
                        onProgress: ev => this.updateUploadRow(ev, index),
                        onAbort: () => this.removeUploadRow(index),
                        onLoad: () => {
                            const uploadItemIndex = this.uploadItems.findIndex(item => item.index === index);
                            if (uploadItemIndex === -1) {
                                return;
                            }
                            const uploadItem = {...this.uploadItems[uploadItemIndex]};

                            const result = this.requests[index].response;
                            if (result?.id) {
                                this.items = [result, ...this.items];
                                uploadItem.status = this.uploadStatuses.done;
                                setTimeout(() => this.removeUploadRow(index), 3000);
                            } else {
                                uploadItem.status = this.uploadStatuses.error;
                            }
                            this.uploadItems.splice(uploadItemIndex, 1);
                            this.uploadItems.push(uploadItem);

                            this.triggerRunUpload();
                        }
                    });
                },
                triggerRunUpload() {
                    const countUploaded = this.uploadItems.filter(item => {
                        return [
                            this.uploadStatuses.processing,
                            this.uploadStatuses.uploading,
                        ].includes(item.status);
                    }).length;

                    if (countUploaded < this.maxFilesUploadAtOnce) {
                        const item = this.uploadItems.find(item => item.status === this.uploadStatuses.pending);
                        if (item) {
                            this.runUpload(item.index);
                        }
                    }
                },

                addUploadRow(file, event, index) {
                    this.uploadItems.push({
                        index,
                        name: file.name,
                        size: humanFileSize(file.size, true, 2),
                        uploaded: '0 B',
                        speed: '',
                        eta: '',
                        progress: '',
                        status: this.uploadStatuses.pending,
                        file
                    });
                    this.triggerRunUpload();
                },

                updateUploadRow(event, index) {
                    const uploadItemIndex = this.uploadItems.findIndex(item => item.index === index);
                    if (uploadItemIndex === -1) {
                        return;
                    }
                    this.progressIteration[index]++;
                    const uploadItem = this.uploadItems[uploadItemIndex];
                    const {loaded, total} = event;

                    const value = ((loaded * 100) / total).toFixed(0);
                    const progress = `${value}%`;

                    const now = new Date().getTime();
                    const uploadedBytes = loaded - this.lastBytes[index];
                    const elapsed = (now - this.lastNow[index]) / 1000;
                    const bytesPerSecond = elapsed ? (uploadedBytes / elapsed).toFixed(0) : 0;
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
                        data.status = this.uploadStatuses.processing;
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

        Vue.createApp(MediaListPage).mount('#media-app');
    </script>
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ url('css/media-page.css') }}">
@endsection
