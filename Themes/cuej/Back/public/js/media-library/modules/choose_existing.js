/**
 * Library for choosing an existing media.
 * @namespace
 */
const library_choose_existing = {
  /**
   * API endpoint for getting all medias.
   * @constant {string}
   */
  ENDPOINT: "admin/get_all_medias",

  /**
   * Gets all medias from the server.
   * @async
   * @function
   * @returns {Promise<Object>} - A promise that resolves to an object containing medias.
   * @throws {Error} - Throws an error if an issue occurs while fetching medias.
   */
  async getAllMedias() {
    const response = await fetch(`${this.ENDPOINT}`);
    const json = await response.json();
    if (!response.ok) {
      throw new Error("An error occurred while getting all medias");
    }
    return json;
  },

  /**
   * Initializes the media library.
   * @async
   * @function
   * @returns {Promise<void>} - A promise that resolves when the library is initialized.
   */
  async init() {
    const mediaArea = document.getElementById("cuej-media");
    if (!mediaArea) return;

    let type = mediaArea.dataset.type || "all";

    // Check if an input file is already present in the media area, if not return
    const inputFile = mediaArea.querySelector("input[type=file]");
    if (!inputFile) return;

    // Create and append the "Choose an existing media" details element
    const chooseExisting = document.createElement("details");
    chooseExisting.classList.add("cuej-media__choose-existing");

    const chooseExistingSummary = document.createElement("summary");
    chooseExistingSummary.innerText = "Choose an existing media";
    chooseExisting.appendChild(chooseExistingSummary);

    const flexDiv = document.createElement("div");
    flexDiv.classList.add("flex", "flex-wrap", "flex-gap");
    chooseExisting.appendChild(flexDiv);

    // Gets all the medias in the library and filter them by type if needed
    // The type looks like this in the HTML: <element data-type="jpg, jpeg, png, gif"></element>
    const allMedias = await this.getAllMedias();
    if (type !== "all") {
      allMedias.medias = allMedias.medias.filter((media) => {
        const extensionsAllowed = type.split(", ");
        const extension = media.type.split("/")[1];
        return extensionsAllowed.includes(extension);
      });
    }

    for (const media of allMedias.medias) {
      // Create a hidden input with the media id to choose
      const radio = document.createElement("input");
      radio.type = "radio";
      radio.name = "media_id";
      radio.value = media.id;
      radio.id = media.id;

      // Create a label with the media name
      const label = document.createElement("label");
      label.classList.add("cuej-media__choose-existing-label");

      // Get extension from mime type
      const extension = media.type.split("/")[1];
      label.innerHTML = "<span class='cuej-media__choose-existing-label-name'>" + media.name + "." + extension + "</span>";
      label.htmlFor = media.id;

      // Create a div with the media preview
      const preview = document.createElement("div");
      preview.classList.add("cuej-media__choose-existing-preview");

      // Handles media path (checks if it's local or not)
      const imagePath = media.path.includes("http") ? media.path : `../../../../../${media.path}`;

      // Set the background image of the preview div if its extension is an image.
      if (media.type.includes("image")) {
        preview.style.setProperty("--image", `url("${imagePath}")`);
      } else {
        preview.style.setProperty("--image", `url("../../../../../public/back/img/${extension}.png")`);
      }

      // Append all elements
      flexDiv.appendChild(radio);
      flexDiv.appendChild(label);
      label.appendChild(preview);
    }

    mediaArea.appendChild(chooseExisting);
    chooseExisting.open = true;
  },
};

export default library_choose_existing;
