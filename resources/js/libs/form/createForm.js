import UppyVideo from "./uppyVideo.js";
import { createEditor } from "../editor.js";
import TusVideo from "../tus/tusVideo.js";

class CreateForm {
    constructor({
        form,
        tusConfig,
        dropAreaIdMain,
        dropAreaIdPreview,
        publishButton,
        editorHolderId,
        editorPlaceholder,
        editorData,
        uploadMaxSize,
    }) {
        this.form = form;
        this.editor = null;
        this.editorHolderId = editorHolderId;
        this.editorPlaceholder = editorPlaceholder;
        this.editorData = editorData || {};
        this.tusConfig = tusConfig;
        this.uploadMaxSize = uploadMaxSize ?? 2000000000;
        this.uppyMainVideoTarget = null;
        this.uppyPreviewVideoTarget = null;
        this.saveButton = document.getElementById("save-button");
        try {
            this.uppyMainVideoTarget = document.querySelector(dropAreaIdMain);
        } catch (ex) { }
        try {
            this.uppyPreviewVideoTarget = document.querySelector(dropAreaIdPreview);
        } catch (ex) { }
        if (this.mainVideoIsValid()) {
            this.uppyMainVideo = UppyVideo({
                target: dropAreaIdMain,
                maxFileSize: this.uploadMaxSize,
                tusToken: tusConfig.tusToken,
                tusEndpoint: tusConfig.tusEndpoint
            });
            this.uppyMainVideo.on('file-added', (file) => {
                const video = document.createElement("video");
                video.preload = "metadata";
                video.addEventListener("loadedmetadata", ev => {
                    try {
                        document.getElementById("video_width").value = video.videoWidth;
                    } catch (ex) { }
                    try {
                        document.getElementById("video_height").value = video.videoHeight;
                    } catch (ex) { }
                    try {
                        document.getElementById("video_duration").value = video.duration;
                    } catch (ex) { }
                });
                video.src = URL.createObjectURL(file.data);
            });
        }
        if (this.previewVideoIsValid()) {
            this.uppyPreviewVideo = UppyVideo({
                target: dropAreaIdPreview,
                maxFileSize: this.uploadMaxSize,
                tusToken: tusConfig.tusToken,
                tusEndpoint: tusConfig.tusEndpoint
            });
            this.uppyPreviewVideo.on('file-added', (file) => {
                const video = document.createElement("video");
                video.preload = "metadata";
                video.addEventListener("loadedmetadata", ev => {
                    try {
                        document.getElementById("video_preview_width").value = video.videoWidth;
                    } catch (ex) { }
                    try {
                        document.getElementById("video_preview_height").value = video.videoHeight;
                    } catch (ex) { }
                    try {
                        document.getElementById("video_preview_duration").value = video.duration;
                    } catch (ex) { }
                });
                video.src = URL.createObjectURL(file.data);
            });
        }
        this.publishButton = publishButton;
    }

    mainVideoIsValid() {
        return this.uppyMainVideoTarget !== null;
    }

    previewVideoIsValid() {
        return this.uppyPreviewVideoTarget !== null;
    }

    async upload(uppyObject) {
        const result = await uppyObject.upload();
        //console.info('Successful uploads:', result)
        if (result.failed.length > 0) {
            console.error('Errors:');
            result.failed.forEach((file) => {
                console.error(file.error);
            });
            return Promise.reject(result.failed);
        } else {
            return Promise.resolve(result.successful);
        }
    }

    async uploadPreviewVideo() {
        return this.upload(this.uppyPreviewVideo);
    }

    async uploadMainVideo() {
        return this.upload(this.uppyMainVideo);
    }

    disableSaveButton() {
        if (this.saveButton) {
            this.saveButton.disabled = true;
        }
    }

    enableSaveButton() {
        if (this.saveButton) {
            this.saveButton.disabled = false;
        }
    }

    async submitForm(ev) {
        ev.preventDefault();
        this.disableSaveButton();
        if (this.previewVideoIsValid()) {
            let previewVideoUploadResult = null;
            try {
                previewVideoUploadResult = await this.uploadPreviewVideo();
                const previewUploadURL = TusVideo.extractURL(previewVideoUploadResult[0].uploadURL);
                const previewUploadName = previewVideoUploadResult[0].name;
                const previewHiddenVideoName = document.getElementById("video_preview_name");
                previewHiddenVideoName.value = previewUploadName;
                const previewHiddenVideoUploadUser = document.getElementById("video_preview_upload_url");
                previewHiddenVideoUploadUser.value = previewUploadURL;
            } catch (err) {
                console.error("Error uploading video", previewVideoUploadResult);
                this.enableSaveButton();
            }
        }
        if (this.mainVideoIsValid()) {
            let mainVideoUploadResult = null;
            try {
                mainVideoUploadResult = await this.uploadMainVideo();
                const mainUploadURL = TusVideo.extractURL(mainVideoUploadResult[0].uploadURL);
                const mainUploadName = mainVideoUploadResult[0].name;
                const mainHiddenVideoName = document.getElementById("video_name");
                mainHiddenVideoName.value = mainUploadName;
                const mainHiddenVideoUploadUser = document.getElementById("video_upload_url");
                mainHiddenVideoUploadUser.value = mainUploadURL;
            } catch (err) {
                console.error("Error uploading video", mainVideoUploadResult);
                this.enableSaveButton();
            }
        }
        const outputData = await this.editor.save();
        const descriptionInput = document.querySelector("input[name='description']");
        descriptionInput.value = JSON.stringify(outputData.blocks);
        this.form.submit();
        this.enableSaveButton();
    }

    setStatusPublished() {
        try {
            document.getElementById("status").value = "PUBLISHED";
        } catch (ex) {
            console.error(ex);
        }
    }

    setup() {
        this.editor = createEditor({
            holderId: this.editorHolderId,
            placeholder: this.editorPlaceholder,
            data: this.editorData
        });
        this.form.addEventListener("submit", this.submitForm.bind(this));

        if (this.publishButton !== null) {
            this.publishButton.addEventListener("click", ev => {
                ev.preventDefault();
                this.setStatusPublished();
                this.submitForm(ev);
                return false;
            });
        }
    }
}

export {
    CreateForm
}
