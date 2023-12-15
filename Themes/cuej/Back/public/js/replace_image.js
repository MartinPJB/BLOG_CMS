/**
 * Just a simple script to delete an image and display back the form to upload a new one
 * Author: Martin B.
 * Date: 12/13/2023
 */

// Constants
const MEDIA_ENDPOINT = "admin/unassign_media";

// Functions
async function deleteMedia() {
  const mediaId = deleteImageButton.dataset.id;
  const table = deleteImageButton.dataset.table;
  const column = deleteImageButton.dataset.column;
  const lineId = deleteImageButton.dataset.lineid;

  if (!mediaId || !table || !column || !lineId) {
    return Promise.reject(new Error("Not enough data to delete the media."));
  }

  const formData = new FormData();
  formData.append("table", table);
  formData.append("column", column);
  formData.append("id", lineId);

  const response = await fetch(`${MEDIA_ENDPOINT}/${mediaId}&json`, {
    method: "POST",
    body: formData,
  });
  const json = await response.json();
  if (!response.ok) {
    throw new Error("An error occurred while replacing the media");
  }
  console.log(json);
}

function recreateForm() {
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

async function confirmDelete() {
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "This will not delete the image from the server, only from this page.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  });

  return result.isConfirmed;
}

async function handleDelete() {
  try {
    const shouldDelete = await confirmDelete();

    if (shouldDelete) {
      await deleteMedia();
      await Swal.fire({
        title: "Done!",
        text: "Your file has been replaced.",
        icon: "success",
      });
      recreateForm();
    }
  } catch (e) {
    Swal.fire({
      title: "Error",
      text: e.message,
      icon: "error",
    });
  }
}

// Main
const deleteImageButton = document.getElementById("cuej__delete_media");
if (!deleteImageButton) {
  console.warn("No delete image button found");
} else {
  // Event listeners
  deleteImageButton.addEventListener("click", handleDelete);
}
