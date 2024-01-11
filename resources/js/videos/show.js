import { createEditor } from '../libs/editor.js';

document.addEventListener("DOMContentLoaded", () => {
    createEditor({
        holderId: "description",
        readOnly: true,
        data: {
            blocks: JSON.parse(document.getElementById("source_description").innerText.trim())
        }
    });
});