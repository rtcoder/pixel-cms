/**
 *
 * @param {string} id
 */
function closeFlash(id) {
    const selector = `.flash-container .flash[data-flash-key="f-${id}"]`;
    document.querySelector(selector).remove();
}

function insertAfter(newNode, referenceNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

/**
 *
 * @param {number} [length]
 * @returns {string}
 */
const getRandomString = (length = 10) => {
    let result = '';
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const charactersLength = characters.length;
    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
};
/**
 *
 * @param {string} url
 * @param {RequestInit} [options]
 * @returns {Promise<Response>}
 */
const fetchFromApi = (url, options) => {
    options = {...options};
    const token = document.querySelector('meta[name="api-token"]')
        ?.getAttribute('content');
    if (token) {
        options.headers = {
            ...options.headers,
            Authorization: `Bearer ${token}`,
        };
    }
    return fetch(url, options);
}

/**
 *
 * @param {File} file
 * @param {Object} [listeners]
 * @param {Function=} listeners.onError
 * @param {Function=} listeners.onAbort
 * @param {Function=} listeners.onLoad
 * @param {Function=} listeners.onLoadStart
 * @param {Function=} listeners.onLoadEnd
 * @param {Function=} listeners.onProgress
 * @param {Function=} listeners.onTimeout
 * @returns {XMLHttpRequest}
 */
function upload(file, listeners) {
    const xhr = new XMLHttpRequest();
    const token = document.querySelector('meta[name="api-token"]').getAttribute('content');

    xhr.open('POST', '/api/media/', true);
    xhr.setRequestHeader('Authorization', 'Bearer ' + token)
    xhr.setRequestHeader('Accept', 'application/json')
    xhr.responseType = 'json';

    if (listeners.onError) {
        xhr.addEventListener('error', listeners.onError);
    }

    if (listeners.onAbort) {
        xhr.addEventListener('abort', listeners.onAbort);
    }

    if (listeners.onLoad) {
        xhr.addEventListener('load', listeners.onLoad);
    }

    if (listeners.onLoadStart) {
        xhr.addEventListener('loadstart', listeners.onLoadStart);
    }

    if (listeners.onLoadEnd) {
        xhr.addEventListener('loadend', listeners.onLoadEnd);
    }

    if (listeners.onTimeout) {
        xhr.addEventListener('timeout', listeners.onTimeout);
    }

    if (xhr.upload && listeners.onProgress) {
        xhr.upload.addEventListener('progress', listeners.onProgress);
    }

    const data = new FormData();
    data.append('file', file);
    xhr.send(data);

    return xhr;
}
