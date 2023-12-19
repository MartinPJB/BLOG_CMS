// Import methods
import library_delete from "./modules/delete.js";
import library_unassign from "./modules/unassign.js";
import library_choose_existing from "./modules/choose_existing.js";

// Initializations
library_delete.assignButtons();
library_unassign.assignButtons();

document.addEventListener("media_unassigned", () => {
  library_choose_existing.init();
});
library_choose_existing.init();

// Allows to uncheck radio buttons in the media library div
function wait(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

(async () => {
  let mediaArea;
  const cuej__media = document.querySelector("#cuej__media");

  while (!mediaArea && cuej__media) {
    mediaArea = document.querySelector(".cuej__media__choose_existing");
    await wait(500);
  }

  if (mediaArea) {
    const radioLabels = mediaArea.querySelectorAll("label");
    for (const label of radioLabels) {
      const input = document.getElementById(label.htmlFor);
      if (!input) continue;

      label.addEventListener("click", (e) => {
        if (input.checked) {
          input.checked = false;
          console.log(input.checked);
        }
      });
    }
  }
})();