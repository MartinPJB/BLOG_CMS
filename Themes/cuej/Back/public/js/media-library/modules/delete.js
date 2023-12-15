// Import modules
import {
  swal_confirmAction,
  swal_success,
  swal_error,
} from "../../modules/swal.js";

// Object containing the delete media function
const ENDPOINT = "admin/delete_media";
const library_delete = {
  // Handle the delete media function
  async deleteMedia(e) {
    if (
      !(await swal_confirmAction({
        title: "Are you sure you want to delete this media?",
        text: "This action cannot be undone. It will be deleted from the library and the server.",
        icon: "warning",
        confirmButtonText: "Yes, delete it!",
      }))
    )
      return;

    const id = e.target.dataset.id;
    const response = await fetch(`${ENDPOINT}/${id}&json`, {
      method: "POST",
    });

    const json = await response.json();
    if (!response.ok) {
      throw new Error("An error occurred while deleting the media");
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
        text: "Your file has been deleted.",
        icon: "success",
      });
      e.target.closest(".cuej__media").remove();
    }
  },

  // Assign the delete media function to the delete buttons
  assignButtons() {
    const deleteImageButtons = document.querySelectorAll(
      ".cuej__delete_media.btn"
    );

    for (const button of deleteImageButtons) {
      button.addEventListener("click", this.deleteMedia);
    }
  },
};

export default library_delete;
