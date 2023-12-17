// Import modules
import {
  swal_confirmAction,
  swal_success,
  swal_error,
} from "../../modules/swal.js";

/**
 * Library for handling media unassignment.
 * @namespace
 */
const library_unassign = {
  /**
   * API endpoint for unassigning media.
   * @constant {string}
   */
  ENDPOINT: "admin/unassign_media",

  /**
   * Handles the unassign media function.
   * @async
   * @function
   * @param {Event} e - The event object.
   * @param {Event} unassignEvent - The custom event to trigger when media is unassigned.
   * @throws {Error} - Throws an error if an issue occurs while unassigning the media.
   * @returns {Promise<void>}
   */
  async unassignMedia(e, unassignEvent) {
    try {
      // Confirm the unassign action
      if (!(await swal_confirmAction({
        title: "Are you sure you want to replace this media?",
        text: "The file won't be deleted from the server.",
        icon: "warning",
        confirmButtonText: "Yes, I'm sure",
      }))) {
        return;
      }

      // Extract data from the button's dataset
      const id = e.target.dataset.id;
      const table = e.target.dataset.table;
      const column = e.target.dataset.column;
      const lineId = e.target.dataset.lineid;

      // Validate data
      if (!id || !table || !column || !lineId) {
        throw new Error("Not enough data to replace the media.");
      }

      // Create a FormData object and append necessary data
      const formData = new FormData();
      formData.append("table", table);
      formData.append("column", column);
      formData.append("id", lineId);

      // Send a request to unassign the media
      const response = await fetch(`${this.ENDPOINT}/${id}&json`, {
        method: "POST",
        body: formData,
      });

      const json = await response.json();
      if (!response.ok) {
        throw new Error("An error occurred while replacing the media");
      }

      // Handle success or error response
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

        // Reset the form with a new file input
        const form = document.getElementById("cuej__media");
        const label = document.createElement("label");
        const input = document.createElement("input");

        label.innerHTML = "Select an image";
        input.type = "file";
        input.name = "image";

        form.innerHTML = "";
        form.appendChild(label);
        form.appendChild(input);

        // Dispatch the unassign event
        document.dispatchEvent(unassignEvent);
      }
    } catch (error) {
      console.error(error.message);
    }
  },

  /**
   * Assigns the unassign media function to the unassigned buttons.
   * @function
   */
  assignButtons() {
    const unassignedImageButtons = document.querySelectorAll(
      ".cuej__unassign_media.btn"
    );

    const unassignEvent = this.unassignEvent();
    for (const button of unassignedImageButtons) {
      button.addEventListener("click", (e) => {
        this.unassignMedia(e, unassignEvent);
      });
    }
  },

  /**
   * Creates a new event to trigger when the unassign media function is called, to detect when the media is unassigned.
   * @function
   * @returns {Event}
   */
  unassignEvent() {
    return new Event("media_unassigned");
  },
};

export default library_unassign;
