import { CreateForm } from "../libs/form/createCarModelForm.js";
import TusConfig from "../libs/tus/tusConfig.js";

(() => {
  // const programCreateForm = new ProgramCreateForm({
  //     form: document.getElementById("program-create-form"),
  //     editorHolderId: "description"
  // });

  // programCreateForm.setup();
  // window.programCreateForm = programCreateForm;
  const tusToken = document.getElementById("tus_token").value;
  const preExistingSelect = document.getElementById("pre_existing_video_id");
  const tusEndpoint = document.getElementById("storage_upload_endpoint").value;
  const tusConfig = new TusConfig({
    tusEndpoint,
    tusToken,
  });
  let editorData = {};
  let videos = new Set();
  const form = document.getElementById("model-form");
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
  tusCreateForm.uppyPreviewVideo &&
    tusCreateForm.uppyPreviewVideo.on("file-removed", () => {
      videos.delete("preview");
      if (videos.size === 0) {
        form.querySelector("#publish-button").disabled = false;
      }
    });
  preExistingSelect.addEventListener("input", function () {
    var enteredValue = preExistingSelect.value;
    var options = document.querySelectorAll("#pre_existing_videos option");
    var selectedOption = Array.from(options).find(function (option) {
      return option.value === enteredValue;
    });
    if (selectedOption) {
      var dataUrl = selectedOption.getAttribute("data-url");
      var dataId = selectedOption.getAttribute("data-id");
      tusCreateForm.handlePreExistingVideoSelection(dataUrl, dataId);
    } else {
      console.log("No matching option found");
    }
  });
  tusCreateForm.setup();
  window.tusCreateForm = tusCreateForm;
})();
