// Import modules
import {
  swal_confirmAction,
  swal_success,
  swal_error,
} from "../../modules/swal.js";

/**
 * Library for handling media deletion.
 * @namespace
 */
const library_delete = {
  /**
   * API endpoint for deleting media.
   * @constant {string}
   */
  ENDPOINT: "admin/delete_media",

  /**
   * Handles the delete media function.
   * @async
   * @function
   * @param {Event} e - The event object.
   * @throws {Error} - Throws an error if an issue occurs while deleting the media.
   */
  async deleteMedia(e) {
    try {
      // Confirm the delete action
      if (!(await swal_confirmAction({
        title: "Are you sure you want to delete this media?",
        text: "This action cannot be undone. It will be deleted from the library and the server.",
        icon: "warning",
        confirmButtonText: "Yes, delete it!",
      }))) {
        return;
      }

      const id = e.target.dataset.id;

      // Send a request to delete the media
      const response = await fetch(`${this.ENDPOINT}/${id}&json`, {
        method: "POST",
      });

      const json = await response.json();
      if (!response.ok) {
        throw new Error("An error occurred while deleting the media");
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
          text: "Your file has been deleted.",
          icon: "success",
        });
        // Remove the media container from the DOM
        e.target.closest(".blog-media").remove();
      }
    } catch (error) {
      console.error(error.message);
    }
  },

  /**
   * Assigns the delete media function to the delete buttons.
   * @function
   */
  assignButtons() {
    const deleteImageButtons = document.querySelectorAll(
      ".blog__delete_media.btn"
    );

    for (const button of deleteImageButtons) {
      button.addEventListener("click", this.deleteMedia.bind(this));
    }
  },
};

export default library_delete;
