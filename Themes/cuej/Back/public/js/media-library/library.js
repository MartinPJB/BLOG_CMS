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