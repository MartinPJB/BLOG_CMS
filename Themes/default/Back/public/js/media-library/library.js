// Import methods
import library_delete from "./modules/delete.js";
import library_unassign from "./modules/unassign.js";
import library_choose_existing from "./modules/choose_existing.js";
import { wait } from "../modules/functions.js";

// Allows to uncheck radio buttons in the media library div
async function detectMediaLibrary() {
  let mediaArea;
  const blog__media = document.querySelector("#blog-media");
  let tries = 5;

  while ((!mediaArea && blog__media) && tries > 0) {
    mediaArea = document.querySelector(".blog-media__choose-existing");
    await wait(500);
    tries--;
  }

  if (mediaArea) {
    const radioLabels = mediaArea.querySelectorAll("label");
    const inputFile = blog__media.querySelector("input[type=file]");

    for (const label of radioLabels) {
      const input = document.getElementById(label.htmlFor);
      if (!input) continue;

      label.addEventListener("click", (e) => {
        if (input.checked) {
          e.preventDefault();
          input.checked = false;
          inputFile.removeAttribute("disabled");
          return;
        }

        inputFile.setAttribute("disabled", true);
      });
    }
  }
}

// Initializations
library_delete.assignButtons();
library_unassign.assignButtons();

document.addEventListener("show_choices", () => {
  library_choose_existing.init();
  detectMediaLibrary();
});
library_choose_existing.init();
detectMediaLibrary();