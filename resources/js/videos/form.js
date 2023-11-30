import { CreateForm } from "../libs/form/createCarModelForm.js";
import TusConfig from "../libs/tus/tusConfig.js";

(() => {
  const tusToken = document.getElementById("tus_token").value;
  const tusEndpoint = document.getElementById("storage_upload_endpoint").value;
  const preExistingSelect = document.getElementById("pre_existing_video_id");
  const tusConfig = new TusConfig({
    tusEndpoint,
    tusToken,
  });
  let editorData = {};
  let videos = new Set();
  const form = document.getElementById("video-form");
  const tusCreateForm = new CreateForm({
    form,
    tusConfig,
    dropAreaIdMain: "#drag-drop-area",
    dropAreaIdPreview: "#drag-drop-area-preview",
    publishButton: document.getElementById("publish-button"),
    editorData,
  });
  tusCreateForm.uppyPreviewVideo &&
    tusCreateForm.uppyPreviewVideo.on("file-added", () => {
      videos.add("preview");
      form.querySelector("#publish-button").disabled = true;
    });
  tusCreateForm.uppyMainVideo &&
    tusCreateForm.uppyMainVideo.on("file-added", () => {
      videos.add("main");
      form.querySelector("#publish-button").disabled = true;
    });
  tusCreateForm.uppyPreviewVideo &&
    tusCreateForm.uppyPreviewVideo.on("file-removed", () => {
      videos.delete("preview");
      if (videos.size === 0) {
        form.querySelector("#publish-button").disabled = false;
      }
    });
  tusCreateForm.uppyMainVideo &&
    tusCreateForm.uppyMainVideo.on("file-removed", () => {
      videos.delete("main");
      if (videos.size === 0) {
        form.querySelector("#publish-button").disabled = false;
      }
    });
  preExistingSelect.addEventListener("input", async function () {
    var enteredValue = preExistingSelect.value;
    var options = document.querySelectorAll("#pre_existing_videos option");
    var selectedOption = Array.from(options).find(function (option) {
      return option.value === enteredValue;
    });
    if (selectedOption) {
      var dataUrl = selectedOption.getAttribute("data-url");
      var dataId = selectedOption.getAttribute("data-id");
      await tusCreateForm.handlePreExistingVideoSelection(dataUrl, dataId);
    } else {
      console.log("No matching option found");
    }
  });
  tusCreateForm.setup();
  window.tusCreateForm = tusCreateForm;
})();
