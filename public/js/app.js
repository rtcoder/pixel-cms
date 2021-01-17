function closeFlash(id) {
    const selector = `.flash-container .flash[data-flash-key="f-${id}"]`;
    document.querySelector(selector).remove();
}

function insertAfter(newNode, referenceNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

const getRandomString = (length = 10) => {
    let result = '';
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const charactersLength = characters.length;
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
};
const fetchFromApi = {}

function upload(file, listeners = {
    onError: (evt) => {
    },
    onAbort: (evt) => {
    },
    onLoad: (evt) => {
    },
    onProgress: (evt) => {
    }
}) {
    const onError = listeners.onError ? listeners.onError : (evt) => {
    };
    const onAbort = listeners.onAbort ? listeners.onAbort : (evt) => {
    };
    const onLoad = listeners.onLoad ? listeners.onLoad : (evt) => {
    };
    const onProgress = listeners.onProgress ? listeners.onProgress : (evt) => {
    };
    const xhr = new XMLHttpRequest();
    const token = document.querySelector('meta[name="api-token"]').getAttribute('content');

    xhr.open('POST', '/api/media/', true);
    xhr.setRequestHeader('Authorization', 'Bearer ' + token)
    xhr.setRequestHeader('Accept', 'application/json')
    xhr.responseType = 'json';

    xhr.addEventListener('error', onError);
    xhr.addEventListener('abort', onAbort);
    xhr.addEventListener('load', onLoad);

    if (xhr.upload) {
        xhr.upload.addEventListener('progress', onProgress);
    }

    const data = new FormData();
    data.append('file', file);
    xhr.send(data);

    return xhr;
}
