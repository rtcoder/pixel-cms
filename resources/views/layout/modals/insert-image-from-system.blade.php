<div id="ImageFromSystemDialog" class="modal">

    <div class="modal-content">
        <div class="modal-header">
            <h2>Modal Header</h2>
            <span class="close material-icons" onclick="ImageFromSystemDialogManager.close()">close</span>
        </div>
        <div class="modal-body">
            <div class="images-list"></div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="ImageFromSystemDialogManager.close()">Anuluj</button>
            <button type="button" onclick="ImageFromSystemDialogManager.add()">Wstaw</button>
        </div>
    </div>

</div>
<script>
    const ImageFromSystemDialog = document.getElementById("ImageFromSystemDialog");
    const ImageFromSystemDialogManager = ImageFromSystemDialogFn();

    function ImageFromSystemDialogFn() {
        return {
            open: () => {
                ImageFromSystemDialog.style.display = "block";
            },
            close: () => {
                ImageFromSystemDialog.style.display = 'none';
                ImageFromSystemDialog.querySelector('.images-list').innerHTML = '';
            },
            add: () => {
                this.close();
            },
            abort: () => {
                this.close();
            },
            selectImage: (image) => {
                editor.model.change(writer => {
                    const imageElement = writer.createElement('image', {
                        src: image.url
                    });

                    editor.model.insertContent(imageElement, editor.model.document.selection);
                });
            }
        }
    }
</script>
