// Import modules
import {
  swal_success,
  swal_error,
} from "../../modules/swal.js";

/**
 * Handles the block order functionality.
 * @namespace
 */
const order_block = {
  /**
   * Handles the block order save function.
   * @async
   * @function
   */
  async saveBlockOrder() {
    try {
      const blocksOrder = [];
      const blocks = document.querySelectorAll(".blocks-draggable__item");

      for (const block of blocks) {
        blocksOrder.push(block.dataset.block);
      }

      const formData = new FormData();
      formData.append("blocksOrder", JSON.stringify(blocksOrder));

      const response = await fetch("admin/change_block_order", {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (data.success) {
        swal_success({
          title: "Success",
          text: "The blocks' orders have been saved.",
          icon: "success",
        });
      } else {
        swal_error({
          title: "Error",
          text: `An error occurred while saving the blocks' orders: ${data.error}`,
          icon: "error",
        });
      }
      document.getElementById("blocksOrderSave").disabled = true;
    } catch (error) {
      console.error(error.message);
    }
  },

  /**
   * Assigns the save block order function to the save button.
   * @function
   */
  assignButton() {
    const blocksOrderSave = document.getElementById("blocksOrderSave");

    if (blocksOrderSave) {
      blocksOrderSave.addEventListener("click", this.saveBlockOrder);
    }
  }
};

export default order_block;
