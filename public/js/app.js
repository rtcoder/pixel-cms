function closeFlash(id) {
    const selector = `.flash-container .flash[data-flash-key="f-${id}"]`;
    document.querySelector(selector).remove();
}
