import { CreateForm } from '../libs/form/createForm.js';
import TusConfig from '../libs/tus/tusConfig.js';

; (() => {
    // const modelCreateForm = new modelCreateForm({
    //     form: document.getElementById("model-create-form"),
    //     editorHolderId: "description"
    // });

    // modelCreateForm.setup();
    // window.modelCreateForm = modelCreateForm;
    const tusToken = document.getElementById("tus_token").value;
    const tusEndpoint = document.getElementById("storage_upload_endpoint").value;
    const tusConfig = new TusConfig({
        tusEndpoint,
        tusToken
    });
    let editorData = {};
    let videos = new Set();
    const form = document.getElementById("model-form");
    const tusCreateForm = new CreateForm({
        form,
        tusConfig,
        dropAreaIdMain: "#drag-drop-area",
        dropAreaIdPreview: "#drag-drop-area-preview",
        editorData,
    });
    tusCreateForm.uppyPreviewVideo && tusCreateForm.uppyPreviewVideo.on("file-added", () => {
        videos.add("preview");
    });
    tusCreateForm.uppyMainVideo && tusCreateForm.uppyMainVideo.on("file-added", () => {
        videos.add("main");
    });
    tusCreateForm.uppyPreviewVideo && tusCreateForm.uppyPreviewVideo.on("file-removed", () => {
        videos.delete("preview");
    });
    tusCreateForm.uppyMainVideo && tusCreateForm.uppyMainVideo.on("file-removed", () => {
        videos.delete("main");
    });
    tusCreateForm.uppyMainVideo.on("error", (error) => {
        console.error("Video upload error:", error);
    });
    tusCreateForm.setup();
    window.tusCreateForm = tusCreateForm;
})();
