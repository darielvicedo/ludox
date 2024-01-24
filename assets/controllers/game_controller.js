import {Controller} from '@hotwired/stimulus';

import Dropzone from "dropzone";

export default class extends Controller {
    static targets = [
        'image',
    ];

    async connect() {
        await this.bindImageDropzone();
    }

    async bindImageDropzone() {
        const controller = this;

        const container = document.getElementById("gameImageDropzone");
        if (container) {
            let dropzone = new Dropzone(container, {
                paramName: 'imageFile',
                acceptedFiles: 'image/*',
                maxFilesize: 1000000,
                dictDefaultMessage: "Haga clic o arrastre una imagen<br><i class='bi bi-cloud-arrow-up-fill'></i>",
            });

            dropzone.on('error', function (file, data) {
                if (data.detail) {
                    this.emit('error', file, data.detail);
                }
            });

            dropzone.on('success', function (file, response) {
                this.removeAllFiles();

                controller.imageTarget.src = response.fileName;
                controller.imageTarget.classList.remove('visually-hidden');
            });
        }
    }
}
