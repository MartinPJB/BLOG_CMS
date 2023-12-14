/**
 * Just a simple script to delete an image and display back the form to upload a new one
 * Author: Martin B.
 * Date: 12/13/2023
 */
// Functions
function deleteMedia() {
  return new Promise(async (resolve, reject) => {
    const mediaId = deleteImageButton.dataset.id;

    if (!mediaId) {
      return reject(new Error("No ID has been defined, can't delete an unknown media."));
    }

    try {
      const response = await fetch(`admin/delete_media/${mediaId}&json`, {
        method: "POST",
      });

      const json = await response.json();
      console.log(json);

      if (!response.ok) {
        return reject(new Error("An error occured while deleting the media"));
      }

      return resolve();
    } catch(e) {
      return reject(e);
    }
  });
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

async function clickHandler() {
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  });

  if (result.isConfirmed) {
    try {
      await deleteMedia();
      await Swal.fire({
        title: "Deleted!",
        text: "Your file has been deleted.",
        icon: "success",
      });

      recreateForm();
    } catch (e) {
      Swal.fire({
        title: "Error",
        text: e.message,
        icon: "error",
      });
    }
  }
}

const deleteImageButton = document.getElementById("cuej__delete_media");
if (!deleteImageButton) console.warn("No delete image button found");
else {
  // Event listeners
  deleteImageButton.addEventListener("click", clickHandler);
}
