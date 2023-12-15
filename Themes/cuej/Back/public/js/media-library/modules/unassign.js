// Import modules
import {
  swal_confirmAction,
  swal_success,
  swal_error,
} from "../../modules/swal.js";

// Object containing the unassign media function
const ENDPOINT = "admin/unassign_media";
const library_unassign = {
  // Handle the unassign media function
  async unassignMedia(e) {
    if (
      !(await swal_confirmAction({
        title: "Are you sure you want to replace this media?",
        text: "The file won't be deleted from the server.",
        icon: "warning",
        confirmButtonText: "Yes, I'm sure",
      }))
    )
      return;

    const id = e.target.dataset.id;
    const table = e.target.dataset.table;
    const column = e.target.dataset.column;
    const lineId = e.target.dataset.lineid;

    if (!id || !table || !column || !lineId) {
      return Promise.reject(new Error("Not enough data to replace the media."));
    }

    const formData = new FormData();
    formData.append("table", table);
    formData.append("column", column);
    formData.append("id", lineId);

    const response = await fetch(`${ENDPOINT}/${id}&json`, {
      method: "POST",
      body: formData,
    });

    const json = await response.json();
    if (!response.ok) {
      throw new Error("An error occurred while replacing the media");
    }

    if (json.error) {
      swal_error({
        title: "Error",
        text: json.error,
        icon: "error",
      });
    } else {
      swal_success({
        title: "Done!",
        text: "The image has been replaced!",
        icon: "success",
      });

      const form = document.getElementById("cuej__media");
      const label = document.createElement("label");
      const input = document.createElement("input");

      label.innerHTML = "Select an image";
      input.type = "file";
      input.name = "image";

      form.innerHTML = "";
      form.appendChild(label);
      form.appendChild(input);
    }
  },

  // Assign the unassign media function to the unassigned buttons
  assignButtons() {
    const unassignedImageButtons = document.querySelectorAll(
      ".cuej__unassign_media.btn"
    );

    for (const button of unassignedImageButtons) {
      console.log(button);
      button.addEventListener("click", this.unassignMedia);
    }
  },
};

export default library_unassign;
