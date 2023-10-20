import { createEditor } from "../libs/editor.js";

; (() => {
    let editorData = {};
    const descriptionData = document.getElementById("live_source_description").innerText.trim();
    if (descriptionData.length > 0) {
        editorData.blocks = JSON.parse(descriptionData);
    }
    const form = document.getElementById("live-form");
    const editor = createEditor({
        holderId: "description",
        //placeholder: this.editorPlaceholder,
        data: editorData
    });

    form.addEventListener("submit", async (ev) => {
        ev.preventDefault();
        const outputData = await editor.save();
        const descriptionInput = document.querySelector("input[name='description']");
        descriptionInput.value = JSON.stringify(outputData.blocks);
        form.submit();
    });
})();
