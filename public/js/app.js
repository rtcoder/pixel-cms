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
    return fetch(url, options).then(response => response.json());
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

/**
 * Format bytes as human-readable text.
 *
 * @param {number} bytes Number of bytes.
 * @param {boolean} si True to use metric (SI) units, aka powers of 1000. False to use
 *           binary (IEC), aka powers of 1024.
 * @param {number} dp Number of decimal places to display.
 *
 * @return {string} Formatted string.
 */
function humanFileSize(bytes, si = false, dp = 1) {
    const thresh = si ? 1000 : 1024;

    if (Math.abs(bytes) < thresh) {
        return bytes + ' B';
    }

    const units = si
        ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
        : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
    let u = -1;
    const r = 10 ** dp;

    do {
        bytes /= thresh;
        ++u;
    } while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1);


    return bytes.toFixed(dp) + ' ' + units[u];
}

/**
 *
 * @param {number} seconds
 * @returns {string}
 */
function secondsToHms(seconds) {
    seconds = Number(seconds);
    const h = Math.floor(seconds / 3600);
    const m = Math.floor(seconds % 3600 / 60);
    const s = Math.floor(seconds % 3600 % 60);

    const hDisplay = h > 0 ? h : '00';
    const mDisplay = m > 0 ? m : '00';
    const sDisplay = s > 0 ? s : '00';
    return `${hDisplay}:${mDisplay}:${sDisplay}`;
}
