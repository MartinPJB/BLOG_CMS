const library_delete = {
  async deleteMedia(e) {
    const id = e.target.dataset.id;
    const response = await fetch(`admin/delete_media/${id}`, {
      method: "POST",
    });
    const json = await response.json();
    if (!response.ok) {
      throw new Error("An error occurred while replacing the media");
    }
    console.log(json);
  },

  assignButtons() {
    const deleteImageButton = document.querySelectorAll(".cuej__delete_media.btn");

    deleteImageButton.addEventListener("click", this.deleteMedia);
  }
}

export default library_delete;