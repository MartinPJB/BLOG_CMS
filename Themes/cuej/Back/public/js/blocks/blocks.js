// Import methods
import block_create from "./modules/add_block.js";

// Initializations
block_create.assignButtons();

// Delete all script tags in the body
const scripts = document.querySelectorAll("script");
for (const script of scripts) {
  script.remove();
}